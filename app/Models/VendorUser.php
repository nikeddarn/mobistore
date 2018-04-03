<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorUser extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'vendor_users';

    /**
     * Array of composite primary keys.
     *
     * @var array
     */
    protected $primaryKey = ['vendors_id', 'users_id'];

    /**
     * Non auto incrementing primary key.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'vendors_id', 'users_id',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'users_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vendor()
    {
        return $this->belongsTo('App\Models\Vendor', 'vendors_id', 'id');
    }
}
