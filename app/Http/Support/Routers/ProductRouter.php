<?php
/**
 * Product router
 */

namespace App\Http\Support\Routers;


use App\Models\Storage;
use App\Models\Vendor;

class ProductRouter
{
    /**
     * @var Storage
     */
    private $storage;

    /**
     * @var Vendor
     */
    private $vendor;

    /**
     * ProductRouter constructor.
     * @param Storage $storage
     * @param Vendor $vendor
     */
    public function __construct(Storage $storage, Vendor $vendor)
    {
        $this->storage = $storage;
        $this->vendor = $vendor;
    }

    /**
     * Define storage for collecting user order.
     *
     * @param array $orderStoragesId
     * @param int $deliveryCity
     * @return int
     */
    public function defineCollectingOrderStorage(array $orderStoragesId, int $deliveryCity = null): int
    {
        // select single possible storage
        if (count($orderStoragesId) === 1) {
            return $orderStoragesId[0];
        }

        // retrieve order storages
        $orderStorages = $this->storage->whereIn('id', $orderStoragesId)->orderByDesc('is_main')->get();

        if ($deliveryCity) {

            // define storage that placed in delivery city
            $deliveryCityStorage = $orderStorages->where('cities_id', $deliveryCity)->first();

            if ($deliveryCityStorage) {
                return $deliveryCityStorage->id;
            }

            /**
             * ToDo: Define storage nearest to delivery city
             */
        }

        // get first storage
        return $orderStorages->first()->id;
    }

    /**
     * Define storage for collecting user order.
     *
     * @param array $orderVendorsId
     * @param int $deliveryCity
     * @return int
     */
    public function defineCollectingPreOrderStorage(array $orderVendorsId, int $deliveryCity = null): int
    {
        // retrieve all storages
        $allStorages = $this->storage->orderByDesc('is_main')->get();

        // select single possible storage
        if ($allStorages->count() === 1) {
            return $allStorages->first()->id;
        }

        if ($deliveryCity) {

            // define storage that placed in delivery city
            $deliveryCityStorage = $allStorages->where('cities_id', $deliveryCity)->first();

            if ($deliveryCityStorage) {
                return $deliveryCityStorage->id;
            }

            /**
             * ToDo: Define storage nearest to delivery city
             */

            // retrieve possible vendors
            $orderVendorsCityId = $this->vendor->whereIn('id', $orderVendorsId)->get()->pluck('cities_id')->toArray();
            // define storage that placed with one of order vendors in same city
            $vendorCityStorage = $allStorages->whereIn('cities_id', $orderVendorsCityId)->first();

            if ($vendorCityStorage) {
                return $vendorCityStorage;
            }
        }

        // get first storage
        return $allStorages->first();
    }
}