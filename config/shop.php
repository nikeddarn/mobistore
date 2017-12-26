<?php

/**
 * Shop settings.
 */

use App\Contracts\Currency\ExchangeRateSourcesInterface;
use App\Contracts\Shop\Badges\BadgeTypes;

return [

    // count of products in product list pages.
    'products_per_page' => 12,

    // count of categories and subcategories filters (filters creating depth).
    'category_filters_depth' => 2,

    // min number of product rating updates to show product rating on page.
    'min_rating_count_to_show' => 5,

    // max count of last comments that will show on product details page.
    'product_details_comment_count' => 3,

    // max count of comments that will show on product comments page.
    'product_comments_count' => 30,

    // phones of shop.
    'phones' => '&#9742;&nbsp;067-409-16-65, 063-765-74-08',

    // large image height.
    'large_product_image_height' => 245*5,

    // thumbnail image height.
    'small_product_image_height' => 245*2,

    // image size rate.
    'product_image_size_rate' => 1.3333,

    // watermark color.
    'product_image_watermark_color' => '0x50317EAC',

    // use vendor product price if product is out of own stock.
    'can_use_vendor_price' => true,

    // primary source of currency exchange rates.
    'primary_exchange_rate_source' => ExchangeRateSourcesInterface::PB,

    // exchange rate time to live in hours.
    'exchange_rate_ttl' => 4,

    // exchange rate source timeout
    'exchange_rate_source_timeout' => 500,

    // badges settings. ttl in days. 0 for unlimited.
    'badges' => [
        BadgeTypes::NEW => [
            'ttl' => 5,
            'class' => 'info'
        ],
        BadgeTypes::PRICE_DOWN => [
            'ttl' => 3,
            'class' => 'warning'
        ],
        BadgeTypes::ENDING => [
            'ttl' => 0,
            'class' => 'danger'
        ],
        BadgeTypes::SALE => [
            'ttl' => 3,
            'class' => 'success'
        ],
        BadgeTypes::BEST_SELLER => [
            'ttl' => 3,
            'class' => 'info'
        ],
    ],
];