<?php
/**
 * Create invoice with incoming and outgoing storages.
 */

namespace App\Http\Support\Invoices\Creators\StorageInvoiceCreators;


use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Http\Support\Invoices\Creators\InvoiceCreator;
use App\Models\Invoice;
use App\Models\StorageInvoice;
use Exception;
use Illuminate\Database\Eloquent\Model;

class StoragesInvoiceCreator extends InvoiceCreator
{
    /**
     * @var int
     */
    private $incomingStorageDepartmentId;

    /**
     * @var int
     */
    private $outgoingStorageDepartmentId;

    /**
     * Create incoming and outgoing storage invoices by storage department's id.
     *
     * @param int $incomingStorageDepartmentId
     * @param int $outgoingStorageDepartmentId
     * @return Invoice
     * @throws Exception
     */
    public function createInvoice(int $incomingStorageDepartmentId, int $outgoingStorageDepartmentId): Invoice
    {
        try {
            $this->databaseManager->beginTransaction();

            $this->incomingStorageDepartmentId = $incomingStorageDepartmentId;
            $this->outgoingStorageDepartmentId = $outgoingStorageDepartmentId;

            $invoice = $this->makeInvoice();

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
    protected function makeInvoice(): Invoice
    {
        $invoice = parent::makeInvoice();

        $incomingStorageInvoice = $this->makeIncomingStorageInvoice($invoice);
        $outgoingStorageInvoice = $this->makeOutgoingStorageInvoice($invoice);

        return $invoice
            ->setRelation('storageInvoices', collect()
                ->push($incomingStorageInvoice)
                ->push($outgoingStorageInvoice)
            )
            ->setRelation('incomingStorageInvoice', $incomingStorageInvoice)
            ->setRelation('outgoingStorageInvoice', $outgoingStorageInvoice);
    }

    /**
     * Get array of data for create Invoice model.
     *
     * @return array
     */
    protected function getInvoiceData(): array
    {
        return parent::getInvoiceData();
    }

    /**
     * Make incoming StorageInvoice.
     *
     * @param Invoice $invoice
     * @return StorageInvoice|Model
     */
    private function makeIncomingStorageInvoice(Invoice $invoice): StorageInvoice
    {
        return $invoice->storageInvoices()->create([
            'storage_departments_id' => $this->incomingStorageDepartmentId,
            'direction' => InvoiceDirections::INCOMING,
        ]);
    }

    /**
     * Make outgoing StorageInvoice.
     *
     * @param Invoice $invoice
     * @return StorageInvoice|Model
     */
    private function makeOutgoingStorageInvoice(Invoice $invoice): StorageInvoice
    {
        return $invoice->storageInvoices()->create([
            'storage_departments_id' => $this->outgoingStorageDepartmentId,
            'direction' => InvoiceDirections::OUTGOING,
        ]);
    }
}