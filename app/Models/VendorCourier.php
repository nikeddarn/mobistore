<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorCourier extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'vendor_couriers';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vendorShipmentSchedule()
    {
        return $this->hasMany('App\Models\VendorShipmentSchedule', 'couriers_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vendorShipment()
    {
        return $this->hasMany('App\Models\VendorShipment', 'couriers_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vendor()
    {
        return $this->belongsTo('App\Models\Vendor', 'vendors_id', 'id');
    }
}
