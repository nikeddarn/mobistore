<?php

namespace App\Http\Controllers\Vendor;

use App\Contracts\Shop\Delivery\DeliveryStatusInterface;
use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Contracts\Shop\Invoices\InvoiceStatusInterface;
use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Contracts\Shop\Invoices\Repositories\InvoiceRepositoryConstraintsInterface;
use App\Events\Invoices\UserOrderCancelled;
use App\Events\Invoices\UserOrderCollected;
use App\Events\Invoices\UserOrderPartiallyCollected;
use App\Http\Support\Invoices\Handlers\StorageProductInvoiceHandler;
use App\Http\Support\Invoices\Repositories\User\VendorProductInvoiceRepository;
use App\Http\Support\Invoices\Repositories\Vendor\VendorInvoiceConstraints;
use App\Http\Support\Shipment\VendorShipmentDispatcher;
use App\Models\Invoice;
use App\Models\InvoiceProduct;
use App\Models\Shipment;
use App\Models\Vendor;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class VendorOrderController extends Controller
{
    /**
     * @var Vendor
     */
    private $vendor;

    /**
     * @var Vendor
     */
    private $retrievedVendor;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var VendorProductInvoiceRepository
     */
    private $invoiceRepository;

    /**
     * @var VendorShipmentDispatcher
     */
    private $shipmentDispatcher;

    /**
     * @var StorageProductInvoiceHandler
     */
    private $invoiceHandler;

    /**
     * AccountController constructor.
     * @param Request $request
     * @param Vendor $vendor
     * @param VendorProductInvoiceRepository $invoiceRepository
     * @param VendorShipmentDispatcher $shipmentDispatcher
     * @param StorageProductInvoiceHandler $invoiceHandler
     */
    public function __construct(
        Request $request,
        Vendor $vendor,
        VendorProductInvoiceRepository $invoiceRepository,
        VendorShipmentDispatcher $shipmentDispatcher,
        StorageProductInvoiceHandler $invoiceHandler
    )
    {
        $this->vendor = $vendor;
        $this->request = $request;
        $this->invoiceRepository = $invoiceRepository;
        $this->shipmentDispatcher = $shipmentDispatcher;
        $this->invoiceHandler = $invoiceHandler;
    }

    /**
     * Show user account items.
     *
     * @param int|null $vendorId
     * @return View
     * @throws Exception
     */
    public function index(int $vendorId)
    {
        $this->retrievedVendor = $this->vendor->where('id', $vendorId)->first();

        if (!$this->retrievedVendor) {
            throw new Exception('Vendor is not defined.');
        }

        $retrieveConstraints = (new VendorInvoiceConstraints())
            ->setVendorId($this->retrievedVendor->id)
            ->setInvoiceStatus(InvoiceStatusInterface::PROCESSING)
            ->setInvoiceType([
                InvoiceTypes::ORDER,
                InvoiceTypes::PRE_ORDER,
                InvoiceTypes::RETURN_ORDER
            ])
            ->setInvoiceDirection(InvoiceDirections::OUTGOING)
            ->setImplementedStatus(0)
            ->setPaginate(config('shop.account_items_show'));

        return view('content.vendor.order.index')->with([
            'outgoingOrders' => $this->getOutgoingOrders($retrieveConstraints),
            'outgoingProducts' => $this->getOutgoingProducts($retrieveConstraints),
            'vendorId' => $this->retrievedVendor->id,
        ]);
    }

    /**
     * Collect invoice, add it to shipment, notify user.
     *
     * @throws Exception
     */
    public function collect()
    {
        $invoice = $this->invoiceRepository->getByInvoiceId($this->request->get('invoice_id'));

        $collectedProduct = $this->request->get('quantity');
        $collectedProductQuantity = array_sum($this->request->get('quantity'));

        if ($invoice->invoiceProduct->count() === $collectedProductQuantity) {
            $this->invoiceCollected($invoice);
        } elseif ($collectedProductQuantity === 0) {
            $this->invoiceCancelled($invoice);
        } else {
            $this->invoicePartiallyCollected($invoice, $collectedProduct);
        }

        return back();
    }

    /**
     * Order fully collected.
     *
     * @param Invoice|Model $invoice
     * @throws Exception
     */
    private function invoiceCollected(Invoice $invoice)
    {
        // mark as implemented
        $this->markInvoiceAsImplemented($invoice);

        // add to shipment
        $shipment = $this->addInvoiceToShipment($invoice);

        // define related user invoice.
        $userInvoice = $invoice->vendorInvoice->load('userInvoice')->userInvoice;

        if ($userInvoice) {
            // change related user invoice status if exists
            $userInvoice->delivery_status_id = DeliveryStatusInterface::COLLECTED;
            $userInvoice->save();

            // change delivery date
            $userInvoice->userDelivery->planned_arrival = $shipment->planned_arrival;
            $userInvoice->userDelivery->save();

            // fire event
            event(new UserOrderCollected($invoice));
        }
    }

    /**
     * Products don't present on vendor store. Order cancelled.
     *
     * @param Invoice|Model $invoice
     */
    private function invoiceCancelled(Invoice $invoice)
    {
        // delete products from invoice
        $this->invoiceHandler->bindInvoice($invoice);
        $invoice->invoiceProduct->each(function (InvoiceProduct $invoiceProduct){
            $this->invoiceHandler->deleteProducts($invoiceProduct->id);
        });

        // set invoice status as cancelled
        $invoice->invoice_status_id = InvoiceStatusInterface::CANCELLED;
        $invoice->save();

        // fire event
        event(new UserOrderCancelled($invoice));
    }

    /**
     * Some products don't present on vendor store. Partially collected invoice.
     *
     * @param Invoice|Model $invoice
     * @param array $collectedProduct
     * @throws Exception
     */
    private function invoicePartiallyCollected(Invoice $invoice, array $collectedProduct)
    {
        // decrease products count in invoice
        $this->invoiceHandler->bindInvoice($invoice);

        $invoice->invoiceProduct->each(function (InvoiceProduct $invoiceProduct) use ($collectedProduct){

            $orderProductCountDifference = $invoiceProduct->quantity - $collectedProduct[$invoiceProduct->id];

            if ($orderProductCountDifference > 0) {
                $this->invoiceHandler->decreaseProductCount($invoiceProduct->id, $orderProductCountDifference);
            }
        });

        // mark as implemented
        $this->markInvoiceAsImplemented($invoice);

        // add to shipment
        $shipment = $this->addInvoiceToShipment($invoice);

        // define related user invoice.
        $userInvoice = $invoice->vendorInvoice->load('userInvoice')->userInvoice;

        if ($userInvoice) {
            // change related user invoice status if exists
            $userInvoice->delivery_status_id = DeliveryStatusInterface::COLLECTED;
            $userInvoice->save();

            // change delivery date
            $userInvoice->userDelivery->planned_arrival = $shipment->planned_arrival;
            $userInvoice->userDelivery->save();

            // fire event
            event(new UserOrderPartiallyCollected($invoice));
        }
    }

    /**
     * Mark vendor invoice as implemented.
     *
     * @param Invoice $invoice
     */
    private function markInvoiceAsImplemented(Invoice $invoice)
    {
        $invoice->vendorInvoice->implemented = 1;
        $invoice->vendorInvoice->save();
    }

    /**
     * @param Invoice|Model $invoice
     * @return Shipment
     * @throws Exception
     */
    private function addInvoiceToShipment(Invoice $invoice): Shipment
    {
        // add shipment
        $nearestShipment = $this->shipmentDispatcher->getOrCreateNextShipment();

        $invoice->shipments_id = $nearestShipment->id;
        $invoice->save();

        return $nearestShipment;
    }

    /**
     * Get not collected to shipment vendor orders.
     *
     * @param VendorInvoiceConstraints $constraints
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    private function getOutgoingOrders(VendorInvoiceConstraints $constraints)
    {
        return $this->invoiceRepository->getInvoices($constraints);
    }

    /**
     * Get all not collected order's product
     *
     * @param VendorInvoiceConstraints $constraints
     * @return Collection
     */
    private function getOutgoingProducts(VendorInvoiceConstraints $constraints): Collection
    {
        return $this->invoiceRepository->getRetrieveInvoicesQuery($constraints)
            ->leftJoin('invoice_products', 'invoices.id', '=', 'invoice_products.invoices_id')
            ->join('products', 'products.id', '=', 'invoice_products.products_id')
            ->join('vendor_products', 'vendor_products.products_id', '=', 'products.id')
            ->select(['products.page_title_' . app()->getLocale() . ' as page_title', 'vendor_products.vendor_product_id as vendor_product_id', DB::raw('sum(invoice_products.quantity) as total_quantity')])
            ->groupBy('vendor_products.vendor_product_id')
            ->get();
    }
}
