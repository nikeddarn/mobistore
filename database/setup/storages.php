<?php

/**
* Array of product storages
*/

use App\Models\City;

return [
    [
        'cities_id' => City::where('title_en', 'Kyiv')->first()->id,
        'title_en' => 'Kyiv',
        'title_ru' => 'Киев',
        'title_ua' => 'Київ',
        'is_main' => 1,
    ],
];

