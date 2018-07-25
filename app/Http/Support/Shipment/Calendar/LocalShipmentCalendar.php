<?php
/**
 * Local shipment planner.
 */

namespace App\Http\Support\Shipment\Calendar;


use Carbon\Carbon;

class LocalShipmentCalendar extends ShipmentCalendar
{
    /**
     * Define next possible week day for local delivery.
     *
     * @param $maxShipmentTime
     * @return Carbon
     */
    public function getNearestShipmentDay($maxShipmentTime)
    {
        $nearestShipmentDay = $this->getNearestWeekDay();

        // add 1 day if current time more than max shipment departure time
        if ($nearestShipmentDay > Carbon::today()->addHours($maxShipmentTime)) {
            $nearestShipmentDay->addDay();
        }

        // add days till departure day is weekday
        while ($nearestShipmentDay->isWeekend()) {
            $nearestShipmentDay->addDay();
        }

        return $nearestShipmentDay;
    }
}