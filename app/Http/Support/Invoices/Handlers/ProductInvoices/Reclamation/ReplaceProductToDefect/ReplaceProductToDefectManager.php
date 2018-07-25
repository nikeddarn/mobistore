<?php
/**
 * Methods to handle reclamation invoices.
 */

namespace App\Http\Support\Invoices\Handlers\ProductInvoices\Reclamation\ReplaceProductToDefect;

use App\Contracts\Shop\Reclamations\ReclamationStatusInterface;
use App\Http\Support\Invoices\Handlers\ProductInvoices\ProductInvoiceManager;
use Exception;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Collection;

class ReplaceProductToDefectManager extends ProductInvoiceManager
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
     * Add new reclamation product to invoice by product's id.
     *
     * @param int $productId
     * @param string|null $productLabel
     * @param string|null $defect
     * @return bool
     */
    public function addNewReclamation(int $productId, string $productLabel = null, string $defect = null): bool
    {
        assert($productId > 0, 'Product id must be positive integer');

        try {
            $this->databaseManager->beginTransaction();

            if (static::addProductToInvoice($productId, $productLabel, $defect)) {

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
     * Add new reclamation product to invoice by product's id.
     *
     * @param int $productId
     * @param string|null $productLabel
     * @param string|null $defect
     * @return bool
     */
    private function addProductToInvoice(int $productId, string $productLabel = null, string $defect = null): bool
    {
        // create reclamation
        $reclamation = $this->invoice->reclamations()->create([
            'products_id' => $productId,
            'product_label' => $productLabel,
            'defect' => $defect,
            'reclamation_status_id' => ReclamationStatusInterface::REGISTERED,
        ]);

        // create invoice defect product
        $this->invoice->reclamations()->attach($reclamation->id);

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