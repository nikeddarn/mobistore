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
}
