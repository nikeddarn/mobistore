<?php
/**
 * User invoice creator  for outgoing storage invoices.
 */

namespace App\Http\Support\Invoices\Creators;

use App\Contracts\Shop\Delivery\DeliveryStatusInterface;
use App\Models\Invoice;
use Exception;

class OutgoingUserInvoiceCreator extends InvoiceCreator implements DeliveryStatusInterface
{
    /**
     * @param int $invoiceType
     * @param int $userId
     * @param int $storageId
     * @param int $deliveryTypeId
     * @return Invoice
     * @throws Exception
     */
    public function createInvoice(int $invoiceType, int $userId, int $storageId, int $deliveryTypeId)
    {
        try {
            $this->databaseManager->beginTransaction();

            parent::makeInvoice($invoiceType);

            // create user invoice
            $userInvoice = $this->createdInvoice->userInvoice()->create([
                'invoices_id' => $this->createdInvoice->id,
                'users_id' => $userId,
                'direction' => self::INCOMING,
                'delivery_status_id' => self::PROCESSING,
                'delivery_types_id' =>$deliveryTypeId,
            ]);

            // create storage invoice
            $storageInvoice = $this->createdInvoice->storageInvoice()->create([
                'invoices_id' => $this->createdInvoice->id,
                'storages_id' => $storageId,
                'direction' => self::OUTGOING,
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