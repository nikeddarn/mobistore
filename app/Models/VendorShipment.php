<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorShipment extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'vendor_shipments';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function courier()
    {
        return $this->belongsTo('App\Models\Courier', 'couriers_id', 'id');
    }
}
