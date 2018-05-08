<?php
/**
 * Week days calendar.
 */

namespace App\Http\Support\Shipment;


use App\Models\VendorShipmentSchedule;
use Carbon\Carbon;

class ShipmentCalendar
{
    /**
     * @var VendorShipmentSchedule
     */
    private $vendorShipmentSchedule;

    /**
     * ShipmentCalendar constructor.
     * @param VendorShipmentSchedule $vendorShipmentSchedule
     */
    public function __construct(VendorShipmentSchedule $vendorShipmentSchedule)
    {
        $this->vendorShipmentSchedule = $vendorShipmentSchedule;
    }
    /**
     * Define next possible week day for local delivery.
     *
     * @param $maxShipmentTime
     * @return Carbon
     */
    public function getNearestLocalWeekDay($maxShipmentTime)
    {
        $possibleDay = Carbon::now();

        // add 1 day if current time more than max shipment departure time
        if ($possibleDay > Carbon::today()->addHours($maxShipmentTime)) {
            $possibleDay->addDay();
        }

        // add days till departure day is weekday
        while ($possibleDay->isWeekend()) {
            $possibleDay->addDay();
        }

        return $possibleDay;
    }

    /**
     * Define next possible week day for vendor delivery.
     *
     * @param $maxShipmentDispatchingTime
     * @return Carbon
     */
    public function getNearestVendorWeekDay($maxShipmentDispatchingTime = null)
    {
        $possibleDay = Carbon::now();

        // add 1 day if current time more than max shipment departure time
        if ($maxShipmentDispatchingTime && $possibleDay > Carbon::today()->addHours($maxShipmentDispatchingTime)) {
            $possibleDay->addDay();
        }

        return $possibleDay;
    }

    public function getNextPlannedVendorDepartureDay(int $vendorId)
    {
        $nearestVendorWeekDay = $this->getNearestVendorWeekDay();

        $nearestDeparture =  $this->vendorShipmentSchedule
            ->where('vendors_id', $vendorId)
            ->where('planned_departure', '>=', $nearestVendorWeekDay->toDateString())
            ->orderBy('planned_departure')
            ->first();

        return $nearestDeparture ? $nearestDeparture->planned_departure : null;
    }
}