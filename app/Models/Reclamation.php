<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reclamation extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'reclamations';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['accepted'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rejectReclamationReason()
    {
        return $this->belongsTo('App\Models\RejectReclamationReason', 'reject_reclamation_reasons_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reclamationStatus()
    {
        return $this->belongsTo('App\Models\ReclamationStatus', 'reclamation_status_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoiceDefectProduct()
    {
        return $this->hasMany('App\Models\InvoiceDefectProduct', 'reclamations_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userActiveReclamation()
    {
        return $this->hasMany('App\Models\UserActiveReclamation', 'reclamations_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vendorActiveReclamation()
    {
        return $this->hasMany('App\Models\VendorActiveReclamation', 'reclamations_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function storageActiveReclamation()
    {
        return $this->hasMany('App\Models\StorageActiveReclamation', 'reclamations_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'products_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function invoice()
    {
        return $this->belongsToMany('App\Models\Invoice', 'invoice_defect_products', 'reclamations_id', 'invoices_id');
    }


}
