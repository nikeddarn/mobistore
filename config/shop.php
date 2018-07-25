<?php

/**
 * Shop settings.
 */

use App\Contracts\Shop\Badges\ProductBadgesInterface;
use App\Http\Support\Currency\FinanceExchangeRates;
use App\Http\Support\Currency\PrivatBankExchangeRates;

return [
    // count of items per page for show to user
    'user_items_per_page_count' => [
        'active_items' => 24,
        'all_items' => 12,
    ],

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

    // phones of shop.
    'phones' => '&#9742;&nbsp;067-409-16-65, 063-765-74-08',

    // large image height.
    'large_product_image_height' => 245 * 5,

    // thumbnail image height.
    'small_product_image_height' => 245 * 2,

    // image size rate.
    'product_image_size_rate' => 1.3333,

    // watermark color.
    'product_image_watermark_color' => '0x50317EAC',

    // use vendor product price if product is out of own stock.
    'can_use_vendor_price' => true,

    'exchange_rate' => [
        // sources of currency exchange rates in priority order.
        'sources' => [
            PrivatBankExchangeRates::class,
            FinanceExchangeRates::class,
        ],

        // exchange rate for product price time to live in hours.
        'update_rate_hours' => 4,

        // max days for using
        'valid_stored_rate_days' => 2,
    ],


    // badges settings. ttl in days. 0 for unlimited.
    'badges' => [
        ProductBadgesInterface::NEW => [
            'ttl' => 5,
            'class' => 'info'
        ],
        ProductBadgesInterface::PRICE_DOWN => [
            'ttl' => 3,
            'class' => 'warning'
        ],
        ProductBadgesInterface::ENDING => [
            'ttl' => 0,
            'class' => 'danger'
        ],
        ProductBadgesInterface::ACTION => [
            'ttl' => 3,
            'class' => 'success'
        ],
    ],

    'cart' => [
        // cart ttl in days
        'cart_expire_days' => 7,

        // products cart prices ttl in days
        'cart_product_price_expire_days' => 1,
    ],

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

        //min invoice sum for free delivery in USD
        'free_delivery_invoice_sum' => 20,

        // local delivery price in usd
        'local_delivery_price' => 2,
    ],

    // max current day delivery time in hours after midnight
    'shipment' => [
        'current_day_delivery_max_time' => [
            'local' => 9,
            'post' => 9,
            'vendor' => 12,
        ]
    ],

    // invoices
    'invoice' => [
        // user orders
        'order' => [
            // auto define outgoing storage and create storage invoice
            'create_outgoing_storage_invoice' => true,
            // auto create replacement by storages invoices to collect order products on one storage
            'create_replacement_storage_invoice' => true,
        ],
        // user pre orders
        'pre_order' => [
            // auto define outgoing storage and create storage invoice
            'create_outgoing_storage_invoice' => true,
            // auto create vendor invoices
            'create_vendor_invoice' => true,
            // auto add to nearest shipment
            'auto_add_to_nearest_shipment' => true,
            // correct user invoice(change quantity or cancel invoice) and send user notification if related vendor invoice is not completely collected
            'auto_correct_user_invoice' => true,
        ],
    ],
];