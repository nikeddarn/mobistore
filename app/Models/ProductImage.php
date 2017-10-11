<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'product_images';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'products_id', 'image', 'is_primary',
    ];
}
