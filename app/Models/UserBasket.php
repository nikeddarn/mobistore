<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBasket extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'user_baskets';

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice', 'invoices_id', 'id');
    }
}
