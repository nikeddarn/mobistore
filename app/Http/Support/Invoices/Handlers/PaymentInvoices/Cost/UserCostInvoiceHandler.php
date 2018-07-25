<?php
/**
 * Handle payment user storage invoices.
 */

namespace App\Http\Support\Invoices\Handlers\PaymentInvoices\Cost;


use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Http\Support\Balance\UserBalance;
use App\Http\Support\Invoices\Handlers\PaymentInvoices\PaymentInvoiceManager;
use App\Models\UserInvoice;
use Exception;
use Illuminate\Database\DatabaseManager;

final class UserCostInvoiceHandler extends PaymentInvoiceManager
{
    /**
     * @var UserBalance
     */
    private $userBalance;

    /**
     * UserPaymentInvoiceHandler constructor.
     * @param DatabaseManager $databaseManager
     * @param UserBalance $userBalance
     */
    public function __construct(DatabaseManager $databaseManager, UserBalance $userBalance)
    {
        parent::__construct($databaseManager);
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
     * Complete user invoice.
     *
     * @return bool
     */
    private function completeUserInvoice()
    {
        if (!$this->setUserInvoiceImplemented()){
            return false;
        }

        // fix incoming user invoice
        if ($this->isUserInvoiceIncoming()) {
            return  $this->fixIncomingUserInvoiceInBalance() && $this->setInvoiceFinished();
        }

        // fix outgoing user invoice
        if ($this->isUserInvoiceOutgoing()) {
            return  $this->fixOutgoingUserInvoiceInBalance() && $this->setInvoiceFinished();
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
    protected function setInvoiceCancelled():bool
    {
        if ($this->isUserInvoiceImplemented()){
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
    protected function deleteHandlingInvoice():bool
    {
        if ($this->isUserInvoiceImplemented()){
            return false;
        }

        return parent::deleteHandlingInvoice();
    }

    /**
     * Is user invoice incoming?
     *
     * @return bool
     */
    private function isUserInvoiceIncoming()
    {
        return $this->getUserInvoice()->direction === InvoiceDirections::INCOMING;
    }

    /**
     * Is user invoice outgoing?
     *
     * @return bool
     */
    private function isUserInvoiceOutgoing()
    {
        return $this->getUserInvoice()->direction === InvoiceDirections::OUTGOING;
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
     * Increase user balance on invoice sum.
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
     * Decrease user balance on invoice sum.
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
     * Get user invoice.
     *
     * @return UserInvoice
     */
    private function getUserInvoice():UserInvoice
    {
        return $this->invoice->userInvoices->first();
    }
}