<?php
/**
 * Handle collected vendor orders.
 */

namespace App\Http\Controllers\Vendor;


use App\Contracts\Shop\Delivery\DeliveryStatusInterface;
use App\Contracts\Shop\Invoices\Handlers\InvoiceHandlerInterface;
use App\Events\Invoices\UserOrderCancelled;
use App\Events\Invoices\UserOrderCollected;
use App\Events\Invoices\UserOrderPartiallyCollected;
use App\Http\Support\Invoices\Fabrics\UserOrderInvoiceFabric;
use App\Http\Support\Invoices\Fabrics\VendorOrderInvoiceFabric;
use App\Http\Support\Invoices\Handlers\Product\VendorStorageProductInvoiceHandler;
use App\Http\Support\Invoices\RelatedInvoices\RelatedInvoicesHandler;
use App\Http\Support\Shipment\VendorShipmentDispatcher;
use Exception;
use Illuminate\Http\Request;

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
     * @var RelatedInvoicesHandler
     */
    private $relatedInvoicesHandler;

    /**
     * AccountController constructor.
     * @param Request $request
     * @param VendorOrderInvoiceFabric $vendorOrderInvoiceFabric
     * @param UserOrderInvoiceFabric $userOrderInvoiceFabric
     * @param VendorShipmentDispatcher $shipmentDispatcher
     * @param RelatedInvoicesHandler $relatedInvoicesHandler
     */
    public function __construct(Request $request, VendorOrderInvoiceFabric $vendorOrderInvoiceFabric, UserOrderInvoiceFabric $userOrderInvoiceFabric, VendorShipmentDispatcher $shipmentDispatcher, RelatedInvoicesHandler $relatedInvoicesHandler)
    {
        $this->request = $request;
        $this->vendorOrderInvoiceFabric = $vendorOrderInvoiceFabric;
        $this->shipmentDispatcher = $shipmentDispatcher;
        $this->userOrderInvoiceFabric = $userOrderInvoiceFabric;
        $this->relatedInvoicesHandler = $relatedInvoicesHandler;
    }


    /**
     * Collect invoice, add it to shipment, notify user.
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function collect()
    {
        // get handler with invoice
        if ($this->request->has('invoice_id')) {
            $vendorInvoiceHandler = $this->getVendorInvoiceHandler($this->request->get('invoice_id'));
        } else {
            throw new Exception('Collecting invoice is not defined');
        }

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
     * Collect all products in all invoices, add it to shipment, notify users.
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function collectAll()
    {
        if ($this->request->has('invoices_id')) {
            // iterate each collecting invoice
            foreach ($this->request->get('invoices_id') as $invoiceId) {
                // get handler with invoice
                $vendorInvoiceHandler = $this->getVendorInvoiceHandler((int)$invoiceId);
                // invoice was collected
                $this->invoiceCollected($vendorInvoiceHandler);
            }
        } else {
            throw new Exception('Collecting invoices is not defined');
        }

        return back();
    }

    /**
     * Retrieve invoice. Bind it to handler.
     *
     * @param int $invoiceId
     * @return VendorStorageProductInvoiceHandler
     * @throws Exception
     */
    private function getVendorInvoiceHandler(int $invoiceId): VendorStorageProductInvoiceHandler
    {
        $invoiceRepository = $this->vendorOrderInvoiceFabric->getRepository();
        $invoiceHandler = $this->vendorOrderInvoiceFabric->getHandler();

        // retrieve collecting invoice
        $collectingInvoice = $invoiceRepository->getByInvoiceId($invoiceId);

        if ($collectingInvoice) {
            // return invoice handler with bound invoice
            return $invoiceHandler->bindInvoice($collectingInvoice);
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
        $vendorInvoiceHandler->implementVendorInvoice();

        // add invoice to shipment
        $this->addInvoiceToShipment($vendorInvoiceHandler);

        // define related user invoice.
        $relatedUserInvoice = $this->relatedInvoicesHandler->getRelatedUserInvoice($vendorInvoiceHandler->getInvoice());

        if ($relatedUserInvoice) {

            // get user invoice handler
            $userInvoiceHandler = $this->userOrderInvoiceFabric->getHandler()->bindInvoice($relatedUserInvoice);

            // get related vendor invoices
            $relatedVendorInvoices = $this->relatedInvoicesHandler->getRelatedVendorInvoicesByUserInvoice($relatedUserInvoice);

            if ($this->relatedInvoicesHandler->areRelatedVendorInvoicesImplemented($relatedVendorInvoices)) {

                // define new delivery date
                $userInvoiceDeliveryDate = $this->shipmentDispatcher->calculateDeliveryDateByInvoices($relatedVendorInvoices->pluck('id')->toArray());

                // change delivery date
                $userInvoiceHandler->updateDeliveryDate($userInvoiceDeliveryDate);

                // change delivery status
                $userInvoiceHandler->updateDeliveryStatus(DeliveryStatusInterface::COLLECTED);

                // fire event
                event(new UserOrderCollected($relatedUserInvoice));
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
        $vendorInvoiceHandler->implementVendorInvoice();

        // add invoice to shipment
        $this->addInvoiceToShipment($vendorInvoiceHandler);

        // correct user invoice quantity, set delivery data, send notification
        if (config('shop.invoice.pre_order.auto_correct_user_invoice')) {

            // define related user invoice.
            $relatedUserInvoice = $this->relatedInvoicesHandler->getRelatedUserInvoice($vendorInvoiceHandler->getInvoice());

            if ($relatedUserInvoice) {
                // get user invoice handler
                $userInvoiceHandler = $this->userOrderInvoiceFabric->getHandler()->bindInvoice($relatedUserInvoice);

                // update user invoice products quantity
                $this->updateInvoiceProductQuantity($userInvoiceHandler, $collectedProducts);

                // get related vendor invoices
                $relatedVendorInvoices = $this->relatedInvoicesHandler->getRelatedVendorInvoicesByUserInvoice($relatedUserInvoice);

                if ($this->relatedInvoicesHandler->areRelatedVendorInvoicesImplemented($relatedVendorInvoices)) {

                    // define new delivery date
                    $userInvoiceDeliveryDate = $this->shipmentDispatcher->calculateDeliveryDateByInvoices($relatedVendorInvoices->pluck('id')->toArray());

                    // change delivery date
                    $userInvoiceHandler->updateDeliveryDate($userInvoiceDeliveryDate);

                    // change delivery status
                    $userInvoiceHandler->updateDeliveryStatus(DeliveryStatusInterface::COLLECTED);

                    // fire event
                    event(new UserOrderPartiallyCollected($relatedUserInvoice));
                }
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
        $vendorInvoiceHandler->cancelInvoice();

        // cancel user invoice, send notification
        if (config('shop.invoice.pre_order.auto_correct_user_invoice')) {
            // define related user invoice.
            $relatedUserInvoice = $this->relatedInvoicesHandler->getRelatedUserInvoice($vendorInvoiceHandler->getInvoice());

            if ($relatedUserInvoice) {
                // get user invoice handler
                $userInvoiceHandler = $this->userOrderInvoiceFabric->getHandler()->bindInvoice($relatedUserInvoice);

                // cancel invoice
                $userInvoiceHandler->cancelInvoice();

                // fire event
                event(new UserOrderCancelled($relatedUserInvoice));
            }
        }
    }

    /**
     * Get nearest shipment. Add invoice to it.
     *
     * @param VendorStorageProductInvoiceHandler $invoiceHandler
     * @return bool
     */
    private function addInvoiceToShipment(VendorStorageProductInvoiceHandler $invoiceHandler)
    {
        if (!config('shop.invoice.pre_order.auto_add_to_nearest_shipment')) {
            return false;
        }
        // get nearest shipment
        $nearestShipment = $this->shipmentDispatcher->getNextShipment($invoiceHandler->getVendorId());

        return $nearestShipment ? $invoiceHandler->bindInvoiceToShipment($nearestShipment->id) : false;
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
                $invoiceHandler->removeProduct($invoiceProduct->products_id, $decreasingQuantity);
            }
        }
    }
}