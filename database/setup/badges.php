<?php

/**
 * Array of device colors.
 */

use App\Contracts\Shop\Badges\BadgeTypes;

return [

    BadgeTypes::NEW => ['title_en' => 'New product', 'title_ru' => 'Новый товар', 'title_ua' => 'Новий товар'],
    BadgeTypes::PRICE_DOWN => ['title_en' => 'Price down', 'title_ru' => 'Снижение цены', 'title_ua' => 'Зниження ціни'],
    BadgeTypes::ENDING => ['title_en' => 'Running out', 'title_ru' => 'Товар заканчивается', 'title_ua' => 'Товар закінчується'],
    BadgeTypes::SALE => ['title_en' => 'Sale', 'title_ru' => 'Распродажа', 'title_ua' => 'Розпродаж'],
    BadgeTypes::BEST_SELLER => ['title_en' => 'Best sale', 'title_ru' => 'Топ продаж', 'title_ua' => 'Топ продажів'],
];