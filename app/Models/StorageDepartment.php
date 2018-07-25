<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StorageDepartment extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'storage_departments';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function storageProduct()
    {
        return $this->hasMany('App\Models\StorageProduct', 'storage_departments_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function storageActiveReclamation()
    {
        return $this->hasMany('App\Models\StorageActiveReclamation', 'storage_departments_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function reclamation()
    {
        return $this->belongsToMany('App\Models\Reclamation', 'storage_active_reclamations', 'storage_departments_id', 'reclamations_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function storageInvoice()
    {
        return $this->hasMany('App\Models\StorageInvoice', 'storage_departments_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function storage()
    {
        return $this->belongsTo('App\Models\Storage', 'storages_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function storageDepartmentType()
    {
        return $this->belongsTo('App\Models\StorageDepartmentType', 'storage_department_types_id', 'id');
    }

}
