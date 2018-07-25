<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserActiveReclamation extends Model
{
    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'user_active_reclamations';

    /**
     * Array of composite primary keys.
     *
     * @var array
     */
    protected $primaryKey = 'reclamations_id';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'users_id', 'id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function reclamation()
    {
        return $this->belongsTo('App\Models\Reclamation', 'reclamations_id', 'id');
    }
}
