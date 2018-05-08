<?php
/**
 * Vendor invoice creator for outgoing storage invoices.
 */

namespace App\Http\Support\Invoices\Creators;

use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Models\Invoice;
use Exception;

class VendorInvoiceCreator extends InvoiceCreator
{
    /**
     * @param int $invoiceType
     * @param int $vendorId
     * @param int $storageId
     * @param string $direction
     * @return Invoice
     * @throws Exception
     */
    public function createInvoice(int $invoiceType, int $vendorId, string $direction, int $storageId = null)
    {
        try {
            $this->databaseManager->beginTransaction();

            parent::makeInvoice($invoiceType);

            // create vendor invoice
            $vendorInvoice = $this->createdInvoice->vendorInvoice()->create([
                'invoices_id' => $this->createdInvoice->id,
                'vendors_id' => $vendorId,
                'direction' => $direction,
            ]);

            $this->createdInvoice->setRelation('vendorInvoice', $vendorInvoice);

            // create storage invoice
            if ($storageId) {
                $oppositeDirection = $direction === InvoiceDirections::OUTGOING ? InvoiceDirections::INCOMING : InvoiceDirections::OUTGOING;

                $storageInvoice = $this->createdInvoice->storageInvoice()->create([
                    'invoices_id' => $this->createdInvoice->id,
                    'storages_id' => $storageId,
                    'direction' => $oppositeDirection,
                ]);

                $this->createdInvoice->setRelation('storageInvoice', $storageInvoice);
            }

            $this->databaseManager->commit();

            return $this->createdInvoice;

        } catch (Exception $e) {
            $this->databaseManager->rollback();
            throw new Exception($e->getMessage());
        }
    }
}