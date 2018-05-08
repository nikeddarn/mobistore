<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class VendorShipmentSchedule extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'vendor_shipment_schedules';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courier()
    {
        return $this->belongsTo('App\Models\Courier', 'couriers_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vendor()
    {
        return $this->belongsTo('App\Models\Vendor', 'vendors_id', 'id');
    }

    /**
     * Get the planned departure.
     *
     * @param  string  $value
     * @return string
     */
    public function getPlannedDepartureAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d h:i:s', $value) : null;
    }
}
