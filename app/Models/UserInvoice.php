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
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'users_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function userDelivery()
    {
        return $this->belongsTo('App\Models\UserDelivery', 'user_deliveries_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function vendorInvoice()
    {
        return $this->hasOne('App\Models\VendorInvoice', 'user_invoices_id', 'id');
    }
}
