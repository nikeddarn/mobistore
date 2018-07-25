<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 13.05.18
 * Time: 10:00
 */

namespace App\Http\Support\Shipment\Calendar;


use App\Models\VendorShipmentSchedule;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class VendorShipmentCalendar extends ShipmentCalendar
{
    /**
     * @var VendorShipmentSchedule
     */
    private $vendorShipmentSchedule;

    /**
     * VendorShipmentCalendar constructor.
     *
     * @param VendorShipmentSchedule $vendorShipmentSchedule
     */
    public function __construct(VendorShipmentSchedule $vendorShipmentSchedule)
    {
        $this->vendorShipmentSchedule = $vendorShipmentSchedule;
    }

    /**
     * Define next possible shipment day.
     *
     * @param $maxShipmentDispatchingTime
     * @return Carbon
     */
    public function getNearestShipmentDay($maxShipmentDispatchingTime = null)
    {
        $nearestShipmentDay = $this->getNearestWeekDay();

        // add 1 day if current time more than max shipment departure time
        if ($maxShipmentDispatchingTime && $nearestShipmentDay > Carbon::today()->addHours($maxShipmentDispatchingTime)) {
            $nearestShipmentDay->addDay();
        }

        return $nearestShipmentDay;
    }

    /**
     * Define next possible shipment day from vendor couriers schedule.
     *
     * @param int $vendorId
     * @return Carbon|null
     */
    public function getNearestShipmentDayFromSchedule(int $vendorId)
    {
        $nearestVendorWeekDay = $this->getNearestShipmentDay();

        $nearestDeparture = $this->vendorShipmentSchedule
            ->where('vendors_id', $vendorId)
            ->where('planned_departure', '>=', $nearestVendorWeekDay->toDateString())
            ->orderBy('planned_departure')
            ->first();

        return $nearestDeparture ? $nearestDeparture->planned_departure : null;
    }

    /**
     * Get nearest couriers tours.
     *
     * @param int $vendorId
     * @return Collection
     */
    public function getCouriersTours(int $vendorId): Collection
    {
        return $this->vendorShipmentSchedule->whereHas('vendorCourier', function ($query) use ($vendorId) {
            $query->where('vendors_id', $vendorId);
        })
            ->whereDate('planned_departure', '>=', Carbon::now()->toDateString())
            ->orderBy('planned_departure')
            ->with('vendorCourier')
            ->limit(config('shop.delivery.pre_order.max'))
            ->get();
    }

    /**
     * Create vendor courier tour in VendorShipmentSchedule.
     *
     * @param array $data
     * @return VendorShipmentSchedule
     */
    public function createCourierTour(array $data): VendorShipmentSchedule
    {
        return $this->vendorShipmentSchedule->create($data);
    }

    /**
     * Get courier tour by tour id and vendor id.
     *
     * @param int $vendorId
     * @param $tourId
     * @return VendorShipmentSchedule
     */
    public function getCourierTourById(int $vendorId, $tourId): VendorShipmentSchedule
    {
        return $this->vendorShipmentSchedule->whereHas('vendorCourier', function ($query) use ($vendorId) {
            $query->where('vendors_id', $vendorId);
        })->where('id', $tourId)
            ->first();
    }

    /**
     * Is courier tour exist ?
     *
     * @param int $courierId
     * @param Carbon $departure
     * @return bool
     */
    public function courierTourExist(int $courierId, Carbon $departure): bool
    {
        return (bool)$this->vendorShipmentSchedule->where('vendor_couriers_id', $courierId)->whereDate('planned_departure', $departure->toDateString())->first();
    }

    /**
     * Get max of all vendors possible shipments departure date. All shipments are required.
     *
     * @param array $invoiceVendors
     * @return Carbon|null
     */
    public function getMaxArrivalDateFromVendorsSchedule(array $invoiceVendors)
    {
        // define nearest departure days from vendor shipment schedule
        $possibleDepartureDate = $this->getNearestShipmentDay(config('shop.shipment.current_day_delivery_max_time.vendor'));

        // define max of all vendors possible shipments departure date
        return $this->vendorShipmentSchedule
            ->where('planned_departure', '>=', $possibleDepartureDate->toDateString())
            ->join('vendor_couriers', function ($join) use ($invoiceVendors) {
                $join->on('vendor_couriers.id', '=', 'vendor_shipment_schedules.vendor_couriers_id')
                    ->whereIn('vendors_id', $invoiceVendors);
            })
            ->selectRaw('MIN(vendor_shipment_schedules.planned_arrival) AS arrival')
            ->groupBy('vendor_couriers.vendors_id')
            ->get()
            ->max('arrival');
    }
}