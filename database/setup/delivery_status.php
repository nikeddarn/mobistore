<?php

/**
 * Array of delivery statuses.
 */

use App\Contracts\Shop\Delivery\DeliveryStatusInterface;

return [
    DeliveryStatusInterface::PROCESSING => ['title_en' => 'Processing', 'title_ru' => 'Обрабатывается', 'title_ua' => 'Обробляється', 'badge_class' => 'warning'],

    DeliveryStatusInterface::ORDERED => ['title_en' => 'Ordered', 'title_ru' => 'Заказан', 'title_ua' => 'Замовлен', 'badge_class' => 'info'],

    DeliveryStatusInterface::COLLECTED => ['title_en' => 'Collected', 'title_ru' => 'Собран', 'title_ua' => 'Зібран', 'badge_class' => 'info'],

    DeliveryStatusInterface::DELIVERING => ['title_en' => 'Delivering', 'title_ru' => 'На доставке', 'title_ua' => 'На доставці', 'badge_class' => 'info'],

    DeliveryStatusInterface::DELIVERED => ['title_en' => 'Delivered', 'title_ru' => 'Получен', 'title_ua' => 'Отриман', 'badge_class' => 'success'],

    DeliveryStatusInterface::CANCELLED => ['title_en' => 'Cancelled', 'title_ru' => 'Отменен', 'title_ua' => 'Скасован', 'badge_class' => 'alert'],
];