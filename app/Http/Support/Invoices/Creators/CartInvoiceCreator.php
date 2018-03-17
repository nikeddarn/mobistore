<?php
/**
 * Create user cart by user's id or cookie.
 */

namespace App\Http\Support\Invoices\Creators;

use App\Models\Invoice;
use Exception;

class CartInvoiceCreator extends InvoiceCreator
{
    /**
     * Create user cart invoice model by user id.
     *
     * @param int $userId
     * @return Invoice
     * @throws \Exception
     */
    public function createByUserId(int $userId): Invoice
    {
        try {
            $this->databaseManager->beginTransaction();

            parent::makeInvoice(self::CART);

            $userCart = $this->createdInvoice->userCart()->create([
                'invoices_id' => $this->createdInvoice->id,
                'users_id' => $userId,
            ]);

            $this->createdInvoice->setRelation('userCart', $userCart);

            $this->databaseManager->commit();

            return $this->createdInvoice;

        } catch (Exception $e) {
            $this->databaseManager->rollback();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Create user cart invoice model by user cookie.
     *
     * @param string $userCookie
     * @return Invoice
     * @throws Exception
     */
    public function createByUserCookie(string $userCookie)
    {
        try {
            $this->databaseManager->beginTransaction();

            parent::makeInvoice(self::CART);

            $userCart = $this->createdInvoice->userCart()->create([
                'invoices_id' => $this->createdInvoice->id,
                'cookie' => $userCookie,
            ]);

            $this->createdInvoice->setRelation('userCart', $userCart);

            $this->databaseManager->commit();

            return $this->createdInvoice;

        } catch (Exception $e) {
            $this->databaseManager->rollback();
            throw new Exception($e->getMessage());
        }
    }
}