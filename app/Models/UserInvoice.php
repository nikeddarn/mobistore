<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInvoice extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'user_invoices';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice', 'invoices_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userDelivery()
    {
        return $this->belongsTo('App\Models\UserDelivery', 'user_deliveries_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deliveryStatus()
    {
        return $this->belongsTo('App\Models\DeliveryStatus', 'delivery_status_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deliveryType()
    {
        return $this->belongsTo('App\Models\DeliveryType', 'delivery_types_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vendorInvoice()
    {
        return $this->belongsTo('App\Models\VendorInvoice', 'vendor_invoices_id', 'id');
    }
}
