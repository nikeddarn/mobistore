<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryRecipient extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'delivery_recipients';

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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function userDelivery()
    {
        return $this->hasMany('App\Models\UserDelivery', 'delivery_recipient_id', 'id');
    }
}
