<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceModel extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'models';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'brands_id', 'url', 'breadcrumb', 'series', 'title', 'image,',  'meta_keywords_en', 'meta_keywords_ru', 'meta_keywords_ua',
    ];
}
