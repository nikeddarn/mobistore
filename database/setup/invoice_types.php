<?php

/**
 * Array of invoice types.
 */

use App\Contracts\Shop\Invoices\InvoiceTypes;

return [

    // cart product invoice
    InvoiceTypes::USER_CART => ['title_en' => 'User cart', 'title_ru' => 'Корзина пользователя', 'title_ua' => 'Кошик користувача'],


    // product invoices
    InvoiceTypes::USER_ORDER => ['title_en' => 'User order', 'title_ru' => 'Заказ пользователя', 'title_ua' => 'Замовлення користувача'],

    InvoiceTypes::USER_PRE_ORDER => ['title_en' => 'User pre-order', 'title_ru' => 'Предварительный заказ пользователя', 'title_ua' => 'Попереднє замовлення користувача'],

    InvoiceTypes::VENDOR_ORDER => ['title_en' => 'Vendor order', 'title_ru' => 'Заказ поставщика', 'title_ua' => 'Замовлення постачальника'],

    InvoiceTypes::USER_RETURN_ORDER => ['title_en' => 'Return of the user\'s order', 'title_ru' => 'Возврат заказа пользователя', 'title_ua' => 'Повернення замовлення користувача'],

    InvoiceTypes::VENDOR_RETURN_ORDER => ['title_en' => 'Return of the vendor\'s order', 'title_ru' => 'Возврат заказа поставщика', 'title_ua' => 'Повернення замовлення постачальника'],

    InvoiceTypes::STORAGE_REMOVE_PRODUCT => ['title_en' => 'Moving between storages', 'title_ru' => 'Перемещение между складами', 'title_ua' => 'Переміщення між складами'],


    // product and reclamation invoices
    InvoiceTypes::REMOVE_PRODUCT_TO_RECLAMATION => ['title_en' => 'Replace from product to reclamation department', 'title_ru' => 'Перемещение со склада в гарантийный отдел', 'title_ua' => 'Переміщення зі складу в гарантійний відділ'],

    InvoiceTypes::REMOVE_RECLAMATION_TO_PRODUCT => ['title_en' => 'Replace from reclamation to product department', 'title_ru' => 'Перемещение с гарантийного отдела на склад', 'title_ua' => 'Переміщення з гарантійного відділу на склад'],

    InvoiceTypes::USER_EXCHANGE_RECLAMATION => ['title_en' => 'Exchange user defect product', 'title_ru' => 'Обмен гарантийного товара пользователя', 'title_ua' => 'Обмін гарантійного товару постачальника'],

    InvoiceTypes::VENDOR_EXCHANGE_RECLAMATION => ['title_en' => 'Exchange vendor defect product', 'title_ru' => 'Обмен гарантийного товара пользователя', 'title_ua' => 'Обмін гарантійного товару постачальника'],


    // reclamation invoices
    InvoiceTypes::USER_RECLAMATION => ['title_en' => 'Receive user\'s defect product', 'title_ru' => 'Получение гарантийного товара пользователя', 'title_ua' => 'Отримання гарантійного товару користувача'],

    InvoiceTypes::VENDOR_RECLAMATION => ['title_en' => 'Send defect product to vendor', 'title_ru' => 'Отправка гарантийного товара поставщику', 'title_ua' => 'Відправка гарантійного товару постачальнику'],

    InvoiceTypes::USER_RETURN_RECLAMATION => ['title_en' => 'Return non-warranty product to user', 'title_ru' => 'Возврат не гарантийного товара пользователю', 'title_ua' => 'Повернення не гарантійний товару користувачу'],

    InvoiceTypes::VENDOR_RETURN_RECLAMATION => ['title_en' => 'Return of non-warranty product from vendor', 'title_ru' => 'Возврат не гарантийного товара от поставщика', 'title_ua' => 'Повернення не гарантійного товару від постачальника'],

    InvoiceTypes::STORAGE_REMOVE_RECLAMATION => ['title_en' => 'Moving product between reclamation departments', 'title_ru' => 'Перемещение брака между гарантийными отделами', 'title_ua' => 'Переміщення браку між гарантійними відділами'],

    InvoiceTypes::USER_WRITE_OFF_RECLAMATION => ['title_en' => 'Write-off user\'s defect product', 'title_ru' => 'Списание гарантийного товара пользователя', 'title_ua' => 'Списання гарантійного товару користувача'],

    InvoiceTypes::VENDOR_WRITE_OFF_RECLAMATION => ['title_en' => 'Write-off vendor\'s defect product', 'title_ru' => 'Списание гарантийного товара поставщика', 'title_ua' => 'Списання гарантійного товару постачальника'],


    // payment invoices
    InvoiceTypes::USER_PAYMENT => ['title_en' => 'User\'s payment', 'title_ru' => 'Оплата пользователя', 'title_ua' => 'Оплата користувача'],

    InvoiceTypes::VENDOR_PAYMENT => ['title_en' => 'Payment to vendor', 'title_ru' => 'Оплата поставщику', 'title_ua' => 'Оплата постачальнику'],

    InvoiceTypes::USER_RETURN_PAYMENT => ['title_en' => 'Return payment to user', 'title_ru' => 'Возврат оплаты пользователю', 'title_ua' => 'Повернення оплати користувачу'],

    InvoiceTypes::VENDOR_RETURN_PAYMENT => ['title_en' => 'Return payment from vendor', 'title_ru' => 'Возврат оплаты от поставщика', 'title_ua' => 'Повернення оплати від постачальника'],

    InvoiceTypes::STORAGE_REMOVE_CASH => ['title_en' => 'Moving money by department', 'title_ru' => 'Перемешение денег по отделам', 'title_ua' => 'Переміщення грошей по відділах'],


    // cost invoices
    InvoiceTypes::VENDOR_DELIVERY_TO_SHIPMENT_PAYMENT => ['title_en' => 'Payment for shipment delivery', 'title_ru' => 'Плата за доставку отгрузки', 'title_ua' => 'Плата за доставку відвантаження'],
];