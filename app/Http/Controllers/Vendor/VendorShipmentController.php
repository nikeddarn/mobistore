<?php

namespace App\Http\Controllers\Vendor;

use App\Contracts\Shop\Delivery\DeliveryStatusInterface;
use App\Events\Delivery\ChangeDeliveryDate;
use App\Http\Support\Courier\VendorCourierRepository;
use App\Http\Support\Invoices\RelatedInvoices\RelatedInvoicesHandler;
use App\Http\Support\Shipment\Calendar\VendorShipmentCalendar;
use App\Http\Support\Shipment\VendorShipmentDispatcher;
use App\Models\Vendor;
use Carbon\Carbon;
use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class VendorShipmentController extends Controller
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
     * @var VendorShipmentDispatcher
     */
    private $shipmentDispatcher;

    /**
     * @var VendorShipmentCalendar
     */
    private $shipmentCalendar;

    /**
     * @var VendorCourierRepository
     */
    private $courierRepository;

    /**
     * @var RelatedInvoicesHandler
     */
    private $relatedInvoicesHandler;

    /**
     * AccountController constructor.
     *
     * @param Vendor $vendor
     * @param VendorShipmentDispatcher $shipmentDispatcher
     * @param VendorShipmentCalendar $shipmentCalendar
     * @param VendorCourierRepository $courierRepository
     * @param RelatedInvoicesHandler $relatedInvoicesHandler
     */
    public function __construct(Vendor $vendor, VendorShipmentDispatcher $shipmentDispatcher, VendorShipmentCalendar $shipmentCalendar, VendorCourierRepository $courierRepository, RelatedInvoicesHandler $relatedInvoicesHandler)
    {
        $this->vendor = $vendor;
        $this->shipmentDispatcher = $shipmentDispatcher;
        $this->shipmentCalendar = $shipmentCalendar;
        $this->courierRepository = $courierRepository;
        $this->relatedInvoicesHandler = $relatedInvoicesHandler;
    }

    /**
     * Show create new shipment form.
     *
     * @param int $vendorId
     * @return View
     * @throws Exception
     */
    public function index(int $vendorId)
    {
        $this->retrievedVendor = $this->vendor->where('id', $vendorId)->first();

        if (!$this->retrievedVendor) {
            throw new Exception('Vendor is not defined.');
        }

        return view('content.vendor.shipment.index')->with([
            'vendorId' => $this->retrievedVendor->id,
            'vendorTitle' => $this->retrievedVendor->title,
            'notDispatchedVendorShipments' => $this->shipmentDispatcher->getAvailableVendorShipments($vendorId),
            'courierTours' => $this->shipmentCalendar->getCouriersTours($this->retrievedVendor->id),
            'vendorCouriers' => $this->courierRepository->getVendorCouriers($this->retrievedVendor->id),
        ]);
    }

    /**
     * Create vendor shipment from courier tour.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function createFromSchedule(Request $request)
    {
        $vendorTour = $this->shipmentCalendar->getCourierTourById($request->get('vendors_id'), $request->get('courierTourId'));

        // wrong vendor tour
        if (!$vendorTour) {
            abort(403);
        }

        Validator::extend('shipmentUnique', function ($attribute, $value) use ($vendorTour) {
            return !$this->shipmentDispatcher->vendorShipmentExists($vendorTour->vendor_couriers_id, $vendorTour->planned_departure);
        });

        $this->validate($request, [
            'vendors_id' => 'required|integer',
            'courierTourId' => 'required|shipmentUnique'
        ]);

        $this->shipmentDispatcher->createVendorShipment($vendorTour->planned_departure, $vendorTour->planned_arrival, $request->get('vendors_id'), $vendorTour->vendor_couriers_id);

        Session::flash('message', 'New shipment was inserted');

        return back();
    }

    /**
     * Create vendor shipment by date and courier.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function createByDate(Request $request)
    {
        Validator::extend('arrivalMoreThanDeparture', function ($attribute, $value, $parameters, $validator) {
            return Carbon::createFromFormat('d-m-Y', $value) > Carbon::createFromFormat('d-m-Y', $validator->getData()['planned_departure']);
        });

        Validator::extend('shipmentUnique', function ($attribute, $value) use ($request) {
            return !$this->shipmentDispatcher->vendorShipmentExists($request->get('vendor_couriers_id'), Carbon::createFromFormat('d-m-Y', $request->get('planned_departure')));
        });

        $this->validate($request, [
            'vendors_id' => 'required|integer',
            'vendor_couriers_id' => 'required|integer|shipmentUnique',
            'planned_departure' => 'required|string',
            'planned_arrival' => 'required|string|arrivalMoreThanDeparture',
        ]);

        $departure = Carbon::createFromFormat('d-m-Y', $request->get('planned_departure'));
        $arrival = Carbon::createFromFormat('d-m-Y', $request->get('planned_arrival'));

        $this->shipmentDispatcher->createVendorShipment($departure, $arrival, $request->get('vendors_id'), $request->get('vendor_couriers_id'));

        Session::flash('message', 'New shipment was inserted');

        return back();
    }

    /**
     * Dispatch shipment.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function dispatchShipment(Request $request)
    {
        // dispatch shipment
        $dispatchedVendorInvoices = $this->shipmentDispatcher->dispatchShipment($request->get('shipments_id'));

        // change delivery date, delivery status of related user invoice and notify user
        foreach ($dispatchedVendorInvoices as $dispatchedVendorInvoice) {

            // get related user invoice
            $relatedUserInvoice = $this->relatedInvoicesHandler->getRelatedUserInvoice($dispatchedVendorInvoice);

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

                    // change delivery status
                    $this->relatedInvoicesHandler->updateDeliveryStatus($relatedUserInvoice, DeliveryStatusInterface::STORAGE_DELIVERING);
                }
            }
        }

        return back();
    }

    /**
     * Delete shipment.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws Exception
     */
    public function removeShipment(Request $request)
    {
        $unloadedVendorInvoices = $this->shipmentDispatcher->deleteShipment($request->get('shipments_id'));

        // change delivery date, delivery status of related user invoice and notify user
        foreach ($unloadedVendorInvoices as $dispatchedVendorInvoice) {
            // get related user invoice
            $relatedUserInvoice = $this->relatedInvoicesHandler->getRelatedUserInvoice($dispatchedVendorInvoice);

            if ($relatedUserInvoice) {

                // change delivery date to undefined
                if ($this->relatedInvoicesHandler->updateDeliveryDate($relatedUserInvoice)) {
                    // notify user
                    event(new ChangeDeliveryDate($relatedUserInvoice));
                }

                // change delivery status
                $this->relatedInvoicesHandler->updateDeliveryStatus($relatedUserInvoice, DeliveryStatusInterface::COLLECTED);
            }
        }

        return back();
    }
}
