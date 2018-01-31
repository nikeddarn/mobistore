<?php

/**
 * Array of invoice types.
 */

use App\Contracts\Shop\Invoices\InvoiceAmountTypes;

return [
    InvoiceAmountTypes::ORDER_AMOUNT => ['title_en' => 'Order price', 'title_ru' => 'Сумма заказа', 'title_ua' => 'Сума замовлення'],
    InvoiceAmountTypes::DELIVERY_AMOUNT => ['title_en' => 'Cost of delivery', 'title_ru' => 'Стоимость доставки', 'title_ua' => 'Вартість доставки'],
    InvoiceAmountTypes::PAYMENT_AMOUNT => ['title_en' => 'Amount of payment', 'title_ru' => 'Сумма платежа', 'title_ua' => 'Сума платежу'],
    ];