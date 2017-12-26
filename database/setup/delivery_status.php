<?php

/**
 * Array of delivery statuses.
 */

use App\Contracts\Shop\Delivery\DeliveryStatus;

return [

    DeliveryStatus::PROCESSING => ['title_en' => 'Order is being processed', 'title_ru' => 'Заказ обрабатывается', 'title_ua' => 'Замовлення обробляється'],
    DeliveryStatus::ORDERED => ['title_en' => 'Items ordered', 'title_ru' => 'Товар заказан', 'title_ua' => 'Товар замовлений'],
    DeliveryStatus::PARTIALLY_ORDERED => ['title_en' => 'Items partially ordered', 'title_ru' => 'Товар частично  заказан', 'title_ua' => 'Товар частково замовлений'],
    DeliveryStatus::SHIPPING => ['title_en' => 'The goods are delivering to the store', 'title_ru' => 'Товар доставляется на склад', 'title_ua' => 'Товар доставляється на склад'],
    DeliveryStatus::CUSTOMER_DELIVERY => ['title_en' => 'Goods is on delivery to the customer', 'title_ru' => 'Товар на доставке клиенту', 'title_ua' => 'Товар на доставці клієнту'],
];