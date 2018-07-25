<?php
/**
 * Methods to handle reclamation invoices.
 */

namespace App\Http\Support\Invoices\Handlers\ProductInvoices\Reclamation\WriteOffDefectProduct;

use App\Http\Support\Invoices\Handlers\ProductInvoices\ProductInvoiceManager;
use Exception;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Collection;

class WriteOffDefectProductManager extends ProductInvoiceManager
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
     * Add existing reclamation product by reclamation id.
     *
     * @param int $reclamationId
     * @param float $price
     * @return bool
     */
    public function addExistingReclamation(int $reclamationId, float $price): bool
    {
        try {
            $this->databaseManager->beginTransaction();

            if (static::addProductByReclamationId($reclamationId, $price)) {

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
     * @param $price
     * @return bool
     */
    protected function addProductByReclamationId(int $reclamationId, float $price): bool
    {
        // add write off price to reclamation
        $this->invoice->reclamations()->where('id', $reclamationId)->update([
            'price' => $price,
        ]);

        // increase invoice sum by write off price
        parent::increaseInvoiceSum($price);

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
        // get reclamation
        $reclamationPrice = $this->invoice->reclamations()->where('id', $reclamationId)->first()->price;

        if (!$reclamationPrice){
            return false;
        }

        // decrease invoice sum by write off price
        parent::decreaseInvoiceSum($reclamationPrice);

        // remove invoice defect product
        return $this->invoice->reclamations()->detach($reclamationId);
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
     * Set invoice status as cancelled.
     *
     * @return bool
     */
    protected function setInvoiceCancelled():bool
    {
        return parent::setInvoiceCancelled();
    }
}