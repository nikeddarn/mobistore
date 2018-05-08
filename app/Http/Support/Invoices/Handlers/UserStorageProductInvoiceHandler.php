<?php
/**
 * Handler for product invoices that used storage with delivery.
 */

namespace App\Http\Support\Invoices\Handlers;

use App\Contracts\Shop\Invoices\Handlers\UserProductInvoiceHandlerInterface;
use App\Models\InvoiceProduct;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class UserStorageProductInvoiceHandler extends StorageProductInvoiceHandler implements UserProductInvoiceHandlerInterface
{
    /**
     * Append user delivery to invoice.
     *
     * @param int $userDeliveryId
     * @param int $deliveryTypeId
     */
    public function appendUserDelivery(int $userDeliveryId, int $deliveryTypeId)
    {
        $this->invoice->userInvoice->user_deliveries_id = $userDeliveryId;
        $this->invoice->userInvoice->delivery_types_id = $deliveryTypeId;

        $this->invoice->userInvoice->save();
    }

    /**
     * Update user delivery status.
     *
     * @param int $deliveryStatus
     */
    public function setDeliveryStatus(int $deliveryStatus)
    {
        $this->invoice->userInvoice->delivery_status_id = $deliveryStatus;
        $this->invoice->userInvoice->save();
    }

    /**
     * Update user delivery date.
     *
     * @param Carbon $deliveryDate
     */
    public function updateDeliveryDate(Carbon $deliveryDate)
    {
        $this->invoice->userInvoice->userDelivery->planned_arrival = $deliveryDate->toDateString();
        $this->invoice->userInvoice->userDelivery->save();
    }

    /**
     * Set storage invoice as implemented.
     *
     * @return bool
     */
    public function setStorageInvoiceImplemented():bool
    {
        $storageInvoice = $this->invoice->storageInvoice->first();

        if ($storageInvoice){
            $storageInvoice->implemented = 1;
            $storageInvoice->save();
            return true;
        }
        return false;
    }

    /**
     * Set user invoice as implemented.
     *
     * @return bool
     */
    public function setUserInvoiceImplemented():bool
    {
        $userInvoice = $this->invoice->userInvoice;

        if ($userInvoice){
            $userInvoice->implemented = 1;
            $userInvoice->save();
            return true;
        }
        return false;
    }

    /**
     * Get collection of related VendorInvoice.
     *
     * @return Collection
     */
    public function getRelatedVendorInvoices():Collection
    {
        return $this->invoice->userInvoice->vendorInvoice()->get();
    }

    /**
     * Remove reserve of product on storage
     *
     * @param Collection $products
     */
    public function removeReserveFromStorage(Collection $products = null)
    {
        // get all invoice products
        if (!$products){
            $products = $this->invoice->invoiceProduct;
        }

        $products->each(function (InvoiceProduct $invoiceProduct){
            $this->reserveProductsOnStorage($invoiceProduct->products_id, -abs($invoiceProduct->quantity));
        });
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
        $addedCount = parent::addProductsToInvoice($productId, $price, $quantity, $warranty);

        $this->reserveProductsOnStorage($productId, $addedCount);

        return $addedCount;
    }

    /**
     * Delete products from invoice by product's id.
     *
     * @param int $productId
     * @return int Products count that was subtracted from invoice.
     * @throws \Exception
     */
    protected function deleteProductsFromInvoice(int $productId): int
    {
        $deletedCount = parent::deleteProductsFromInvoice($productId);

        $this->reserveProductsOnStorage($productId, -abs($deletedCount));

        return $deletedCount;
    }

    /**
     * Decrease product count in invoice
     *
     * @param int $productId
     * @param int $decreasingQuantity
     * @return int
     * @throws \Exception
     */
    protected function decreaseInvoiceProductCount(int $productId, int $decreasingQuantity):int
    {
        $deletedCount = parent::decreaseInvoiceProductCount($productId, $decreasingQuantity);

        $this->reserveProductsOnStorage($productId, -abs($deletedCount));

        return $deletedCount;
    }

    /**
     * Reserve products on outgoing store. If $reservingCount is negative, products will be unreserved by this count.
     *
     * @param int $productId
     * @param int $reservingCount
     */
    protected function reserveProductsOnStorage(int $productId, int $reservingCount)
    {
        $outgoingStorage = $this->invoice->outgoingStorage->first();

        if ($outgoingStorage) {

            $storageProduct = $outgoingStorage->storageProduct->keyBy('products_id')->get($productId);

            if (!$storageProduct && $reservingCount > 0) {
                $storageProduct = $this->createStorageProduct($outgoingStorage, $productId);
                $outgoingStorage->storageProduct->push($storageProduct);
            }

            $storageProduct->reserved_quantity = max($storageProduct->reserved_quantity + $reservingCount, 0);
            $storageProduct->save();
        }
    }
}