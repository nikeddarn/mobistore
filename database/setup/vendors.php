<?php

/**
* Array of product vendors
*/

use App\Models\City;

return [
    [
        'title' => 'Lankin',
        'cities_id' => City::where('title_en', 'Moscow')->first()->id,
    ],
];

