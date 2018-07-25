<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceDefectProduct extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'invoice_defect_products';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Non auto incrementing primary key.
     *
     * @var bool
     */
    public $incrementing = false;

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
    public function reclamation()
    {
        return $this->belongsTo('App\Models\Reclamation', 'reclamations_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function userReclamation()
    {
        return $this->belongsToMany('App\Models\UserReclamation', 'user_reclamation_invoices', 'invoice_defect_products_id', 'user_reclamations_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function vendorReclamation()
    {
        return $this->belongsToMany('App\Models\VendorReclamation', 'vendor_reclamation_invoices', 'invoice_defect_products_id', 'vendor_reclamations_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\belongsToMany
     */
    public function storageReclamation()
    {
        return $this->belongsToMany('App\Models\StorageReclamation', 'storage_reclamation_invoices', 'invoice_defect_products_id', 'storage_reclamations_id');
    }
}
