<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInvoiceHasVendorInvoice extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'user_invoices_has_vendor_invoices';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_invoices_id', 'vendor_invoices_id',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Array of composite primary keys.
     *
     * @var array
     */
    protected $primaryKey = ['user_invoices_id', 'vendor_invoices_id',];

    /**
     * Non auto incrementing primary key.
     *
     * @var bool
     */
    public $incrementing = false;
}
