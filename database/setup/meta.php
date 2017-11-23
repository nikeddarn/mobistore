<?php

/**
 * Array of common page's Meta Data by url
 */

$bue_en = trans('meta.phrases.bue', [], 'en');
$features_en = trans('meta.phrases.features', [], 'en');
$wholesaleRetail_en = trans('meta.phrases.wholesale_and_retail', [], 'en');
$originalCopy_en = trans('meta.phrases.original_and_copy', [], 'en');

$bue_ru = trans('meta.phrases.bue', [], 'ru');
$features_ru = trans('meta.phrases.features', [], 'ru');
$wholesaleRetail_ru = trans('meta.phrases.wholesale_and_retail', [], 'ru');
$originalCopy_ru = trans('meta.phrases.original_and_copy', [], 'ru');

$bue_ua = trans('meta.phrases.bue', [], 'ua');
$features_ua = trans('meta.phrases.features', [], 'ua');
$wholesaleRetail_ua = trans('meta.phrases.wholesale_and_retail', [], 'ua');
$originalCopy_ua = trans('meta.phrases.original_and_copy', [], 'ua');

return [
    [
        'url' => 'brand',
        'page_title_en' => 'Brands of spare parts for smartphones and tablets',
        'page_title_ru' => 'Бренды запчастей к смартфонам и планшетам',
        'page_title_ua' => 'Бренди запчастин до смартфонів і планшетів',
        'meta_title_en' => 'Brands of spare parts for smartphones and tablets ' . $wholesaleRetail_en,
        'meta_title_ru' => 'Бренды запчастей к смартфонам и планшетам ' . $wholesaleRetail_ru,
        'meta_title_ua' => 'Бренди запчастин до смартфонів і планшетів ' . $wholesaleRetail_ua,
        'meta_description_en' => 'Brands of spare parts for smartphones and tablets. ' . ucfirst($wholesaleRetail_en). ', ' . $originalCopy_en . '. ' . ucfirst($features_en) . ' &#8212; ' . $bue_en,
        'meta_description_ru' => 'Бренды запчастей к смартфонам и планшетам. ' . ucfirst($wholesaleRetail_ru). ', ' . $originalCopy_ru . '. ' . ucfirst($features_ru) . ' &#8212; ' . $bue_ru,
        'meta_description_ua' => 'Бренди запчастин до смартфонів і планшетів. ' . ucfirst($wholesaleRetail_ua). ', ' . $originalCopy_ua . '. ' . ucfirst($features_ua) . ' &#8212; ' . $bue_ua,
        'meta_keywords_en' => 'Smartphones spare parts, tablets spare parts, brands of smartphones spare parts, brands of tablets spare parts',
        'meta_keywords_ru' => 'Запчасти для смартфонов, запасные части для планшетов, бренды запасных частей для смартфонов, бренды запасных частей для планшетов',
        'meta_keywords_ua' => 'Запасні частини для смартфонів, запчастини для планшетів, бренди запчастин для смартфонів, бренди запчастин для планшетів',
        'summary_en' => '<p>Store '. config ('app.name') . ' offers spare parts for smartphones and tablets of most famous brands. The offered spare parts are originals of this brand or a high-quality copy. At us you will find spare parts for popular phones in Ukraine of all well-known manufacturers.</p><p>Spare parts for Apple phones are in great demand. In our store you can buy new and removed original spare parts Apple.</p>',
        'summary_ru' => '<p>Магазин ' . config ('app.name'). ' предлагает запчасти к смартфонам и планшетам большинства известных брендов. Предлагаемые запчасти являются оригиналами данного бренда или высококачественной копией. У нас вы найдете запчасти для популярных в Украине телефонов всех известных производителей.</p><p>Большим спросом традиционно пользуются запчасти для телефонов Apple. Унас вы можете купить новые и снятые оригинальные запчасти Apple.</p>',
        'summary_ua' => '<p>Магазин '. config ('app.name'). ' пропонує запчастини до смартфонів і планшетів більшості відомих брендів. Пропоновані запчастини є оригіналами даного бренду або високоякісними копіями. У нас ви знайдете запчастини для популярних в Україні телефонів усіх відомих виробників.</p><p>Великим попитом традиційно користуються запчастини для телефонів Apple. Унас ви можете купити нові і зняті оригінальні запчастини Apple.</p>',
    ],
    [
        'url' => 'category',
        'page_title_en' => 'Spare parts for smartphones and tablets by category',
        'page_title_ru' => 'Запчасти к смартфонам и планшетам по категориям',
        'page_title_ua' => 'Запчастини до смартфонів і планшетів за категоріями',
        'meta_title_en' => 'Buy wholesale and retail spare parts for smartphones and tablets by category',
        'meta_title_ru' => 'Купить оптом и в розницу запчасти к смартфонам и планшетам по категориям',
        'meta_title_ua' => 'Купити оптом і в роздріб запчастини до смартфонів і планшетів за категоріями',
        'meta_description_en' => 'Spare parts for smartphones and tablets by category. ' . ucfirst($wholesaleRetail_ru). ', ' . $originalCopy_en . '. ' . ucfirst($features_en) . ' &#8212; ' . $bue_en,
        'meta_description_ru' => 'Запчасти к смартфонам и планшетам по категориям. ' . ucfirst($wholesaleRetail_ru). ', ' . $originalCopy_ru . '. ' . ucfirst($features_ru) . ' &#8212; ' . $bue_ru,
        'meta_description_ua' => 'Запчастини до смартфонів і планшетів за категоріями. ' . ucfirst($wholesaleRetail_ua). ', ' . $originalCopy_ua . '. ' . ucfirst($features_ua) . ' &#8212; ' . $bue_ua,
        'meta_keywords_en' => 'Smartphones spare parts, tablets spare parts, categories of smartphones spare parts, categories of tablets spare parts',
        'meta_keywords_ru' => 'Запчасти для смартфонов, запасные части для планшетов, категории запасных частей для смартфонов, категории запасных частей для планшетов',
        'meta_keywords_ua' => 'Запасні частини для смартфонів, запчастини для планшетів, категорії запчастин для смартфонів, категорії запчастин для планшетів',
        'summary_en' => 'Score ' . config ('app.name') . ' offers spare parts of any type to smartphones and tablets. The offered spare parts are originals of this brand or a high-quality copy. In our store you will find all types of spare parts for popular phones in Ukraine of all known manufacturers.</p>',
        'summary_ru' => '<p>Магазин ' . config ('app.name') . ' предлагает запчасти любого типа к смартфонам и планшетам. Предлагаемые запчасти являются оригиналами данного бренда или высококачественной копией. У нас вы найдете все типы запчастей для популярных в Украине телефонов всех известных производителей.</p>',
        'summary_ua' => '<p>Магазин ' . config ('app.name') . ' пропонує запчастини будь-якого типу до смартфонів і планшетів. Пропоновані запчастини є оригіналами даного бренду або високоякісної копією. У нас ви знайдете всі типи запчастин для популярних в Україні телефонів усіх відомих виробників.</p>',
    ],
];