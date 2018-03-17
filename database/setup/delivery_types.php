<?php

/**
 * Array of delivery types.
 */

use App\Contracts\Shop\Delivery\DeliveryTypesInterface;

return [
    DeliveryTypesInterface::COURIER => ['title_en' => 'Courier delivery', 'title_ru' => 'Курьерская доставка', 'title_ua' => 'Кур\'єрська доставка'],

    DeliveryTypesInterface::POST => ['title_en' => 'Post delivery', 'title_ru' => 'Доставка почтой', 'title_ua' => 'Доставка поштою'],
];