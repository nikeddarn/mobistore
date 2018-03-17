<?php
/*
    |--------------------------------------------------------------------------
    | Meta tags Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used in blade templates
    |
    */

return [
    'title' => [
        'shopping_cart' => 'Магазин ' . config('app.name') . ' - корзина пользователя',
        'checkout_order' => 'Магазин ' . config('app.name') . ' - оформление заказа',
        'favourite_products' => 'Фаворитные продукты пользователя',
        'recent_products' => 'Недавние продукты пользователя',
        'action_products' => 'Запчасти к мобильным телефонам и планшетам со скидками',

        // user pages
        'user' => [
            'messages' => 'Магазин ' . config('app.name') . ' - сообщения пользователя',
            'deliveries' => 'Магазин ' . config('app.name') . ' - доставки пользователя',
            'accounts' => 'Магазин ' . config('app.name') . ' - аккаунт пользователя',
            'warranty' => 'Магазин ' . config('app.name') . ' - гарантийное обслуживание пользователя',
            'profile' => [
                'show' => 'Магазин ' . config('app.name') . ' - профиль пользователя',
                'edit' => 'Магазин ' . config('app.name') . ' - редактировать профиль',
            ],
            'password' => 'Магазин ' . config('app.name') . ' - изменение пароля',
        ],
    ],

    'description' => [
        'action_products' => 'Детали к мобильным телефонам и планшетам. Продукты по акции, товары со скидкой - купить в магазине ' . config('app.name'),
    ],

    'keywords' => [
        'action_products' => 'запчасти к мобильным телефонам, запчасти к планшетам, купить оптом и в розницу в Киеве, товары со скидкой, товары по акции',
    ],

    'author' => 'nikeddarn',

    // Phrases in meta
    'phrases' => [
        'bue' => 'купить в магазине ' . config('app.name'),
        'features' => 'высокое качество, продленная гарантия, скидки, доставка по Киеву и Украине',
        'wholesale_and_retail' => 'оптом и в розницу',
        'original_and_copy' => 'оригинальные и копии',
        'buу_for_price' => 'купить в магазине ' . config('app.name') . ' по цене :price гривень',
        'phones' => config('shop.phones'),
        'comments' => [
            'show' => 'Комментарии пользователей',
            'add' => 'Добавить комментарий'
        ],
    ],
];