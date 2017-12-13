<?php

namespace App\Models;

use App\Models\Traits\Translatable;
use Illuminate\Database\Eloquent\Model;

class Storage extends Model
{
    use Translatable;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'storages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title_en', 'title_ru', 'title_ua',
    ];

}
