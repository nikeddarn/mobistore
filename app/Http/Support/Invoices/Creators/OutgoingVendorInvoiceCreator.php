<?php
/**
 * Vendor invoice creator for outgoing storage invoices.
 */

namespace App\Http\Support\Invoices\Creators;

use App\Models\Invoice;
use Exception;

class OutgoingVendorInvoiceCreator extends InvoiceCreator
{
    /**
     * @param int $invoiceType
     * @param int $vendorId
     * @param int $storageId
     * @return Invoice
     * @throws Exception
     */
    public function createInvoice(int $invoiceType, int $vendorId, int $storageId)
    {
        try {
            $this->databaseManager->beginTransaction();

            parent::makeInvoice($invoiceType);

            $vendorInvoice = $this->createdInvoice->vendorInvoice()->create([
                'invoices_id' => $this->createdInvoice->id,
                'vendors_id' => $vendorId,
                'direction' => self::INCOMING,
            ]);

            $storageInvoice = $this->createdInvoice->storageInvoice()->create([
                'invoices_id' => $this->createdInvoice->id,
                'storages_id' => $storageId,
                'direction' => self::OUTGOING,
            ]);

            $this->createdInvoice
                ->setRelation('vendorInvoice', $vendorInvoice)
                ->setRelation('storageInvoice', $storageInvoice);

            $this->databaseManager->commit();

            return $this->createdInvoice;

        } catch (Exception $e) {
            $this->databaseManager->rollback();
            throw new Exception($e->getMessage());
        }
    }
}