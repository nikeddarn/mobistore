<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'invoices';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['invoice_sum',];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function userCart()
    {
        return $this->hasOne('App\Models\UserCart', 'invoices_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoiceProduct()
    {
        return $this->hasMany('App\Models\InvoiceProduct', 'invoices_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function storage()
    {
        return $this->belongsTo('App\Models\Storage', 'storages_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoiceType()
    {
        return $this->belongsTo('App\Models\InvoiceType', 'invoice_types_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoiceAmount()
    {
        return $this->hasMany('App\Models\InvoiceAmount', 'invoices_id', 'id');
    }
}
