<?php
/**
 * Methods to handle reclamation invoices.
 */

namespace App\Http\Support\Invoices\Handlers\ProductInvoices\Product\Replacement;

use App\Http\Support\Invoices\Handlers\ProductInvoices\ProductInvoiceManager;
use App\Models\InvoiceProduct;
use Exception;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class StorageReplaceProductManager extends ProductInvoiceManager
{
    /**
     * ProductReplacementShowProductInvoiceHandler constructor.
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
        return $this->invoice->invoiceProduct;
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
     * Create array of products data for view.
     *
     * @param Collection $invoiceProducts
     * @param string|null $imageUrlPrefix
     * @return array
     */
    public function getFormattedProducts(Collection $invoiceProducts, string $imageUrlPrefix = null):array
    {
        $products = [];

        $invoiceProducts->each(function (InvoiceProduct $invoiceProduct) use ($imageUrlPrefix, &$products) {
            $products[] = [
                'id' => $invoiceProduct->product->id,
                'url' => $invoiceProduct->product->url,
                'title' => $invoiceProduct->product->page_title,
                'quantity' => $invoiceProduct->quantity,
                'price' => number_format($invoiceProduct->price, 2, '.', ','),
                'total' => number_format($invoiceProduct->price * $invoiceProduct->quantity, 2, '.', ','),
                'image' => $imageUrlPrefix ? $imageUrlPrefix . ($invoiceProduct->product->primaryImage ? $invoiceProduct->product->primaryImage->image : 'no_image.png') : null,
            ];
        });

        return $products;
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
        return $this->invoice->invoiceProduct->sum('quantity');

    }

    /**
     * Add products to invoice by product's id.
     *
     * @param int $productId
     * @param int $quantity
     * @return int Added products count.
     */
    public function addProduct(int $productId, int $quantity = 1): int
    {
        assert($productId > 0, 'Product id must be positive integer');
        assert($quantity > 0, 'Product quantity must be positive integer');

        try {
            $this->databaseManager->beginTransaction();

            $addedCount = static::addProductsToInvoice($productId, $quantity);

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
     * @param int $quantity
     * @return int Count of products that was added to invoice or subtracted from invoice.
     */
    protected function addProductsToInvoice(int $productId, int $quantity = 1): int
    {
        $invoiceProduct = $this->getInvoiceProduct($productId);

        if ($invoiceProduct) {
            $addedCount = $quantity - $invoiceProduct->quantity;
            $invoiceProduct->quantity = $quantity;
            $invoiceProduct->save();
        } else {
            $addedCount = $quantity;
            $this->invoice->invoiceProduct->push($this->createInvoiceProduct($productId, $quantity));
        }

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

        $deletedCount = $invoiceProduct->quantity;

        $invoiceProduct->delete();
        $this->invoice->setRelation('invoiceProduct', $this->removeFromInvoiceProductCollectionByProductId($this->invoice->invoiceProduct, $productId));

        return $deletedCount;
    }

    /**
     * Decrease product count in invoice
     *
     * @param int $productId
     * @param int $decreasingQuantity
     * @return int
     */
    protected function decreaseInvoiceProductCount(int $productId, int $decreasingQuantity):int
    {
        $invoiceProduct = $this->getInvoiceProduct($productId);

        if (!$invoiceProduct) {
            return 0;
        }

        $deletedCount = min($invoiceProduct->quantity, $decreasingQuantity);

        $invoiceProduct->quantity -= $deletedCount;
        $invoiceProduct->save();

        return $deletedCount;
    }

    /**
     * Set invoice status as cancelled.
     *
     * @return bool
     */
    protected function setInvoiceCancelled():bool
    {
        return parent::setInvoiceCancelled();
    }

    /**
     * Delete current invoice.
     *
     * @return bool
     * @throws \Exception
     */
    protected function deleteHandlingInvoice():bool
    {
        return parent::deleteHandlingInvoice();
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
     * @param int $quantity
     * @return InvoiceProduct
     */
    private function createInvoiceProduct(int $productId, int $quantity = 1): Model
    {
        return $this->invoice->invoiceProducts()->create([
            'invoices_id' => $this->invoice->id,
            'products_id' => $productId,
            'quantity' => $quantity,
        ]);
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