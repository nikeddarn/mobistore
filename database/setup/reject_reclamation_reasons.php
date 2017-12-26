<?php

/**
 * Array of reclamation reasons.
 */


use App\Contracts\Shop\Reclamations\RejectReclamationReasons;

return [

    RejectReclamationReasons::UNIDENTIFIED_PRODUCT => ['title_en' => 'The product is not from our store', 'title_ru' => 'Товар не нашего магазина', 'title_ua' => 'Товар не нашого магазину'],
    RejectReclamationReasons::WARRANTY_EXPIRED => ['title_en' => 'Warranty is missing or has expired', 'title_ru' => 'Гарантия отсутствует или закончилась', 'title_ua' => 'Гарантія відсутня або закінчилася'],
    RejectReclamationReasons::SEAL_BROKEN => ['title_en' => 'The warranty seal is damaged or missing', 'title_ru' => 'Гарантийная пломба повреждена или отсутствует', 'title_ua' => 'Гарантійна пломба пошкоджена або відсутня'],
    RejectReclamationReasons::PROTECTIVE_FILM_REMOVED => ['title_en' => 'The protective film is damaged or was removed', 'title_ru' => 'Защитная пленка повреждена или снималась', 'title_ua' => 'Захисна плівка пошкоджена або знімалася'],
    RejectReclamationReasons::MECHANICAL_DAMAGE => ['title_en' => 'Traces of mechanical damage to the product', 'title_ru' => 'Следы механических повреждений товара', 'title_ua' => 'Сліди механічних пошкоджень товару'],
];