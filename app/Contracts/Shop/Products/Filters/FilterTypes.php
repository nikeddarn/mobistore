<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 18.11.17
 * Time: 17:50
 */

namespace App\Contracts\Shop\Products\Filters;


interface FilterTypes
{
    /**
     * Filter by categories.
     */
    const CATEGORY = 'category';

    /**
     * Filter by brands.
     */
    const BRAND = 'brand';

    /**
     * Filter by models.
     */
    const MODEL = 'model';

    /**
     * Filter by quality of products.
     */
    const QUALITY = 'quality';

    /**
     * Filter by colors of products.
     */
    const COLOR = 'color';
}