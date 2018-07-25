<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'shipments';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoice()
    {
        return $this->hasMany('App\Models\Invoice', 'shipments_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function localShipment()
    {
        return $this->hasOne('App\Models\LocalShipment', 'shipments_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function vendorShipment()
    {
        return $this->hasOne('App\Models\VendorShipment', 'shipments_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasOne
     */
    public function postShipment()
    {
        return $this->hasOne('App\Models\PostShipment', 'shipments_id', 'id');
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
     * Get the dispatched timestamp.
     *
     * @param  string  $value
     * @return string
     */
    public function getDispatchedAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value) : null;
    }

    /**
     * Get the received timestamp.
     *
     * @param  string  $value
     * @return string
     */
    public function getReceivedAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value) : null;
    }
}
