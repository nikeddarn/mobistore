<?php

/**
 * Array of invoice statuses.
 */

use App\Contracts\Shop\Invoices\InvoiceStatusInterface;

return [
    InvoiceStatusInterface::PROCESSING => ['title_en' => 'Processing', 'title_ru' => 'Обрабатывается', 'title_ua' => 'Обробляється', 'badge_class' => 'warning'],

    InvoiceStatusInterface::FINISHED => ['title_en' => 'Finished', 'title_ru' => 'Завершен', 'title_ua' => 'завершений', 'badge_class' => 'success'],

    InvoiceStatusInterface::CANCELLED => ['title_en' => 'Cancelled', 'title_ru' => 'Отменен', 'title_ua' => 'Скасован', 'badge_class' => 'alert'],
];