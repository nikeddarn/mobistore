<?php
/**
 * Storage product statistics handler.
 */

namespace App\Http\Support\Statistics\Product;


use App\Models\StorageProduct;

class StorageProductStatistics
{
    /**
     * Increase sold storage product count.
     *
     * @param StorageProduct $storageProduct
     * @param int $quantity
     * @param float $price
     * @return bool
     */
    public function increasePurchasedStorageProductCount(StorageProduct $storageProduct, int $quantity, float $price):bool
    {
        // calculate average purchase prise
        $avgPurchasePrice = ($storageProduct->average_incoming_price * $storageProduct->purchased_quantity + $price * $quantity) / ($storageProduct->purchased_quantity + $quantity);

        // update purchase price
        $storageProduct->average_incoming_price = $avgPurchasePrice;

        // increase total of purchased product count
        $storageProduct->purchased_quantity += $quantity;

        // save StorageProduct
        return $storageProduct->save();
    }

    /**
     * Decrease sold storage product count.
     *
     * @param StorageProduct $storageProduct
     * @param int $quantity
     * @param float $price
     * @return bool
     */
    public function decreasePurchasedStorageProductCount(StorageProduct $storageProduct, int $quantity, float $price):bool
    {
        // calculate average purchase prise
        $avgPurchasePrice = ($storageProduct->average_incoming_price * $storageProduct->purchased_quantity - $price * $quantity) / ($storageProduct->purchased_quantity - $quantity);

        // update purchase price
        $storageProduct->average_incoming_price = $avgPurchasePrice;

        // increase total of purchased product count
        $storageProduct->purchased_quantity -= $quantity;

        // save StorageProduct
        return $storageProduct->save();
    }

    /**
     * Increase sold storage product count.
     *
     * @param StorageProduct $storageProduct
     * @param int $quantity
     * @param float $price
     * @return bool
     */
    public function increaseSoldStorageProductCount(StorageProduct $storageProduct, int $quantity, float $price):bool
    {
        // calculate average sold prise
        $avgSoldPrice = ($storageProduct->average_outgoing_price * $storageProduct->sold_quantity + $price * $quantity) / ($storageProduct->sold_quantity + $quantity);

        // update purchase price
        $storageProduct->average_outgoing_price = $avgSoldPrice;

        // increase total of purchased product count
        $storageProduct->sold_quantity += $quantity;

        return $storageProduct->save();
    }

    /**
     * Decrease sold storage product count.
     *
     * @param StorageProduct $storageProduct
     * @param int $quantity
     * @param float $price
     * @return bool
     */
    public function decreaseSoldStorageProductCount(StorageProduct $storageProduct, int $quantity, float $price):bool
    {
        // calculate average sold prise
        $avgSoldPrice = ($storageProduct->average_outgoing_price * $storageProduct->sold_quantity - $price * $quantity) / ($storageProduct->sold_quantity - $quantity);

        // update purchase price
        $storageProduct->average_outgoing_price = $avgSoldPrice;

        // increase total of purchased product count
        $storageProduct->sold_quantity -= $quantity;

        return $storageProduct->save();
    }

    /**
     * Increase total sold product count.
     *
     * @param StorageProduct $storageProduct
     * @param int $quantity
     * @return bool
     */
    public function increaseTotalSoldProductCount(StorageProduct $storageProduct, int $quantity):bool
    {
        $product = $storageProduct->product()->first();

        $product->sold_quantity += $quantity;

        return $product->save();
    }

    /**
     * Increase total sold product count.
     *
     * @param StorageProduct $storageProduct
     * @param int $quantity
     * @return bool
     */
    public function decreaseTotalSoldProductCount(StorageProduct $storageProduct, int $quantity):bool
    {
        $product = $storageProduct->product()->first();

        $product->sold_quantity -= $quantity;

        return $product->save();
    }
}