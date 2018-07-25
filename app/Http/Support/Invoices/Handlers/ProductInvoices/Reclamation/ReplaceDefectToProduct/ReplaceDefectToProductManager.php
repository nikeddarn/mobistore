<?php
/**
 * Methods to handle reclamation invoices.
 */

namespace App\Http\Support\Invoices\Handlers\ProductInvoices\Reclamation\ReplaceDefectToProduct;

use App\Http\Support\Invoices\Handlers\ProductInvoices\ProductInvoiceManager;
use Exception;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Collection;

class ReplaceDefectToProductManager extends ProductInvoiceManager
{
    /**
     * ReclamationProductShowProductInvoiceHandler constructor.
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
        return $this->invoice->reclamations()->get();
    }

    /**
     * Get array of products count keyed by product id.
     *
     * @return array
     */
    public function getArrayInvoiceProducts(): array
    {
        return $this->invoice->reclamations()
            ->selectRaw('products_id, COUNT(id) AS quantity')
            ->groupBy('products_id')
            ->get()
            ->pluck('quantity', 'products_id')
            ->toArray();
    }

    /**
     * Is product with given reclamation id already in invoice ?
     *
     * @param int $reclamationId
     * @return bool
     */
    public function productExists(int $reclamationId):bool
    {
        assert($reclamationId > 0, 'Reclamation id must be positive integer');

        return (bool)$this->invoice->reclamations()->where('id', $reclamationId)->first();
    }

    /**
     * Get total count of products of invoice
     *
     * @return int
     */
    public function getProductsCount():int
    {
        return $this->invoice->invoiceDefectProducts()->count();

    }

    /**
     * Add existing reclamation product by reclamation id.
     *
     * @param int $reclamationId
     * @return bool
     */
    public function addExistingReclamation(int $reclamationId): bool
    {
        try {
            $this->databaseManager->beginTransaction();

            if (static::addProductByReclamationId($reclamationId)) {

                $this->databaseManager->commit();

                return true;
            } else {
                $this->databaseManager->rollback();

                return false;
            }
        } catch (Exception $e) {
            $this->databaseManager->rollback();

            return false;
        }
    }

    /**
     * Remove product from reclamation invoice by id of InvoiceDefectProduct model.
     *
     * @param int $reclamationId
     * @return bool
     */
    public function removeReclamation (int $reclamationId): bool
    {
        assert($reclamationId > 0, 'InvoiceDefectProduct id must be positive integer');

        try {
            $this->databaseManager->beginTransaction();

            if (static::deleteProductFromInvoice($reclamationId)) {

                $this->databaseManager->commit();

                return true;
            } else {
                $this->databaseManager->rollback();

                return false;
            }
        } catch (Exception $e) {
            $this->databaseManager->rollback();

            return false;
        }
    }

    /**
     * Add existing reclamation product by storage reclamation.
     *
     * @param int $reclamationId
     * @return bool
     */
    protected function addProductByReclamationId(int $reclamationId): bool
    {
        // create invoice defect product
        $this->invoice->reclamations()->attach($reclamationId);

        return true;
    }

    /**
     * Remove existing reclamation product from invoice by InvoiceDefectProduct id.
     *
     * @param int $reclamationId
     * @return bool
     */
    protected function deleteProductFromInvoice(int $reclamationId): bool
    {
        return $this->invoice->reclamations()->detach($reclamationId);
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
}