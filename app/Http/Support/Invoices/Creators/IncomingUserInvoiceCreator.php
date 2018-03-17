<?php
/**
 * User invoice creator for incoming storage invoices.
 */

namespace App\Http\Support\Invoices\Creators;

use App\Models\Invoice;
use Exception;

class IncomingUserInvoiceCreator extends InvoiceCreator
{
    /**
     * @param int $invoiceType
     * @param int $userId
     * @param int $storageId
     * @return Invoice
     * @throws Exception
     */
    public function createInvoice(int $invoiceType, int $userId, int $storageId)
    {
        try {
            $this->databaseManager->beginTransaction();

            parent::makeInvoice($invoiceType);

            $userInvoice = $this->createdInvoice->userInvoice()->create([
                'invoices_id' => $this->createdInvoice->id,
                'users_id' => $userId,
                'direction' => self::OUTGOING,
            ]);

            $storageInvoice = $this->createdInvoice->storageInvoice()->create([
                'invoices_id' => $this->createdInvoice->id,
                'storages_id' => $storageId,
                'direction' => self::INCOMING,
            ]);

            $this->createdInvoice
                ->setRelation('userInvoice', $userInvoice)
                ->setRelation('storageInvoice', $storageInvoice);

            $this->databaseManager->commit();

            return $this->createdInvoice;

        } catch (Exception $e) {
            $this->databaseManager->rollback();
            throw new Exception($e->getMessage());
        }
    }
}