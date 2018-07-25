<?php
/**
 * User invoice creator.
 */

namespace App\Http\Support\Invoices\Creators\UserInvoiceCreators;

use App\Http\Support\Invoices\Creators\InvoiceCreator;
use App\Models\Invoice;
use App\Models\UserInvoice;
use Exception;
use Illuminate\Database\Eloquent\Model;

abstract class UserInvoiceCreator extends InvoiceCreator
{
    /**
     * @var int
     */
    protected $userId;

    /**
     * Create user invoice model by user's id.
     *
     * @param int $userId
     * @return Invoice
     * @throws Exception
     */
    public function createInvoice(int $userId): Invoice
    {
        try {
            $this->databaseManager->beginTransaction();

            $this->userId = $userId;

            $invoice =  $this->makeInvoice();

            $this->databaseManager->commit();

            return $invoice;

        } catch (Exception $e) {
            $this->databaseManager->rollback();
            throw new Exception($e->getMessage());
        }
    }

    /**
     * Make invoice.
     *
     * @return Invoice
     */
    protected function makeInvoice():Invoice
    {
        $invoice = parent::makeInvoice();

        return $invoice->setRelation('userInvoices', collect()
            ->push($this->makeUserInvoice($invoice))
        );
    }

    /**
     * Get array of data for create Invoice model.
     *
     * @return array
     */
    protected function getInvoiceData():array
    {
        return parent::getInvoiceData();
    }

    /**
     * Get array of data for create UserInvoice model.
     *
     * @return array
     */
    protected function getUserInvoiceData():array
    {
        return [
            'users_id' => $this->userId,
        ];
    }

    /**
     * Make UserInvoice.
     *
     * @param Invoice $invoice
     * @return UserInvoice|Model
     */
    private function makeUserInvoice(Invoice $invoice):UserInvoice
    {
        return $invoice->userInvoices()->create(static::getUserInvoiceData());
    }
}