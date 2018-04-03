<?php
/**
 * User invoice creator  for outgoing storage invoices.
 */

namespace App\Http\Support\Invoices\Creators;

use App\Contracts\Shop\Delivery\DeliveryStatusInterface;
use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Models\Invoice;
use Exception;

class UserInvoiceCreator extends InvoiceCreator
{
    /**
     * @param int $invoiceType
     * @param int $userId
     * @param int $storageId
     * @param string $direction
     * @return Invoice
     * @throws Exception
     */
    public function createInvoice(int $invoiceType, int $userId, int $storageId, string $direction)
    {
        try {
            $this->databaseManager->beginTransaction();

            parent::makeInvoice($invoiceType);

            // create user invoice
            $userInvoice = $this->createdInvoice->userInvoice()->create([
                'invoices_id' => $this->createdInvoice->id,
                'users_id' => $userId,
                'direction' => $direction,
                'delivery_status_id' => DeliveryStatusInterface::PROCESSING,
            ]);

            // create storage invoice
            $oppositeDirection = $direction === InvoiceDirections::OUTGOING ? InvoiceDirections::INCOMING : InvoiceDirections::OUTGOING;

            $storageInvoice = $this->createdInvoice->storageInvoice()->create([
                'invoices_id' => $this->createdInvoice->id,
                'storages_id' => $storageId,
                'direction' => $oppositeDirection,
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