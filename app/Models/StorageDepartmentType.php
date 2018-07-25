<?php

namespace App\Models;

use App\Models\Traits\Translatable;
use Illuminate\Database\Eloquent\Model;

class StorageDepartmentType extends Model
{
    use Translatable;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'storage_department_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title_en', 'title_ru', 'title_ua',
    ];

    /**
     * The attributes that should be selected depends on locale from JSON type field.
     *
     * @var array
     */
    public $translatable = [
        'title',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function storageDepartment()
    {
        return $this->hasMany('App\Models\StorageDepartment', 'storage_department_types_id', 'id');
    }
}
