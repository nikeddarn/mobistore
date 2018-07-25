<?php
/**
 * Handler for complete user reclamation invoices.
 */

namespace App\Http\Support\Invoices\Handlers\ProductInvoices\Reclamation\DefectProduct;


use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Http\Support\Statistics\Product\ProductReclamationStatistics;
use App\Http\Support\Statistics\Product\UserReclamationStatistics;
use Illuminate\Database\DatabaseManager;

final class UserReclamationInvoiceHandler extends UserStorageReclamationProductInvoiceHandler
{
    /**
     * @var ProductReclamationStatistics
     */
    private $productReclamationStatistics;
    /**
     * @var UserReclamationStatistics
     */
    private $userReclamationStatistics;

    /**
     * UserStorageReclamationProductInvoiceHandler constructor.
     * @param DatabaseManager $databaseManager
     * @param ProductReclamationStatistics $productReclamationStatistics
     * @param UserReclamationStatistics $userReclamationStatistics
     */
    public function __construct(DatabaseManager $databaseManager, ProductReclamationStatistics $productReclamationStatistics, UserReclamationStatistics $userReclamationStatistics)
    {
        parent::__construct($databaseManager);
        $this->productReclamationStatistics = $productReclamationStatistics;
        $this->userReclamationStatistics = $userReclamationStatistics;
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

        if ($this->isUserInvoiceImplemented() && $this->isStorageInvoiceIncoming()) {
            return $this->fixIncomingStorageInvoice() && $this->fixOutgoingUserInvoice() && $this->setInvoiceFinished();
        }

        return true;
    }

    /**
     * Complete user invoice.
     *
     * @return bool
     */
    protected function completeUserInvoice()
    {
        if (!parent::completeUserInvoice()) {
            return false;
        }

        if ($this->isStorageInvoiceImplemented() && $this->isUserInvoiceIncoming()) {

            return $this->fixOutgoingStorageInvoice() && $this->fixIncomingUserInvoice() && $this->setInvoiceFinished();
        }

        return true;
    }

    /**
     * Add incoming storage data to statistics.
     *
     * @return bool
     */
    private function fixIncomingStorageInvoice(): bool
    {
        // get storage department
        $storageDepartment = $this->invoice->incomingStorageDepartment()->first();

        foreach ($this->getInvoiceProducts() as $reclamation) {

            // add from active storage reclamation
            $storageDepartment->reclamation()->attach($reclamation->id);

            // update statistics
            if (!$this->productReclamationStatistics->takeAcceptedReclamationInvoiceToStatistic($reclamation)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Add outgoing storage data to statistics.
     *
     * @return bool
     */
    private function fixOutgoingStorageInvoice(): bool
    {
        // get storage department
        $storageDepartment = $this->invoice->outgoingStorageDepartment()->first();

        foreach ($this->getInvoiceProducts() as $reclamation) {

            // remove from active storage reclamation
            $storageDepartment->reclamation()->detach($reclamation->id);

            // update statistics
            if (!$this->productReclamationStatistics->takeRejectedReclamationInvoiceToStatistic($reclamation)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Add outgoing user data to statistics.
     *
     * @return bool
     */
    private function fixOutgoingUserInvoice(): bool
    {
        // retrieve user
        $user = $this->getUserInvoice()->user;

        foreach ($this->getInvoiceProducts() as $reclamation) {
            // remove from active storage reclamation
            $user->reclamation()->attach($reclamation->id);
        }

        return $this->userReclamationStatistics->takeAcceptedReclamationInvoiceToStatistic($user, $this->getProductsCount());
    }

    /**
     * Add incoming user data to statistics.
     *
     * @return bool
     */
    private function fixIncomingUserInvoice(): bool
    {
        // retrieve user
        $user = $this->getUserInvoice()->user;

        foreach ($this->getInvoiceProducts() as $reclamation) {
            // remove from active storage reclamation
            $user->reclamation()->detach($reclamation->id);
        }

        return $this->userReclamationStatistics->takeRejectedReclamationInvoiceToStatistic($user, $this->getProductsCount());
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
}