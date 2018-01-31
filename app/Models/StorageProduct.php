<?php

namespace App\Models;

use App\Models\Traits\CompositeKeys;
use Illuminate\Database\Eloquent\Model;

class StorageProduct extends Model
{
    use CompositeKeys;

    /**
     * Table name.
     *
     * @var string
     */
    protected $table = 'storage_products';

    /**
     * Array of composite primary keys.
     *
     * @var array
     */
    protected $primaryKey = ['storages_id', 'products_id'];

    /**
     * Non auto incrementing primary key.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'storages_id', 'products_id', 'stock_quantity', 'average_incoming_price', 'purchased_quantity', 'average_outgoing_price', 'sold_quantity',];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function storage()
    {
        return $this->belongsTo('App\Models\Storage', 'storages_id', 'id');
    }
}
