<?php

namespace App\Models;

use App\Models\Traits\Translatable;
use Illuminate\Database\Eloquent\Model;

class ReclamationStatus extends Model
{
    use Translatable;
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'reclamation_status';

    /**
     * Non auto incrementing primary key.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'title_en', 'title_ru', 'title_ua', 'badge_class',
    ];

    /**
     * The attributes that should be selected depends on locale from JSON type field.
     *
     * @var array
     */
    public $translatable = ['title'];
}
