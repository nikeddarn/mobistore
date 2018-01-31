<?php

/**
 * Array of invoice types.
 */

use App\Contracts\Shop\Invoices\InvoiceTypes;

return [

    InvoiceTypes::CART => ['title_en' => 'User cart', 'title_ru' => 'Корзина пользователя', 'title_ua' => 'Кошик користувача'],

    InvoiceTypes::ORDER => ['title_en' => 'User order', 'title_ru' => 'Заказ пользователя', 'title_ua' => 'Замовлення користувача'],
    InvoiceTypes::PRE_ORDER => ['title_en' => 'User pre-order', 'title_ru' => 'Предварительный заказ пользователя', 'title_ua' => 'Попереднє замовлення користувача'],
    InvoiceTypes::RETURN_ORDER => ['title_en' => 'Return of the user\'s order', 'title_ru' => 'Возврат заказа пользователя', 'title_ua' => 'Повернення замовлення користувача'],

    InvoiceTypes::RECLAMATION => ['title_en' => 'The receipt of the guarantee goods', 'title_ru' => 'Прийом гарантийного товара', 'title_ua' => 'Прийом гарантійного товару'],
    InvoiceTypes::EXCHANGE_RECLAMATION => ['title_en' => 'Warranty exchange', 'title_ru' => 'Обмен гарантийного товара', 'title_ua' => 'Обмін гарантійного товару'],
    InvoiceTypes::RETURN_RECLAMATION => ['title_en' => 'Return of non-warranty items', 'title_ru' => 'Возврат не гарантийного товара', 'title_ua' => 'Повернення не гарантійний товару'],
    InvoiceTypes::WRITE_OFF_RECLAMATION => ['title_en' => 'Warranty write-off', 'title_ru' => 'Списание гарантийного товара', 'title_ua' => 'Списання гарантійного товару'],

    InvoiceTypes::PAYMENT => ['title_en' => 'Payment for goods', 'title_ru' => 'Оплата товара', 'title_ua' => 'Оплата товару
'],
    InvoiceTypes::RETURN_PAYMENT => ['title_en' => 'Refund', 'title_ru' => 'Возврат оплаты', 'title_ua' => 'Повернення оплати'],
];