<?php
/**
 * Methods to handle user cart invoice.
 */

namespace App\Http\Support\Invoices\Handlers;

use App\Contracts\Shop\Invoices\Handlers\ProductInvoiceHandlerInterface;
use App\Http\Support\FormatInvoiceProducts;
use App\Models\InvoiceProduct;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ProductInvoiceHandler extends InvoiceHandler implements ProductInvoiceHandlerInterface
{
    use FormatInvoiceProducts;

    /**
     * Get collection of products of invoice.
     *
     * @return Collection
     */
    public function getInvoiceProducts(): Collection
    {
        return $this->invoice->invoiceProduct;
    }

    /**
     * Create array of products data for view.
     *
     * @param string|null $imageUrlPrefix
     * @return array
     */
    public function getFormattedProducts(string $imageUrlPrefix = null):array
    {
        return $this->getFormattedInvoiceProductsData($this->invoice->invoiceProduct, $imageUrlPrefix);
    }

    /**
     * Is product with given id already in cart ?
     *
     * @param int $productId
     * @return bool
     */
    public function productExists(int $productId):bool
    {
        assert($productId > 0, 'Product id must be positive integer');

        return (bool)$this->getInvoiceProduct($productId);
    }

    /**
     * Get total count of products of invoice
     *
     * @return int
     */
    public function getProductsCount():int
    {
        $count = 0;

        $this->invoice->invoiceProduct->each(function (InvoiceProduct $invoiceProduct) use (&$count){
            $count += $invoiceProduct->quantity;
        });

        return $count;
    }

    /**
     * Update prices for all products of invoice.
     *
     * @return void
     */
    public function updateProductsPrices()
    {
        $rate = $this->productPrice->getRate();

        if ($rate) {
            $this->invoice->rate = $rate;
            $this->invoice->save();
        }

        $this->invoice->invoiceProduct->each(function (InvoiceProduct $invoiceProduct){
            $invoiceProduct->price = $this->productPrice->getUserPriceByProductId($invoiceProduct->products_id);
        });
    }

    /**
     * Add products to invoice by product's id.
     *
     * @param int $productId
     * @param float $price
     * @param int $quantity
     * @param int|null $warranty
     * @return int Added products count.
     */
    public function appendProducts(int $productId, float $price, int $quantity = 1, int $warranty = null): int
    {
        assert($productId > 0, 'Product id must be positive integer');
        assert($price > 0, 'Product price must be positive float');
        assert($quantity > 0, 'Product quantity must be positive integer');
        assert($warranty > 0, 'Product warranty must be positive integer');

        try {
            $this->databaseManager->beginTransaction();

            $addedCount = static::addProductsToInvoice($productId, $price, $quantity, $warranty);

            $this->databaseManager->commit();

            return $addedCount;
        } catch (Exception $e) {
            $this->databaseManager->rollback();

            return false;
        }
    }

    /**
     * Remove all products from invoice by product's id.
     *
     * @param int $productId
     * @return int Deleted products count.
     */
    public function deleteProducts(int $productId): int
    {
        assert($productId > 0, 'Product id must be positive integer');

        try {
            $this->databaseManager->beginTransaction();

            $deletedCount = static::deleteProductsFromInvoice($productId);

            $this->databaseManager->commit();

            return $deletedCount;
        } catch (Exception $e) {
            $this->databaseManager->rollback();
            return false;
        }
    }

    /**
     * Add products to invoice by product's id.
     *
     * @param int $productId
     * @param float $price
     * @param int $quantity
     * @param int|null $warranty
     * @return int Count of products that was added to invoice or subtracted from invoice.
     */
    protected function addProductsToInvoice(int $productId, float $price, int $quantity = 1, int $warranty = null): int
    {
        $invoiceProduct = $this->getInvoiceProduct($productId);

        if ($invoiceProduct) {
            $addedCount = $quantity - $invoiceProduct->quantity;
            $invoiceProduct->quantity = $quantity;
            $invoiceProduct->save();
        } else {
            $addedCount = $quantity;
            $this->invoice->invoiceProduct->push($this->createInvoiceProduct($productId, $price, $quantity, $warranty));
        }

        parent::increaseInvoiceSum($price * $addedCount);

        return $addedCount;
    }

    /**
     * Delete products from invoice by product's id.
     *
     * @param int $productId
     * @throws Exception
     * @return int Products count that was subtracted from invoice.
     */
    protected function deleteProductsFromInvoice(int $productId): int
    {
        $invoiceProduct = $this->getInvoiceProduct($productId);

        if (!$invoiceProduct) {
            return 0;
        }

        $subtractingSum = $invoiceProduct->price * $invoiceProduct->quantity;
        $deletedCount = $invoiceProduct->quantity;

        $invoiceProduct->delete();
        $this->invoice->setRelation('invoiceProduct', $this->removeFromInvoiceProductCollectionByProductId($this->invoice->invoiceProduct, $productId));

        parent::decreaseInvoiceSum($subtractingSum);

        return $deletedCount;
    }

    /**
     * @param int $productId
     * @return InvoiceProduct|null
     */
    private function getInvoiceProduct(int $productId)
    {
        return $this->invoice->invoiceProduct->keyBy('products_id')->get($productId);
    }

    /**
     * Create InvoiceProduct with given properties.
     *
     * @param int $productId
     * @param float $price
     * @param int $quantity
     * @param int|null $warranty
     * @return InvoiceProduct
     */
    private function createInvoiceProduct(int $productId, float $price, int $quantity = 1, int $warranty = null): Model
    {
        $invoiceProduct = $this->invoice->invoiceProduct()->create([
            'invoices_id' => $this->invoice->id,
            'products_id' => $productId,
            'quantity' => $quantity,
        ]);

        $invoiceProduct->price = $price;
        $invoiceProduct->warranty = $warranty;

        $invoiceProduct->save();

        return $invoiceProduct;
    }

    /**
     * Remove product with given id from collection of invoice product.
     *
     * @param Collection $collection
     * @param int $productId
     * @return Collection
     */
    private function removeFromInvoiceProductCollectionByProductId(Collection $collection, int $productId): Collection
    {
        return $collection->keyBy('products_id')->forget($productId);
    }
}