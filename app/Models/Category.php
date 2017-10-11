<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class Category extends Model
{
    use NodeTrait;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'parent_id', '_lft', '_rgt', 'url', 'breadcrumb', 'image', 'title_en', 'title_ru', 'title_ua', 'meta_keywords_en', 'meta_keywords_ru', 'meta_keywords_ua',
    ];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function metaData()
    {
        return $this->hasMany('App\Models\MetaData', 'categories_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function product()
    {
        return $this->hasMany('App\Models\Product', 'categories_id', 'id');
    }
}
