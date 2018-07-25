<?php
/**
 * Handle payment user storage invoices.
 */

namespace App\Http\Support\Invoices\Handlers\PaymentInvoices\Payment;


use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Http\Support\Balance\StorageBalance;
use App\Http\Support\Balance\UserBalance;
use App\Models\UserInvoice;
use Exception;
use Illuminate\Database\DatabaseManager;

final class UserPaymentInvoiceHandler extends StoragePaymentInvoiceHandler
{
    /**
     * @var UserBalance
     */
    private $userBalance;

    /**
     * UserPaymentInvoiceHandler constructor.
     * @param DatabaseManager $databaseManager
     * @param StorageBalance $storageBalance
     * @param UserBalance $userBalance
     */
    public function __construct(DatabaseManager $databaseManager, StorageBalance $storageBalance, UserBalance $userBalance)
    {
        parent::__construct($databaseManager, $storageBalance);
        $this->userBalance = $userBalance;
    }

    /**
     * Set user invoice as implemented.
     *
     * @return bool
     * @throws \Exception
     */
    public function implementUserInvoice(): bool
    {
        try {
            $this->databaseManager->beginTransaction();

            if (static::completeUserInvoice()) {

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
     * Complete storage invoice.
     *
     * @return bool
     */
    protected function completeStorageInvoice()
    {
        if (!parent::completeStorageInvoice()) {
            return false;
        }

        // fix incoming storage invoice
        if ($this->isUserInvoiceImplemented() && $this->isStorageInvoiceIncoming()) {
            return $this->fixOutgoingUserInvoiceInBalance() && $this->fixIncomingStorageInvoiceInBalance() && $this->setInvoiceFinished();
        }

        return true;
    }

    /**
     * Complete user invoice.
     *
     * @return bool
     */
    private function completeUserInvoice()
    {
        if (!$this->setUserInvoiceImplemented()) {
            return false;
        }

        // fix incoming user invoice
        if ($this->isStorageInvoiceImplemented() && $this->isUserInvoiceIncoming()) {
            return $this->fixIncomingUserInvoiceInBalance() && $this->fixOutgoingStorageInvoiceInBalance() && $this->setInvoiceFinished();
        }

        return false;
    }

    /**
     * Complete user invoice.
     *
     * @return bool
     */
    private function setUserInvoiceImplemented()
    {
        if (!$this->isInvoiceProcessing()) {
            return false;
        }

        $userInvoice = $this->getUserInvoice();

        if ($userInvoice && !$userInvoice->implemented) {
            $userInvoice->implemented = 1;
            return $userInvoice->save();
        }

        return false;
    }

    /**
     * Set invoice status as cancelled.
     *
     * @return bool
     */
    protected function setInvoiceCancelled(): bool
    {
        // invoice is completed
        if ($this->isInvoiceCompleted()) {
            return false;
        }

        return parent::setInvoiceCancelled();
    }

    /**
     * Delete current invoice.
     *
     * @return bool
     * @throws \Exception
     */
    protected function deleteHandlingInvoice(): bool
    {
        if (!$this->isUserInvoiceImplemented()) {
            return parent::deleteHandlingInvoice();
        } else {
            return false;
        }
    }

    /**
     * Is vendor invoice incoming?
     *
     * @return bool
     */
    private function isUserInvoiceIncoming()
    {
        return $this->getUserInvoice()->direction === InvoiceDirections::INCOMING;
    }

    /**
     * Is user invoice already implemented?
     *
     * @return bool
     */
    protected function isUserInvoiceImplemented(): bool
    {
        return (bool)$this->getUserInvoice()->implemented;
    }

    /**
     * Is invoice completed yet?
     *
     * @return bool
     */
    private function isInvoiceCompleted(): bool
    {
        return $this->isStorageInvoiceImplemented() && $this->isUserInvoiceImplemented();
    }

    /**
     * Decrease user balance on invoice sum.
     *
     *
     * @return bool
     */
    private function fixOutgoingUserInvoiceInBalance(): bool
    {
        // retrieve user
        $user = $this->getUserInvoice()->user;

        return $this->userBalance->addToCreditBalance($user, $this->getInvoiceSum());
    }

    /**
     * Decrease user balance on invoice sum.
     *
     * @return bool
     */
    private function fixIncomingUserInvoiceInBalance(): bool
    {
        // retrieve user
        $user = $this->getUserInvoice()->user;

        return $this->userBalance->addToDebitBalance($user, $this->getInvoiceSum());
    }

    /**
     * Get user invoice.
     *
     * @return UserInvoice
     */
    protected function getUserInvoice():UserInvoice
    {
        return $this->invoice->userInvoices->first();
    }
}