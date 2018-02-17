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
    public $timestamps = false;

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
}
