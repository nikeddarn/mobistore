<?php
/**
 * Handle collected vendor orders.
 */

namespace App\Http\Controllers\Vendor;


use App\Contracts\Shop\Delivery\DeliveryStatusInterface;
use App\Contracts\Shop\Invoices\Handlers\InvoiceHandlerInterface;
use App\Contracts\Shop\Invoices\InvoiceStatusInterface;
use App\Events\Invoices\UserOrderCancelled;
use App\Events\Invoices\UserOrderCollected;
use App\Events\Invoices\UserOrderPartiallyCollected;
use App\Http\Support\Invoices\Fabrics\UserOrderInvoiceFabric;
use App\Http\Support\Invoices\Fabrics\VendorOrderInvoiceFabric;
use App\Http\Support\Invoices\Handlers\UserStorageProductInvoiceHandler;
use App\Http\Support\Invoices\Handlers\VendorStorageProductInvoiceHandler;
use App\Http\Support\Shipment\VendorShipmentDispatcher;
use App\Models\Shipment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class VendorCollectOrderController
{
    /**
     * @var Request
     */
    private $request;

    /**
     * @var VendorOrderInvoiceFabric
     */
    private $vendorOrderInvoiceFabric;

    /**
     * @var VendorShipmentDispatcher
     */
    private $shipmentDispatcher;

    /**
     * @var UserOrderInvoiceFabric
     */
    private $userOrderInvoiceFabric;

    /**
     * AccountController constructor.
     * @param Request $request
     * @param VendorOrderInvoiceFabric $vendorOrderInvoiceFabric
     * @param UserOrderInvoiceFabric $userOrderInvoiceFabric
     * @param VendorShipmentDispatcher $shipmentDispatcher
     */
    public function __construct(Request $request, VendorOrderInvoiceFabric $vendorOrderInvoiceFabric, UserOrderInvoiceFabric $userOrderInvoiceFabric, VendorShipmentDispatcher $shipmentDispatcher)
    {
        $this->request = $request;
        $this->vendorOrderInvoiceFabric = $vendorOrderInvoiceFabric;
        $this->shipmentDispatcher = $shipmentDispatcher;
        $this->userOrderInvoiceFabric = $userOrderInvoiceFabric;
    }


    /**
     * Collect invoice, add it to shipment, notify user.
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function collect()
    {
        // handling invoice
        $vendorInvoiceHandler = $this->getVendorInvoiceHandler();

        // array of collected products count by InvoiceProduct id
        $collectedProductsCountByInvoiceProductId = $this->request->get('quantity');

        // ordered products quantity
        $orderedProductsQuantity = array_sum($vendorInvoiceHandler->getArrayInvoiceProducts());
        // collected products quantity
        $collectedProductQuantity = array_sum($this->request->get('quantity'));


        if ($collectedProductQuantity === $orderedProductsQuantity) {
            $this->invoiceCollected($vendorInvoiceHandler);
        } elseif ($collectedProductQuantity === 0) {
            $this->invoiceCancelled($vendorInvoiceHandler);
        } else {
            $this->invoicePartiallyCollected($vendorInvoiceHandler, $collectedProductsCountByInvoiceProductId);
        }

        return back();
    }

    /**
     * Retrieve invoice. Bind it to handler.
     *
     * @return VendorStorageProductInvoiceHandler
     * @throws Exception
     */
    private function getVendorInvoiceHandler(): VendorStorageProductInvoiceHandler
    {
        if ($this->request->has('invoice_id')) {
            $invoiceRepository = $this->vendorOrderInvoiceFabric->getRepository();
            $invoiceHandler = $this->vendorOrderInvoiceFabric->getHandler();

            // retrieve collecting invoice
            $collectingInvoice = $invoiceRepository->getByInvoiceId($this->request->get('invoice_id'));

            if ($collectingInvoice) {
                // return invoice handler with bound invoice
                return $invoiceHandler->bindInvoice($collectingInvoice);
            } else {
                throw new Exception('Collecting invoice is not defined');
            }
        } else {
            throw new Exception('Collecting invoice is not defined');
        }
    }

    /**
     * Order fully collected.
     *
     * @param VendorStorageProductInvoiceHandler $vendorInvoiceHandler
     */
    private function invoiceCollected(VendorStorageProductInvoiceHandler $vendorInvoiceHandler)
    {
        // mark vendor invoice as implemented
        $vendorInvoiceHandler->setVendorInvoiceImplemented();

        // add to shipment
        $shipment = $this->addInvoiceToShipment($vendorInvoiceHandler);

        // define related user invoice.
        $relatedUserInvoice = $vendorInvoiceHandler->getRelatedUserInvoice();

        if ($relatedUserInvoice) {
            // get user invoice handler
            $userInvoiceHandler = $this->userOrderInvoiceFabric->getHandler()->bindInvoice($relatedUserInvoice);

            // get related vendor invoices
            $relatedVendorInvoices = $userInvoiceHandler->getRelatedVendorInvoices();

            // all vendor invoices of user invoice are collected
            if ($this->areAllRelatedVendorInvoicesCollected($relatedVendorInvoices)) {
                // change related user invoice delivery status
                $userInvoiceHandler->setDeliveryStatus(DeliveryStatusInterface::COLLECTED);

                // update user invoice delivery date
                if ($shipment) {
                    $this->updateUserInvoiceDeliveryDate($userInvoiceHandler, $relatedVendorInvoices);
                }

                // fire event
                event(new UserOrderCollected($vendorInvoiceHandler->getInvoice()));
            }
        }
    }

    /**
     * Some products don't present on vendor store. Partially collected invoice.
     *
     * @param VendorStorageProductInvoiceHandler $vendorInvoiceHandler
     * @param array $collectedProducts
     */
    private function invoicePartiallyCollected(VendorStorageProductInvoiceHandler $vendorInvoiceHandler, array $collectedProducts)
    {
        // update vendor invoice products quantity
        $this->updateInvoiceProductQuantity($vendorInvoiceHandler, $collectedProducts);

        // mark vendor invoice as implemented
        $vendorInvoiceHandler->setVendorInvoiceImplemented();

        // add to shipment
        $shipment = $this->addInvoiceToShipment($vendorInvoiceHandler);

        // define related user invoice.
        $relatedUserInvoice = $vendorInvoiceHandler->getRelatedUserInvoice();

        if ($relatedUserInvoice) {
            // get user invoice handler
            $userInvoiceHandler = $this->userOrderInvoiceFabric->getHandler()->bindInvoice($relatedUserInvoice);

            // update user invoice products quantity
            $this->updateInvoiceProductQuantity($userInvoiceHandler, $collectedProducts);

            // get related vendor invoices
            $relatedVendorInvoices = $userInvoiceHandler->getRelatedVendorInvoices();

            // all vendor invoices of user invoice are collected
            if ($this->areAllRelatedVendorInvoicesCollected($relatedVendorInvoices)) {
                // change related user invoice delivery status
                $userInvoiceHandler->setDeliveryStatus(DeliveryStatusInterface::COLLECTED);

                // update user invoice delivery date
                if ($shipment) {
                    $this->updateUserInvoiceDeliveryDate($userInvoiceHandler, $relatedVendorInvoices);
                }

                // fire event
                event(new UserOrderPartiallyCollected($vendorInvoiceHandler->getInvoice()));
            }
        }
    }

    /**
     * Products don't present on vendor store. Order cancelled.
     *
     * @param VendorStorageProductInvoiceHandler $vendorInvoiceHandler
     */
    private function invoiceCancelled(VendorStorageProductInvoiceHandler $vendorInvoiceHandler)
    {
        // set vendor invoice status as 'cancelled'
        $vendorInvoiceHandler->setInvoiceStatus(InvoiceStatusInterface::CANCELLED);

        // define related user invoice.
        $relatedUserInvoice = $vendorInvoiceHandler->getRelatedUserInvoice();

        if ($relatedUserInvoice) {
            // get user invoice handler
            $userInvoiceHandler = $this->userOrderInvoiceFabric->getHandler()->bindInvoice($relatedUserInvoice);

            // set user invoice status as 'cancelled'
            $userInvoiceHandler->setInvoiceStatus(InvoiceStatusInterface::CANCELLED);

            // change user invoice delivery status
            $userInvoiceHandler->setDeliveryStatus(DeliveryStatusInterface::CANCELLED);

            // remove reserve of invoice products from storage
            $userInvoiceHandler->removeReserveFromStorage();

            // fire event
            event(new UserOrderCancelled($vendorInvoiceHandler->getInvoice()));
        }
    }

    /**
     * Get nearest shipment. Add invoice to it.
     *
     * @param VendorStorageProductInvoiceHandler $invoiceHandler
     * @return Shipment|null
     */
    private function addInvoiceToShipment(VendorStorageProductInvoiceHandler $invoiceHandler)
    {
        // get nearest shipment
        $nearestShipment = $this->shipmentDispatcher->getNextShipment($invoiceHandler->getVendorId());

        // bind invoice to shipment
        if ($nearestShipment) {
            $invoiceHandler->bindInvoiceToShipment($nearestShipment->id);
        }

        return $nearestShipment;
    }

    /**
     * Are all the vendor invoices, of which the user invoice is composed, collected?
     *
     * @param Collection $relatedVendorInvoices
     * @return bool
     */
    private function areAllRelatedVendorInvoicesCollected(Collection $relatedVendorInvoices): bool
    {
        // all vendor invoices that related with user invoice are implemented
        return !$relatedVendorInvoices->where('implemented', 0)->count();
    }

    /**
     * Update user invoice delivery date.
     *
     * @param UserStorageProductInvoiceHandler $userInvoiceHandler
     * @param Collection $relatedVendorInvoices
     */
    private function updateUserInvoiceDeliveryDate(UserStorageProductInvoiceHandler $userInvoiceHandler, Collection $relatedVendorInvoices)
    {
        // related vendor invoices id
        $vendorInvoicesId = $relatedVendorInvoices->pluck('invoices_id')->toArray();
        // redefine planned arrival
        $plannedArrival = $this->shipmentDispatcher->calculateDeliveryDateByInvoices($vendorInvoicesId);

        // change delivery date
        if ($plannedArrival) {
            $userInvoiceHandler->updateDeliveryDate($plannedArrival);
        }
    }

    /**
     * Set really collected quantity of each invoice product.
     *
     * @param InvoiceHandlerInterface $invoiceHandler
     * @param array $collectedProducts
     */
    private function updateInvoiceProductQuantity(InvoiceHandlerInterface $invoiceHandler, array $collectedProducts)
    {
        foreach ($invoiceHandler->getInvoiceProducts() as $invoiceProduct) {
            // decreasing quantity
            $decreasingQuantity = $invoiceProduct->quantity - $collectedProducts[$invoiceProduct->products_id];

            // decrease product quantity
            if ($decreasingQuantity > 0) {
                $invoiceHandler->decreaseProductCount($invoiceProduct->products_id, $decreasingQuantity);
            }
        }
    }
}