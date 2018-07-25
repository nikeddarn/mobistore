<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Support\Courier\VendorCourierRepository;
use App\Http\Support\Shipment\Calendar\VendorShipmentCalendar;
use App\Models\Vendor;
use Carbon\Carbon;
use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class VendorCourierController extends Controller
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
     * @var VendorShipmentCalendar
     */
    private $shipmentCalendar;

    /**
     * @var VendorCourierRepository
     */
    private $courierRepository;

    /**
     * AccountController constructor.
     *
     * @param Vendor $vendor
     * @param VendorShipmentCalendar $shipmentCalendar
     * @param VendorCourierRepository $courierRepository
     */
    public function __construct(Vendor $vendor, VendorShipmentCalendar $shipmentCalendar, VendorCourierRepository $courierRepository)
    {
        $this->vendor = $vendor;
        $this->shipmentCalendar = $shipmentCalendar;
        $this->courierRepository = $courierRepository;
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

        return view('content.vendor.courier.index')->with([
            'vendorId' => $this->retrievedVendor->id,
            'vendorTitle' => $this->retrievedVendor->title,
            'courierTours' => $this->shipmentCalendar->getCouriersTours($this->retrievedVendor->id),
            'vendorCouriers' => $this->courierRepository->getVendorCouriers($this->retrievedVendor->id),
        ]);
    }

    /**
     * Create courier.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createCourier(Request $request)
    {
        $this->validate($request, [
            'vendors_id' => 'required|integer',
            'name' => 'required|string|unique:vendor_couriers,name',
            'phone1' => 'required|string',
            'phone2' => 'required|string',
        ]);

        $this->courierRepository->createCourier($request->all());

        Session::flash('message', 'New courier was created');

        return back();
    }

    /**
     * Create courier tour.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createCourierTour(Request $request)
    {
        Validator::extend('arrivalMoreThanDeparture', function ($attribute, $value, $parameters, $validator) {
            return Carbon::createFromFormat('d-m-Y', $value) > Carbon::createFromFormat('d-m-Y', $validator->getData()['planned_departure']);
        });

        Validator::extend('courierTourUnique', function () use ($request) {
            return !$this->shipmentCalendar->courierTourExist($request->get('vendor_couriers_id'), Carbon::createFromFormat('d-m-Y', $request->get('planned_departure')));
        });

        $this->validate($request, [
            'vendors_id' => 'required|integer',
            'vendor_couriers_id' => 'required|integer|courierTourUnique',
            'planned_departure' => 'required|string',
            'planned_arrival' => 'required|string|arrivalMoreThanDeparture',
        ]);

        // given courier doesn't belong to current vendor
        if (!$this->courierRepository->getVendorCourierById($request->get('vendors_id'), $request->get('vendor_couriers_id'))){
            abort(403);
        }

        $tourData = [
            'vendor_couriers_id' => $request->get('vendor_couriers_id'),
            'planned_departure' => Carbon::createFromFormat('d-m-Y', $request->get('planned_departure'))->toDateString(),
            'planned_arrival' => Carbon::createFromFormat('d-m-Y', $request->get('planned_arrival'))->toDateString(),
        ];

        $this->shipmentCalendar->createCourierTour($tourData);

        Session::flash('message', 'New courier tour was inserted');

        return back();
    }
}
