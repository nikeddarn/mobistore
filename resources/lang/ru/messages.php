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
        ],

        InvoiceTypes::PRE_ORDER => [
            'created' => [
                'title' => 'Ваш предварительный заказ принят',
                'message' => 'Заказ номер :id на сумму :sum грн принят и обрабатывается. Ориентировочная <a href="' . route('delivery.show') . '" class="text-gray">дата доставки</a>  <nobr>:delivery</nobr>.<br/>Следите за сообщениями в личном кабинете сайта. Наши менеджеры свяжутся с вами.',
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