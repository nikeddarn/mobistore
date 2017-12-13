<?php

namespace App\Models;

use App\Models\Traits\Translatable;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use Translatable;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'categories_id', 'brands_id', 'colors_id', 'quality_id', 'url', 'page_title_en', 'page_title_ru', 'page_title_ua', 'meta_title_en', 'meta_title_ru', 'meta_title_ua', 'meta_description_en', 'meta_description_ru', 'meta_description_ua', 'meta_keywords_en', 'meta_keywords_ru', 'meta_keywords_ua', 'summary_en', 'summary_ru', 'summary_ua', 'rating', 'rating_count', 'last_purchase_price', 'purchased_quantity', 'average_purchase_price', 'sold_quantity', 'average_sold_price',
    ];

    /**
     * The attributes that should be selected depends on locale from JSON type field.
     *
     * @var array
     */
    public $translatable = ['page_title', 'meta_title', 'meta_description', 'meta_keywords', 'summary'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function brand()
    {
        return $this->belongsTo('App\Models\Brand', 'brands_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function image()
    {
        return $this->hasMany('App\Models\ProductImage', 'products_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function primaryImage()
    {
        return $this->hasOne('App\Models\ProductImage', 'products_id', 'id')->where('is_primary', 1);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function deviceModel()
    {
        return $this->belongsToMany('App\Models\DeviceModel', 'products_models_compatible', 'products_id', 'models_id' );
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function color()
    {
        return $this->belongsTo('App\Models\Color', 'colors_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function quality()
    {
        return $this->belongsTo('App\Models\Quality', 'quality_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'categories_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comment()
    {
        return $this->hasMany('App\Models\ProductComment', 'products_id', 'id')->limit(config('shop.product_comments_count'))->orderByDesc('id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function recentComment()
    {
        return $this->hasMany('App\Models\ProductComment', 'products_id', 'id')->limit(config('shop.product_details_comment_count') + 1)->orderByDesc('id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function vendorProduct()
    {
        return $this->hasMany('App\Models\VendorProduct', 'products_id', 'id');
    }
}
