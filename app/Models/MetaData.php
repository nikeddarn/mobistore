<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetaData extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'meta_data';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'categories_id', 'brands_id', 'models_id', 'url', 'is_canonical', 'page_title_en', 'page_title_ru', 'page_title_ua', 'meta_title_en', 'meta_title_ru', 'meta_title_ua', 'meta_description_en', 'meta_description_ru', 'meta_description_ua', 'meta_keywords_en', 'meta_keywords_ru', 'meta_keywords_ua', 'summary_en', 'summary_ru', 'summary_ua',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'categories_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function brand()
    {
        return $this->belongsTo('App\Models\Brand', 'brands_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deviceModel()
    {
        return $this->belongsTo('App\Models\DeviceModel', 'models_id', 'id');
    }
}
