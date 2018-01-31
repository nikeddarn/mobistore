<?php
/**
 * Created by PhpStorm.
 * User: nick
 * Date: 08.01.18
 * Time: 13:31
 */

namespace App\Http\Support\Invoices\Handlers;


use App\Contracts\Shop\Invoices\ProductInvoiceHandlerInterface;
use Exception;

class StorageProductInvoiceHandler extends ProductInvoiceHandler implements ProductInvoiceHandlerInterface
{
    /**
     * Add products to invoice by product's id.
     *
     * @param int $productId
     * @param float $price
     * @param int $quantity
     * @param int|null $warranty
     * @return bool
     */
    public function addProducts(int $productId, float $price, int $quantity, int $warranty = null): bool{

        assert(is_int($productId) && $quantity > 0, 'Product id must be positive integer');
        assert(is_float($price) && $quantity > 0, 'Product price must be positive float');
        assert(is_int($quantity) && $quantity > 0, 'Product quantity must be positive integer');
        assert(is_int($warranty) && $quantity > 0, 'Product warranty must be positive integer');

        try{
            $this->databaseManager->beginTransaction();

            $this->decreaseStorageProductStock($productId, $price, $quantity);
            parent::addProducts($productId, $price, $quantity, $warranty);

            $this->databaseManager->commit();
            return true;
        } catch(Exception $e){
            $this->databaseManager->rollback();
            return false;
        }
    }

    /**
     * Remove products from invoice by product's id.
     *
     * @param int $productId
     * @param int $quantity
     * @return bool
     */
    public function removeProducts(int $productId, int $quantity): bool{

        assert(is_int($productId) && $quantity > 0, 'Product id must be positive integer');
        assert(is_int($quantity) && $quantity > 0, 'Product quantity must be positive integer');

        try{
            $this->databaseManager->beginTransaction();

            $this->increaseStorageProductStock($productId, parent::getSubtractingProductPrice($productId), $quantity);
            parent::removeProducts($productId, $quantity);

            $this->databaseManager->commit();
            return true;
        } catch(Exception $e){
            $this->databaseManager->rollback();
            return false;
        }
    }

    /**
     * Increase count of storage product.
     * Calculate new average incoming price of product.
     *
     * @param int $productId
     * @param float $price
     * @param int $quantity
     * @return bool
     */
    protected function increaseStorageProductStock(int $productId, float $price, int $quantity = 1):bool 
    {
        $storageProduct = $this->invoice->incomingStorage()->storageProduct()->firstOrNew(['products_id' => $productId]);

        $storageProduct->stock_quantity += $quantity;

        $storageProduct->average_incoming_price = $this->calculateAveragePrice($storageProduct->average_incoming_price, $storageProduct->purchased_quantity, $price, $quantity);
        $storageProduct->purchased_quantity += $quantity;
        
        $storageProduct->save();
        
        return true;
    }

    /**
     * Decrease count of storage product.
     * Calculate new average incoming price of product.
     *
     * @param int $productId
     * @param float $price
     * @param int $quantity
     * @return bool
     */
    protected function decreaseStorageProductStock(int $productId, float $price, int $quantity = 1)
    {
        $storageProduct = $this->invoice->incomingStorage()->storageProduct()->where('products_id', $productId)->first();
        
        if (!$storageProduct || $storageProduct->stock_quantity < $quantity){
            return false;
        }

        $storageProduct->stock_quantity -= $quantity;

        $storageProduct->average_outgoing_price = $this->calculateAveragePrice($storageProduct->average_outgoing_price, $storageProduct->sold_quantity, $price, $quantity);
        $storageProduct->sold_quantity += $quantity;
        
        $storageProduct->save();

        return true;
    }

    /**
     * Calculate new average price of product.
     *
     * @param float $averagePrice
     * @param int $countedQuantity
     * @param float $operationPrice
     * @param int $operationQuantity
     * @return float
     */
    private function calculateAveragePrice(float $averagePrice, int $countedQuantity, float $operationPrice, int $operationQuantity): float
    {
        return $countedQuantity ? ($countedQuantity * $averagePrice + $operationQuantity * $operationPrice) / ($countedQuantity + $operationQuantity) : $operationPrice;
    }
}