<?php

/**
 * Shop settings.
 */

use App\Contracts\Currency\ExchangeRateSourcesInterface;
use App\Contracts\Shop\Badges\BadgeTypes;

return [

    // count of products in product list pages.
    'products_per_page' => 12,

    // count of recent products to show.
    'recent_products_show' => 12,

    // count of account items to show.
    'account_items_show' => 12,

    // count of reclamation items to show.
    'warranty_items_show' => 12,

    // count of categories and subcategories filters (filters creating depth).
    'category_filters_depth' => 2,

    // min number of product rating updates to show product rating on page.
    'min_rating_count_to_show' => 5,

    // max count of last comments that will show on product details page.
    'product_details_comment_count' => 3,

    // max count of comments that will show on product comments page.
    'product_comments_count' => 30,

    // max user messages per page
    'user_messages_count' => 12,

    // actual period for unread message
    'show_unread_message_days' => 7,

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

    // sources of currency exchange rates in priority order.
    'exchange_rate_sources' => [
        ExchangeRateSourcesInterface::PB,
        ExchangeRateSourcesInterface::FINANCE,
    ],

    // exchange rate for product price time to live in hours.
    'exchange_rate_ttl' => 4,

    // invoice exchange rate ttl in hours
    'invoice_exchange_rate_ttl' => 72,

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
        BadgeTypes::ACTION => [
            'ttl' => 3,
            'class' => 'success'
        ],
    ],

    // cart ttl in days
   'user_cart_ttl' => 7,

    // Must recalculate user cart prices every day ?
    'recalculate_cart_prices' => true,

    // prices
    'price' => [
        // starting price group on register wholesale user
        'start_wholesale_price_group' => 3,
    ],

    // deliveries
    'delivery' => [

        // pre order default delivery days
        'pre_order' => [
            'min' => 2,
            'max' => 5,
        ],

        // free delivery from user price group
        'free_delivery_price_group' => 3,

        //min invoice sum for free delivery
        'free_delivery_invoice_sum' => 20,

        // local delivery price in usd
        'local_delivery_price' => 2,
    ],

    // max current day delivery time in hours after midnight
    'shipment' => [
        'current_day_delivery_max_time' => [
            'local' => 9,
            'post' => 9,
            'vendor' =>12,
        ]
    ],

];