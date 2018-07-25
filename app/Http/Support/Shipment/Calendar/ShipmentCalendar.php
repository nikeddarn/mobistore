<?php
/**
 * Week days calendar.
 */

namespace App\Http\Support\Shipment\Calendar;


use Carbon\Carbon;

class ShipmentCalendar
{
    /**
     * Define nearest week day.
     *
     * @return Carbon
     */
    public function getNearestWeekDay()
    {
        $currentDay = Carbon::now();

        // add days till departure day is weekday
        while ($currentDay->isWeekend()) {
            $currentDay->addDay();
        }

        return $currentDay;
    }
}