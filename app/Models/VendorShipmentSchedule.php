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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'vendor_couriers_id', 'planned_departure', 'planned_arrival',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vendorCourier()
    {
        return $this->belongsTo('App\Models\VendorCourier', 'vendor_couriers_id', 'id');
    }

    /**
     * Get the planned departure.
     *
     * @param  string  $value
     * @return string
     */
    public function getPlannedDepartureAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value) : null;
    }

    /**
     * Get the planned arrival.
     *
     * @param  string  $value
     * @return string
     */
    public function getPlannedArrivalAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value) : null;
    }
}
