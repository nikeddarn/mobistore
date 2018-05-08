<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorInvoice extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'vendor_invoices';

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
    public function vendor()
    {
        return $this->belongsTo('App\Models\Vendor', 'vendors_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function userInvoice()
    {
        return $this->belongsToMany('App\Models\UserInvoice', 'user_invoices_has_vendor_invoices', 'vendor_invoices_id', 'user_invoices_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userInvoiceHasVendorInvoice()
    {
        return $this->hasMany('App\Models\UserInvoiceHasVendorInvoice', 'vendor_invoices_id', 'id');
    }
}
