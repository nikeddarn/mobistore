<?php

namespace App\Models;

use App\Models\Traits\Translatable;
use Illuminate\Database\Eloquent\Model;

class Storage extends Model
{
    use Translatable;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'storages';

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
        return $this->hasMany('App\Models\StorageDepartment', 'storages_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function city()
    {
        return $this->belongsTo('App\Models\City', 'cities_id', 'id');
    }

}
