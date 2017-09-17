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
        'categories_id', 'brands_id', 'models_id', 'url', 'page_title_en', 'page_title_ru', 'page_title_ua', 'meta_title_en', 'meta_title_ru', 'meta_title_ua', 'meta_description_en', 'meta_description_ru', 'meta_description_ua', 'meta_keywords_en', 'meta_keywords_ru', 'meta_keywords_ua', 'summary_en', 'summary_ru', 'summary_ua',
    ];
}
