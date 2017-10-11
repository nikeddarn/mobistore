<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quality extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'quality';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title_en', 'title_ru', 'title_ua',
    ];
}
