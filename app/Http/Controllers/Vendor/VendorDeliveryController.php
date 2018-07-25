<?php

namespace App\Http\Controllers\Vendor;

use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Contracts\Shop\Invoices\InvoiceStatusInterface;
use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Events\Delivery\ChangeDeliveryDate;
use App\Http\Support\Invoices\RelatedInvoices\RelatedInvoicesHandler;
use App\Http\Support\Invoices\Repositories\Vendor\VendorInvoiceConstraints;
use App\Http\Support\Invoices\Repositories\Vendor\VendorInvoiceRepository;
use App\Http\Support\Shipment\VendorShipmentDispatcher;
use App\Models\Vendor;
use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class VendorDeliveryController extends Controller
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
     * @var VendorInvoiceRepository
     */
    private $invoiceRepository;

    /**
     * @var VendorShipmentDispatcher
     */
    private $shipmentDispatcher;

    /**
     * @var RelatedInvoicesHandler
     */
    private $relatedInvoicesHandler;

    /**
     * AccountController constructor.
     * @param Vendor $vendor
     * @param VendorInvoiceRepository $invoiceRepository
     * @param VendorShipmentDispatcher $shipmentDispatcher
     * @param RelatedInvoicesHandler $relatedInvoicesHandler
     */
    public function __construct(Vendor $vendor, VendorInvoiceRepository $invoiceRepository, VendorShipmentDispatcher $shipmentDispatcher, RelatedInvoicesHandler $relatedInvoicesHandler)
    {
        $this->vendor = $vendor;
        $this->invoiceRepository = $invoiceRepository;
        $this->shipmentDispatcher = $shipmentDispatcher;
        $this->relatedInvoicesHandler = $relatedInvoicesHandler;
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

        return view('content.vendor.delivery.index')->with([
            'unloadedInvoices' => $this->getUnloadedInvoices(),
            'activeShipments' => $this->shipmentDispatcher->getNotCompletedVendorShipments($this->retrievedVendor->id),
            'availableShipments' => $this->shipmentDispatcher->getAvailableVendorShipments($this->retrievedVendor->id),
            'vendorId' => $this->retrievedVendor->id,
            'vendorTitle' => $this->retrievedVendor->title,
        ]);
    }

    /**
     *  Add given
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function addInvoicesToShipment(Request $request)
    {
        $this->retrievedVendor = $this->vendor->where('id', $request->get('vendors_id'))->first();

        if (!$this->retrievedVendor) {
            throw new Exception('Vendor is not defined.');
        }

        // retrieve and update invoices
        $this->invoiceRepository->getRetrieveInvoicesQuery(
            $this->getRetrieveInvoiceConstraints()
                ->setInvoicesId($request->get('invoices_id'))
        )
            ->update([
                'shipments_id' => $request->get('shipments_id'),
            ]);

        // retrieve shipment invoices
        $shipmentInvoices = $this->invoiceRepository->getRetrieveInvoicesQuery(
            $this->getRetrieveInvoiceConstraints()
                ->setInvoicesId($request->get('invoices_id'))
        )
            ->with('vendorInvoice')
            ->get();

        // update delivery time
        foreach ($shipmentInvoices as $invoice) {
            // get related user invoice
            $relatedUserInvoice = $this->relatedInvoicesHandler->getRelatedUserInvoice($invoice);

            if ($relatedUserInvoice) {

                // get related vendor invoices
                $relatedVendorInvoices = $this->relatedInvoicesHandler->getRelatedVendorInvoicesByUserInvoice($relatedUserInvoice);

                if ($this->relatedInvoicesHandler->areRelatedVendorInvoicesImplemented($relatedVendorInvoices)) {

                    // define new delivery date
                    $userInvoiceDeliveryDate = $this->shipmentDispatcher->calculateDeliveryDateByInvoices($relatedVendorInvoices->pluck('id')->toArray());

                    // change delivery date
                    if ($this->relatedInvoicesHandler->updateDeliveryDate($relatedUserInvoice, $userInvoiceDeliveryDate)) {
                        // notify user
                        event(new ChangeDeliveryDate($relatedUserInvoice));
                    }
                }
            }
        }

        return back();
    }

    /**
     * Retrieve invoices that are not in any shipment.
     *
     * @return Collection
     */
    private function getUnloadedInvoices(): Collection
    {
        return $this->invoiceRepository->getRetrieveInvoicesQuery($this->getRetrieveInvoiceConstraints())
            ->whereNull('shipments_id')
            ->get();
    }

    /**
     * Get retrieve invoice constraints.
     *
     * @return VendorInvoiceConstraints
     */
    private function getRetrieveInvoiceConstraints(): VendorInvoiceConstraints
    {
        return (new VendorInvoiceConstraints())
            ->setVendorId($this->retrievedVendor->id)
            ->setInvoiceStatus(InvoiceStatusInterface::PROCESSING)
            ->setInvoiceType([InvoiceTypes::USER_ORDER, InvoiceTypes::USER_PRE_ORDER, InvoiceTypes::USER_RETURN_ORDER, InvoiceTypes::RECLAMATION, InvoiceTypes::EXCHANGE_RECLAMATION, InvoiceTypes::RETURN_RECLAMATION])
            ->setInvoiceDirection(InvoiceDirections::OUTGOING)
            ->setImplementedStatus(1);
    }
}
