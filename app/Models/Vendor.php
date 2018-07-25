<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'vendors';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'vendor_users', 'vendors_id', 'users_id' );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vendorUser()
    {
        return $this->hasMany('App\Models\VendorUser', 'vendors_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vendorInvoice()
    {
        return $this->hasMany('App\Models\VendorInvoice', 'vendors_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vendorActiveReclamation()
    {
        return $this->hasMany('App\Models\VendorActiveReclamation', 'vendors_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function reclamation()
    {
        return $this->belongsToMany('App\Models\Reclamation', 'vendor_active_reclamations', 'vendors_id', 'reclamations_id');
    }
}
