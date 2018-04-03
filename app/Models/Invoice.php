<?php

namespace App\Models;

use App\Contracts\Shop\Invoices\InvoiceDirections;
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
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function userInvoice()
    {
        return $this->hasOne('App\Models\UserInvoice', 'invoices_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function vendorInvoice()
    {
        return $this->hasOne('App\Models\VendorInvoice', 'invoices_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoiceProduct()
    {
        return $this->hasMany('App\Models\InvoiceProduct', 'invoices_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoiceReclamation()
    {
        return $this->hasMany('App\Models\InvoiceReclamation', 'invoices_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function vendor()
    {
        return $this->belongsToMany('App\Models\Vendor', 'vendor_invoices', 'invoices_id', 'vendors_id' );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoiceType()
    {
        return $this->belongsTo('App\Models\InvoiceType', 'invoice_types_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoiceStatus()
    {
        return $this->belongsTo('App\Models\InvoiceStatus', 'invoice_status_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\hasMany
     */
    public function storageInvoice()
    {
        return $this->hasMany('App\Models\StorageInvoice', 'invoices_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function incomingStorage()
    {
        return $this->belongsToMany('App\Models\Storage', 'storage_invoices', 'invoices_id', 'storages_id' )->wherePivot('direction', InvoiceDirections::INCOMING);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function outgoingStorage()
    {
        return $this->belongsToMany('App\Models\Storage', 'storage_invoices', 'invoices_id', 'storages_id' )->wherePivot('direction', InvoiceDirections::OUTGOING);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function storage()
    {
        return $this->belongsToMany('App\Models\Storage', 'storage_invoices', 'invoices_id', 'storages_id' );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shipment()
    {
        return $this->belongsTo('App\Models\Shipment', 'shipments_id', 'id');
    }
}
