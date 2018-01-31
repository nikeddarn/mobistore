<?php
/**
 * Methods to handle user cart invoice.
 */

namespace App\Http\Support\Invoices\Handlers;


use App\Contracts\Shop\Invoices\ProductInvoiceHandlerInterface;
use App\Models\InvoiceProduct;
use Exception;
use Illuminate\Database\Eloquent\Model;

class ProductInvoiceHandler extends InvoiceHandler implements ProductInvoiceHandlerInterface
{
    /**
     * Get products of invoice.
     *
     * @return array
     */
    public function getProducts(): array
    {
        return $this->invoice->invoiceProduct->toArray();
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
     * Add products to invoice by product's id.
     *
     * @param int $productId
     * @param float $price
     * @param int $quantity
     * @param int|null $warranty
     * @return bool
     */
    public function addProducts(int $productId, float $price, int $quantity, int $warranty = null): bool
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
    public function removeProducts(int $productId, int $quantity): bool
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
     * Add products to invoice by product's id.
     *
     * @param int $productId
     * @param float $price
     * @param int $quantity
     * @param int|null $warranty
     * @return float Sum that was added to invoice.
     */
    private function addProductsToInvoice(int $productId, float $price, int $quantity = 1, int $warranty = null): float
    {
        $invoiceProduct = $this->getInvoiceProduct($productId);

        if ($invoiceProduct) {
            $invoiceProduct->quantity += $quantity;
            $invoiceProduct->save();
        }else{
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
    protected function deleteProductsFromInvoice(int $productId):float
    {
        $invoiceProduct = $this->getInvoiceProduct($productId);

        if ($invoiceProduct) {
            return 0;
        }

        $subtractingSum = $invoiceProduct->price * $invoiceProduct->quantity;

        $invoiceProduct->delete();

        parent::decreaseInvoiceSum($subtractingSum);

        return $subtractingSum;
    }

    /**
     * @param int $productId
     * @return InvoiceProduct|null
     */
    private function getInvoiceProduct(int $productId)
    {
        if ($this->invoice->invoiceProduct->count()) {
            return $this->invoice->invoiceProduct->where('id', $productId)->first();
        } else {
            return null;
        }
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
}