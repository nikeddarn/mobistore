<?php

/**
 * Array of delivery statuses.
 */

use App\Contracts\Shop\Delivery\DeliveryStatusInterface;

return [
    DeliveryStatusInterface::PROCESSING => ['title_en' => 'Processing', 'title_ru' => 'Обрабатывается', 'title_ua' => 'Обробляється', 'badge_class' => 'warning'],

    DeliveryStatusInterface::COLLECTED => ['title_en' => 'Collected', 'title_ru' => 'Собран', 'title_ua' => 'Зібран', 'badge_class' => 'primary'],

    DeliveryStatusInterface::STORAGE_DELIVERING => ['title_en' => 'Delivery to storage', 'title_ru' => 'Доставка на склад', 'title_ua' => 'Доставка на склад', 'badge_class' => 'info'],

    DeliveryStatusInterface::USER_DELIVERING => ['title_en' => 'Delivery to user', 'title_ru' => 'Доставка пользователю', 'title_ua' => 'Доставка користувачу', 'badge_class' => 'info'],

    DeliveryStatusInterface::POST_DELIVERING => ['title_en' => 'Delivery to the post office', 'title_ru' => 'Доставка на почту', 'title_ua' => 'Доставка на пошту', 'badge_class' => 'info'],

    DeliveryStatusInterface::DELIVERED => ['title_en' => 'Delivered', 'title_ru' => 'Получен', 'title_ua' => 'Отриман', 'badge_class' => 'success'],
];