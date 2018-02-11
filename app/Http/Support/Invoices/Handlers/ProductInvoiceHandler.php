<?php
/**
 * Methods to handle user cart invoice.
 */

namespace App\Http\Support\Invoices\Handlers;


use App\Contracts\Shop\Invoices\ProductInvoiceHandlerInterface;
use App\Models\InvoiceProduct;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class ProductInvoiceHandler extends InvoiceHandler implements ProductInvoiceHandlerInterface
{
    /**
     * Get collection of products of invoice.
     *
     * @return Collection
     */
    public function getProducts(): Collection
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
        $products = [];

        $this->invoice->invoiceProduct->each(function (InvoiceProduct $invoiceProduct) use ($imageUrlPrefix, &$products) {
            $products[] = [
                'id' => $invoiceProduct->product->id,
                'url' => $invoiceProduct->product->url,
                'title' => $invoiceProduct->product->page_title,
                'quantity' => $invoiceProduct->quantity,
                'price' => number_format($invoiceProduct->price, 2, '.', ','),
                'total' => number_format($invoiceProduct->quantity * $invoiceProduct->price, 2, '.', ','),
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
    public function isProductPresentInCart(int $productId)
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
     * Add products to invoice by product's id.
     *
     * @param int $productId
     * @param float $price
     * @param int $quantity
     * @param int|null $warranty
     * @return bool
     */
    public function addProducts(int $productId, float $price, int $quantity = 1, int $warranty = null): bool
    {
        assert($productId > 0, 'Product id must be positive integer');
        assert($price > 0, 'Product price must be positive float');
        assert($quantity > 0, 'Product quantity must be positive integer');
        assert($warranty > 0, 'Product warranty must be positive integer');

        try {
            $this->databaseManager->beginTransaction();

            $addingSum = static::addProductsToInvoice($productId, $price, $quantity, $warranty);

            $this->databaseManager->commit();

            return (bool)$addingSum;
        } catch (Exception $e) {
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
    public function removeProducts(int $productId, int $quantity = 1): bool
    {
        assert($productId > 0, 'Product id must be positive integer');
        assert($quantity > 0, 'Product quantity must be positive integer');

        try {
            $this->databaseManager->beginTransaction();

            $subtractingSum = static::removeProductsFromInvoice($productId, $quantity);

            $this->databaseManager->commit();

            return (bool)$subtractingSum;
        } catch (Exception $e) {
            $this->databaseManager->rollback();
            return false;
        }
    }

    /**
     * Remove all products from invoice by product's id.
     *
     * @param int $productId
     * @return bool
     */
    public function deleteProducts(int $productId): bool
    {
        assert($productId > 0, 'Product id must be positive integer');

        try {
            $this->databaseManager->beginTransaction();

            $subtractingSum = static::deleteProductsFromInvoice($productId);

            $this->databaseManager->commit();

            return (bool)$subtractingSum;
        } catch (Exception $e) {
            $this->databaseManager->rollback();
            return false;
        }
    }

    /**
     * Add product to invoice with given count or set count if product is already in invoice.
     *
     * @param int $productId
     * @param float $price
     * @param int $quantity
     * @param int|null $warranty
     * @return bool
     */
    public function setProductsCount(int $productId, float $price, int $quantity = 1, int $warranty = null): bool
    {
        assert($productId > 0, 'Product id must be positive integer');
        assert($price > 0, 'Product price must be positive float');
        assert($quantity > 0, 'Product quantity must be positive integer');
        assert($warranty > 0, 'Product warranty must be positive integer');

        try {
            $this->databaseManager->beginTransaction();

            $invoiceProduct = $this->getInvoiceProduct($productId);
            $currentProductQuantity = $invoiceProduct ? $invoiceProduct->quantity : 0;

            $addingProductQuantity = $quantity - $currentProductQuantity;

            $subtractingSum = static::addProductsToInvoice($productId, $price, $addingProductQuantity, $warranty);

            $this->databaseManager->commit();

            return (bool)$subtractingSum;
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
     * @return float Sum that was added to invoice.
     */
    protected function addProductsToInvoice(int $productId, float $price, int $quantity = 1, int $warranty = null): float
    {
        $invoiceProduct = $this->getInvoiceProduct($productId);

        if ($invoiceProduct) {
            $invoiceProduct->quantity += $quantity;
            $invoiceProduct->save();
        } else {
            $this->invoice->invoiceProduct->push($this->createInvoiceProduct($productId, $price, $quantity, $warranty));
        }

        $addingSum = $price * $quantity;

        parent::increaseInvoiceSum($addingSum);

        return $addingSum;

    }

    /**
     * Remove products from invoice by product's id.
     *
     * @param int $productId
     * @param int $quantity
     * @return float Sum that was subtracted from invoice.
     * @throws Exception
     */
    protected function removeProductsFromInvoice(int $productId, int $quantity): float
    {
        $invoiceProduct = $this->getInvoiceProduct($productId);

        if (!$invoiceProduct) {
            return 0;
        }

        if ($quantity >= $invoiceProduct->quantity) {
            $subtractingSum = $invoiceProduct->price * $invoiceProduct->quantity;

            $invoiceProduct->delete();
            $this->invoice->setRelation('invoiceProduct', $this->removeFromInvoiceProductCollectionByProductId($this->invoice->invoiceProduct, $productId));
        } else {
            $subtractingSum = $invoiceProduct->price * $quantity;
            $invoiceProduct->quantity -= $quantity;
            $invoiceProduct->save();
        }

        parent::decreaseInvoiceSum($subtractingSum);

        return $subtractingSum;
    }

    /**
     * Delete products from invoice by product's id.
     *
     * @param int $productId
     * @return float Sum that was subtracted from invoice.
     * @throws Exception
     */
    protected function deleteProductsFromInvoice(int $productId): float
    {
        $invoiceProduct = $this->getInvoiceProduct($productId);

        if (!$invoiceProduct) {
            return 0;
        }

        $subtractingSum = $invoiceProduct->price * $invoiceProduct->quantity;

        $invoiceProduct->delete();
        $this->invoice->setRelation('invoiceProduct', $this->removeFromInvoiceProductCollectionByProductId($this->invoice->invoiceProduct, $productId));

        parent::decreaseInvoiceSum($subtractingSum);

        return $subtractingSum;
    }

    /**
     * @param int $productId
     * @return InvoiceProduct|null
     */
    private function getInvoiceProduct(int $productId)
    {
        return $this->invoice->invoiceProduct->first(function (InvoiceProduct $invoiceProduct) use ($productId) {
            return $invoiceProduct->products_id === $productId;
        });
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