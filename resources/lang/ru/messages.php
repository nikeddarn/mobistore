<?php

use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Contracts\Shop\Roles\UserRolesInterface;

/**
 * User messages' text
 */

return [
    'invoice' => [

        InvoiceTypes::ORDER => [

            'created' => [
                'title' => 'Ваш заказ принят',
                'message' => 'Заказ номер :id на сумму :sum грн принят и обрабатывается. Ориентировочная <a href="' . route('delivery.show') . '" class="text-gray">дата доставки</a>  <nobr>:delivery</nobr>.<br/>Следите за сообщениями в личном кабинете сайта. Наши менеджеры свяжутся с вами.',
            ],

            'collected' => [
                'title' => 'Ваш заказ собран',
                'message' => 'Заказ номер :id собран. Ориентировочная <a href="' . route('delivery.show') . '" class="text-gray">дата доставки</a>  <nobr>:delivery</nobr>.<br/>Следите за статусом доставки в личном кабинете сайта.',
            ],

            'partially_collected' => [
                'title' => 'Ваш заказ частично собран',
                'message' => 'Заказ номер :id изменен. Некоторые товары отсутствуют на складе.<br/>Ориентировочная <a href="' . route('delivery.show') . '" class="text-gray">дата доставки</a>  <nobr>:delivery</nobr>.<br/>Следите за статусом доставки в личном кабинете сайта.',
            ],

            'cancelled' => [
                'title' => 'Ваш заказ отменен',
                'message' => 'Заказ номер :id отменен. Заказываемый товар отсутствует на складе',
            ],
        ],

        InvoiceTypes::PRE_ORDER => [

            'created' => [
                'title' => 'Ваш предварительный заказ принят',
                'message' => 'Предварительный заказ номер :id на сумму :sum грн принят и обрабатывается. Ориентировочная <a href="' . route('delivery.show') . '" class="text-gray">дата доставки</a>  <nobr>:delivery</nobr>.<br/>Следите за сообщениями в личном кабинете сайта. Наши менеджеры свяжутся с вами.',
            ],

            'collected' => [
                'title' => 'Ваш предварительный заказ собран',
                'message' => 'Заказ номер :id собран. Ориентировочная <a href="' . route('delivery.show') . '" class="text-gray">дата доставки</a>  <nobr>:delivery</nobr>.<br/>Следите за статусом доставки в личном кабинете сайта.',
            ],

            'partially_collected' => [
                'title' => 'Ваш предварительный заказ частично собран',
                'message' => 'Заказ номер :id изменен. Некоторые товары отсутствуют на складе.<br/>Ориентировочная <a href="' . route('delivery.show') . '" class="text-gray">дата доставки</a>  <nobr>:delivery</nobr>.<br/>Следите за статусом доставки в личном кабинете сайта.',
            ],

            'cancelled' => [
                'title' => 'Ваш предварительный заказ отменен',
                'message' => 'Заказ номер :id отменен. Заказываемый товар отсутствует на складе',
            ],
        ],
    ],

    'manager' => [

        UserRolesInterface::VENDOR_MANAGER => [
            'created'  => 'Заказ номер :id на сумму $:sum от поставщика :vendor',
        ],

        UserRolesInterface::STOREKEEPER => [
            'created'  => 'Заказ номер :id со склада :store',
        ],
    ],
];