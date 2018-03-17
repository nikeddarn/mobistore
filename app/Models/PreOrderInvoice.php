<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PreOrderInvoice extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'pre_order_invoices';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'vendor_invoices_id', 'user_invoices_id',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;
}
