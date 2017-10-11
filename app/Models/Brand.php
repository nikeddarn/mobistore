<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'brands';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'breadcrumb', 'url', 'title', 'image', 'priority', 'meta_keywords_en', 'meta_keywords_ru', 'meta_keywords_ua',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function product()
    {
        return $this->hasMany('App\Models\Product', 'brands_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function deviceModel()
    {
        return $this->hasMany('App\Models\DeviceModel', 'brands_id', 'id');
    }
}
