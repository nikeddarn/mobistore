<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class UserDelivery extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'user_deliveries';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function userInvoice()
    {
        return $this->hasOne('App\Models\UserInvoice', 'user_deliveries_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deliveryStatus()
    {
        return $this->belongsTo('App\Models\DeliveryStatus', 'delivery_status_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deliveryType()
    {
        return $this->belongsTo('App\Models\DeliveryType', 'delivery_types_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deliveryRecipient()
    {
        return $this->belongsTo('App\Models\DeliveryRecipient', 'delivery_recipient_id', 'id');
    }

    /**
     * Get the planned arrival.
     *
     * @param  string  $value
     * @return string
     */
    public function getPlannedArrivalAttribute($value)
    {
        return $value ? Carbon::createFromFormat('Y-m-d H:i:s', $value) : null;
    }
}
