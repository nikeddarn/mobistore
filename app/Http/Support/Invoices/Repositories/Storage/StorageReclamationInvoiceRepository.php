<?php
/**
 * Retrieve user invoice with products.
 */

namespace App\Http\Support\Invoices\Repositories\Storage;


use App\Models\Invoice;
use App\Models\InvoiceProduct;
use Exception;
use Illuminate\Database\Eloquent\Model;

class StorageReclamationInvoiceRepository extends StorageInvoiceRepository
{
    /**
     * Eager loading relations.
     *
     * @return void
     */
    protected function addRelations()
    {
        parent::addRelations();

        $this->retrieveQuery
            ->with('invoiceReclamation.product')
            ->with(['storage' => function ($query) {
                $query->wherePivot('storages_id', $this->constraints->storageId);
                $query->with('storageProduct');
            }]);
    }

    /**
     * Remove products reserve from storage. Destroy invoice.
     *
     * @param Invoice|Model $invoice
     * @return bool
     */
    protected function removeInvoiceData(Invoice $invoice)
    {
        try {
            $this->databaseManager->beginTransaction();

            // remove products reserve from storage if exists
            if ($invoice->invoiceProduct->count()) {
                $this->removeReservedProductsOnStorage($invoice);
            }

            // delete invoice
            parent::removeInvoiceData($invoice);

            $this->databaseManager->commit();

            return true;
        } catch (Exception $e) {
            $this->databaseManager->rollback();
            return false;
        }
    }

    /**
     * Remove reserve for this invoice products from storage.
     *
     * @param Invoice $removingInvoice
     */
    protected function removeReservedProductsOnStorage(Invoice $removingInvoice)
    {
        $outgoingStorage = $removingInvoice->outgoingStorage()->first();

        if ($outgoingStorage) {

            $storageProducts = $outgoingStorage->storageProduct->keyBy('products_id');

            $removingInvoice->invoiceProduct->each(function (InvoiceProduct $invoiceProduct) use ($storageProducts) {
                $currentStorageProduct = $storageProducts->get($invoiceProduct->products_id);
                $currentStorageProduct->reserved_quantity = max(($currentStorageProduct->reserved_quantity - $invoiceProduct->quantity), 0);
                $currentStorageProduct->save();
            });
        }
    }
}