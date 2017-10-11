<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorProduct extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'vendors_has_products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'vendors_id', 'products_id', 'vendor_product_id', 'vendor_price', 'vendor_stock_quantity',
    ];
}
