<?php

/**
 * Array of device colors.
 */

use App\Contracts\Shop\Badges\BadgeTypes;

return [

    BadgeTypes::NEW => ['title_en' => 'New product', 'title_ru' => 'Новый товар', 'title_ua' => 'Новий товар'],
    BadgeTypes::PRICE_DOWN => ['title_en' => 'Price down', 'title_ru' => 'Снижение цены', 'title_ua' => 'Зниження ціни'],
    BadgeTypes::ACTION => ['title_en' => 'Action', 'title_ru' => 'Акция', 'title_ua' => 'Акція'],
    BadgeTypes::ENDING => ['title_en' => 'Running out', 'title_ru' => 'Товар заканчивается', 'title_ua' => 'Товар закінчується'],
];