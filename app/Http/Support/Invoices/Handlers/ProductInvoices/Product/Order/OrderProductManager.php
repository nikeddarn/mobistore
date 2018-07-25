<?php
/**
 * Methods to handle product invoices.
 */

namespace App\Http\Support\Invoices\Handlers\ProductInvoices\Product\Order;

use App\Http\Support\Invoices\Handlers\ProductInvoices\ProductInvoiceManager;
use App\Models\InvoiceProduct;
use Exception;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class OrderProductManager extends ProductInvoiceManager
{
    /**
     * ManageShowProductInvoiceHandler constructor.
     *
     * @param DatabaseManager $databaseManager
     */
    public function __construct(DatabaseManager $databaseManager)
    {
        parent::__construct($databaseManager);
    }

    /**
     * Get collection of products of invoice.
     *
     * @return Collection
     */
    public function getInvoiceProducts(): Collection
    {
        return $this->invoice->invoiceProduct->keyBy('products_id');
    }

    /**
     * Get array of products count keyed by product id.
     *
     * @return array
     */
    public function getArrayInvoiceProducts(): array
    {
        return $this->invoice->invoiceProduct->pluck('quantity', 'products_id')->toArray();
    }

    /**
     * Is product with given id already in invoice ?
     *
     * @param int $productId
     * @return bool
     */
    public function productExists(int $productId): bool
    {
        assert($productId > 0, 'Product id must be positive integer');

        return (bool)$this->getInvoiceProducts()->contains('products_id', $productId);
    }

    /**
     * Get total count of products of invoice
     *
     * @return int
     */
    public function getProductsCount(): int
    {
        return $this->invoice->invoiceProduct->sum('quantity');

    }

    /**
     * Update invoice product's price by invoice product's id.
     *
     * @param int $invoiceProductId
     * @param float $price
     * @return mixed
     */
    public function updateInvoiceProductPrice(int $invoiceProductId, float $price)
    {
        $invoiceProduct = $this->getInvoiceProducts()->where('id', $invoiceProductId)->first();

        $changingInvoiceSum = ($invoiceProduct->price - $price) * $invoiceProduct->quantity;

        if ($changingInvoiceSum > 0 ? $this->decreaseInvoiceSum($changingInvoiceSum) : $this->increaseInvoiceSum($changingInvoiceSum)) {

            $invoiceProduct->price = $price;
            return $invoiceProduct->save();
        }

        return false;
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
    public function addProduct(int $productId, float $price, int $quantity = 1, int $warranty = null): int
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
    public function deleteProduct(int $productId): int
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
     * Decrease products count in invoice by product's id.
     *
     * @param int $productId
     * @param int $decreasingQuantity
     * @return int Deleted products count.
     */
    public function removeProduct(int $productId, int $decreasingQuantity): int
    {
        assert($productId > 0, 'Product id must be positive integer');

        try {
            $this->databaseManager->beginTransaction();

            $deletedCount = static::decreaseInvoiceProductCount($productId, $decreasingQuantity);

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
        $invoiceProduct = $this->getInvoiceProducts()->get($productId);

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
        $invoiceProduct = $this->getInvoiceProducts()->get($productId);

        if (!$invoiceProduct) {
            return 0;
        }

        // calculate deleting product data
        $subtractingSum = $invoiceProduct->price * $invoiceProduct->quantity;
        $deletedCount = $invoiceProduct->quantity;

        // delete from db
        $invoiceProduct->delete();

        // delete from collection
        $this->invoice->invoiceProducts()->keyBy('products_id')->forget($productId);

        parent::decreaseInvoiceSum($subtractingSum);

        return $deletedCount;
    }

    /**
     * Decrease product count in invoice
     *
     * @param int $productId
     * @param int $decreasingQuantity
     * @return int
     */
    protected function decreaseInvoiceProductCount(int $productId, int $decreasingQuantity): int
    {
        $invoiceProduct = $this->getInvoiceProducts()->get($productId);

        if (!$invoiceProduct) {
            return 0;
        }

        $deletedCount = min($invoiceProduct->quantity, $decreasingQuantity);
        $subtractingSum = $invoiceProduct->price * $deletedCount;

        $invoiceProduct->quantity -= $deletedCount;
        $invoiceProduct->save();

        parent::decreaseInvoiceSum($subtractingSum);

        return $deletedCount;
    }

    /**
     * Set invoice status as cancelled.
     *
     * @return bool
     */
    protected function setInvoiceCancelled(): bool
    {
        return parent::setInvoiceCancelled();
    }

    /**
     * Delete current invoice.
     *
     * @return bool
     * @throws \Exception
     */
    protected function deleteHandlingInvoice(): bool
    {
        return parent::deleteHandlingInvoice();
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
        $invoiceProduct = $this->invoice->invoiceProducts()->create([
            'invoices_id' => $this->invoice->id,
            'products_id' => $productId,
            'quantity' => $quantity,
        ]);

        $invoiceProduct->price = $price;
        $invoiceProduct->warranty = $warranty;

        $invoiceProduct->save();

        return $invoiceProduct;
    }
}