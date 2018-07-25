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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userInvoices()
    {
        return $this->hasMany('App\Models\UserInvoice', 'invoices_id', 'id');
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
    public function incomingUserInvoice()
    {
        return $this->hasOne('App\Models\UserInvoice', 'invoices_id', 'id')->where('direction', InvoiceDirections::INCOMING);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function outgoingUserInvoice()
    {
        return $this->hasOne('App\Models\UserInvoice', 'invoices_id', 'id')->where('direction', InvoiceDirections::OUTGOING);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vendorInvoices()
    {
        return $this->hasMany('App\Models\VendorInvoice', 'invoices_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function vendorInvoice()
    {
        return $this->hasOne('App\Models\VendorInvoice', 'invoices_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function incomingVendorInvoice()
    {
        return $this->hasOne('App\Models\VendorInvoice', 'invoices_id', 'id')->where('direction', InvoiceDirections::INCOMING);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function outgoingVendorInvoice()
    {
        return $this->hasOne('App\Models\VendorInvoice', 'invoices_id', 'id')->where('direction', InvoiceDirections::OUTGOING);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoiceProducts()
    {
        return $this->hasMany('App\Models\InvoiceProduct', 'invoices_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoiceDefectProducts()
    {
        return $this->hasMany('App\Models\InvoiceDefectProduct', 'invoices_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function reclamations()
    {
        return $this->belongsToMany('App\Models\Reclamation', 'invoice_defect_products', 'invoices_id', 'reclamations_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function vendors()
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function storageInvoices()
    {
        return $this->hasMany('App\Models\StorageInvoice', 'invoices_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function incomingStorageInvoice()
    {
        return $this->hasOne('App\Models\StorageInvoice', 'invoices_id', 'id')->where('direction', InvoiceDirections::INCOMING);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function outgoingStorageInvoice()
    {
        return $this->hasOne('App\Models\StorageInvoice', 'invoices_id', 'id')->where('direction', InvoiceDirections::OUTGOING);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function incomingStorageDepartment()
    {
        return $this->belongsToMany('App\Models\StorageDepartment', 'storage_invoices', 'invoices_id', 'storage_departments_id' )->wherePivot('direction', InvoiceDirections::INCOMING);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function outgoingStorageDepartment()
    {
        return $this->belongsToMany('App\Models\StorageDepartment', 'storage_invoices', 'invoices_id', 'storage_departments_id' )->wherePivot('direction', InvoiceDirections::OUTGOING);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function storageDepartments()
    {
        return $this->belongsToMany('App\Models\StorageDepartment', 'storage_invoices', 'invoices_id', 'storage_departments_id' );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function shipment()
    {
        return $this->belongsTo('App\Models\Shipment', 'shipments_id', 'id');
    }

    /**
     * Get invoice type id.
     *
     * @return int
     */
    public function getInvoiceTypeId():int
    {
        return $this->getAttributeValue('invoice_types_id');
    }
}
