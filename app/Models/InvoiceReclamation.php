<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceReclamation extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'invoice_reclamations';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['accepted',];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function rejectReclamationReason()
    {
        return $this->belongsTo('App\Models\RejectReclamationReason', 'reject_reasons_id', 'id');
    }

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
    public function product()
    {
        return $this->belongsTo('App\Models\Product', 'products_id', 'id');
    }
}
