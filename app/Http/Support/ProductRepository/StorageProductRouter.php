<?php
/**
 * Multi storage products routing
 */

namespace App\Http\Support\ProductRepository;

use Illuminate\Support\Collection;

class StorageProductRouter
{


    /**
     * Define storage for collecting user order.
     *
     * @param Collection $possibleStorages
     * @return int
     */
    public function defineCollectingOrderStorage(Collection $possibleStorages): int
    {
        // select single possible storage
        if ($possibleStorages->count() === 1) {
            return $possibleStorages->first()->id;
        }

        // select main storage
        $mainStorage = $possibleStorages->where('is_main', '=', 1)->first();
        if ($mainStorage) {
            return $mainStorage->id;
        }

        /**
         * ToDo: Define storage depends on user delivery city
         */
        return $possibleStorages->first();
    }
}