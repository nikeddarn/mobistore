<?php
/**
 * Handler for complete user reclamation invoices.
 */

namespace App\Http\Support\Invoices\Handlers\ProductInvoices\Reclamation\WriteOffDefectProduct;


use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Http\Support\Balance\UserBalance;
use App\Models\UserInvoice;
use Exception;
use Illuminate\Database\DatabaseManager;

final class UserWriteOffReclamationInvoiceHandler extends WriteOffDefectProductManager
{
    /**
     * @var UserBalance
     */
    private $userBalance;

    /**
     * UserStorageReclamationProductInvoiceHandler constructor.
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
     * Set invoice status as cancelled.
     *
     * @return bool
     */
    protected function setInvoiceCancelled(): bool
    {
        if (!$this->isUserInvoiceImplemented()) {
            return parent::setInvoiceCancelled();
        } else {
            return false;
        }
    }

    /**
     * Delete current invoice.
     *
     * @return bool
     * @throws Exception
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
     * Complete user invoice.
     *
     * @return bool
     */
    private function completeUserInvoice()
    {
        if (!$this->setUserInvoiceImplemented()) {
            return false;
        }

        if ($this->isUserInvoiceOutgoing()) {
            return $this->fixUserInvoice() && $this->setInvoiceFinished();
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
     * Add incoming user data to statistics.
     *
     * @return bool
     */
    private function fixUserInvoice(): bool
    {
        // retrieve user
        $user = $this->getUserInvoice()->user;

        // increase user balance on invoice delivery sum
        $this->userBalance->addToCreditBalance($user, $this->invoice->invoice_sum);

        foreach ($this->getInvoiceProducts() as $reclamation) {
            // remove from active user reclamation
            $user->reclamation()->detach($reclamation->id);
        }

        return true;
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
     * Is user invoice incoming?
     *
     * @return bool
     */
    protected function isUserInvoiceOutgoing()
    {
        return $this->getUserInvoice()->direction === InvoiceDirections::OUTGOING;
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