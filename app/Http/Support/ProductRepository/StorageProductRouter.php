<?php
/**
 * Multi storage products routing
 */

namespace App\Http\Support\ProductRepository;


use App\Models\Storage;
use Illuminate\Support\Collection;

class StorageProductRouter
{
    /**
     * @var Storage
     */
    private $storage;


    /**
     * StorageProductRouter constructor.
     *
     * @param Storage $storage
     */
    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Get storage id that will serve given invoice.
     *
     * @param Collection $invoiceProducts
     * @return int
     */
    public function defineInvoiceStorage(Collection $invoiceProducts):int
    {
        if ($this->storage->count() === 1){
            return $this->storage->first()->id;
        }else{
            return $this->storage->where('is_main')->first()->id;
        }
    }
}