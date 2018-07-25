<?php
/**
 * User invoice creator with storage invoice.
 */

namespace App\Http\Support\Invoices\Creators\UserInvoiceCreators;

use App\Models\Invoice;
use App\Models\StorageInvoice;
use Exception;
use Illuminate\Database\Eloquent\Model;

abstract class StorageUserInvoiceCreator extends UserInvoiceCreator
{
    /**
     * @var int
     */
    protected $storageDepartmentId;

    /**
     * Create user invoice model by user's id and storage invoice by storage department's id.
     *
     * @param int $userId
     * @param int|null $storageDepartmentId
     * @return Invoice
     * @throws \Exception
     */
    public function createInvoice(int $userId, int $storageDepartmentId = null): Invoice
    {
        try {
            $this->databaseManager->beginTransaction();

            $this->userId = $userId;
            $this->storageDepartmentId = $storageDepartmentId;

            $invoice =  $this->makeInvoice();

            $this->databaseManager->commit();

            return $invoice;

        } catch (Exception $e) {
            $this->databaseManager->rollback();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Make invoice.
     *
     * @return Invoice
     */
    protected function makeInvoice():Invoice
    {
        $invoice = parent::makeInvoice();

        return $invoice->setRelation('storageInvoices', collect()
        ->push($this->makeStorageInvoice($invoice))
        );
    }

    /**
     * Get array of data for create Invoice model.
     *
     * @return array
     */
    protected function getInvoiceData():array
    {
        return parent::getInvoiceData();
    }

    /**
     * Get array of data for create UserInvoice model.
     *
     * @return array
     */
    protected function getUserInvoiceData():array
    {
        return parent::getUserInvoiceData();
    }

    /**
     * Get array of data for create UserInvoice model.
     *
     * @return array
     */
    protected function getStorageInvoiceData():array
    {
        return [
            'storage_departments_id' => $this->storageDepartmentId,
        ];
    }

    /**
     * Make UserInvoice.
     *
     * @param Invoice $invoice
     * @return StorageInvoice|Model
     */
    private function makeStorageInvoice(Invoice $invoice):StorageInvoice
    {
        return $invoice->storageInvoices()->create(static::getStorageInvoiceData());
    }
}