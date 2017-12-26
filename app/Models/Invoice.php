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
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function userBasket()
    {
        return $this->hasOne('App\Models\UserBasket', 'invoices_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function invoiceProduct()
    {
        return $this->hasMany('App\Models\InvoiceProduct', 'invoices_id', 'id');
    }
}
