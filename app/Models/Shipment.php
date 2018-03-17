<?php

namespace App\Models;

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
}
