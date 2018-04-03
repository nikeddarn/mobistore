<?php
/**
 * User invoice creator  for outgoing storage invoices.
 */

namespace App\Http\Support\Invoices\Creators;

use App\Contracts\Shop\Delivery\DeliveryStatusInterface;
use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Models\Invoice;
use Exception;

class StorageReplacementInvoiceCreator extends InvoiceCreator implements DeliveryStatusInterface
{
    /**
     * @param int $outgoingStorageId
     * @param int $incomingStorageId
     * @return Invoice
     * @throws Exception
     */
    public function createInvoice(int $outgoingStorageId, int $incomingStorageId)
    {
        try {
            $this->databaseManager->beginTransaction();

            parent::makeInvoice(InvoiceTypes::STORAGE_REPLACEMENT);

            // create outgoing storage invoice
            $outgoingStorageInvoice = $this->createdInvoice->storageInvoice()->create([
                'invoices_id' => $this->createdInvoice->id,
                'storages_id' => $outgoingStorageId,
                'direction' => InvoiceDirections::OUTGOING,
            ]);

            // create incoming storage invoice
            $incomingStorageInvoice = $this->createdInvoice->storageInvoice()->create([
                'invoices_id' => $this->createdInvoice->id,
                'storages_id' => $incomingStorageId,
                'direction' => InvoiceDirections::INCOMING,
            ]);

            $this->createdInvoice->setRelation('storageInvoice', collect()->push($outgoingStorageInvoice)->push($incomingStorageInvoice));

            $this->databaseManager->commit();

            return $this->createdInvoice;

        } catch (Exception $e) {
            $this->databaseManager->rollback();
            throw new Exception($e->getMessage());
        }
    }
}