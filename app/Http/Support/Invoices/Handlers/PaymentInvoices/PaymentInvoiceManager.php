<?php
/**
 * Methods for base invoice handling.
 */

namespace App\Http\Support\Invoices\Handlers\PaymentInvoices;


use App\Contracts\Shop\Invoices\InvoiceStatusInterface;
use App\Http\Support\Invoices\Handlers\InvoiceHandler;
use Exception;
use Illuminate\Database\DatabaseManager;

abstract class PaymentInvoiceManager extends InvoiceHandler
{
    /**
     * @var DatabaseManager
     */
    protected $databaseManager;

    /**
     * ShowProductInvoiceHandler constructor.
     *
     * @param DatabaseManager $databaseManager
     */
    public function __construct(DatabaseManager $databaseManager)
    {
        $this->databaseManager = $databaseManager;
    }


    /**
     * Update currency rate of invoice.
     *
     * @param float $rate
     * @return bool
     */
    public function setInvoiceExchangeRate(float $rate)
    {
        $this->invoice->rate = $rate;

        return $this->invoice->save();
    }

    /**
     * Set payment invoice sum.
     *
     * @param float $invoiceSum
     * @return bool
     */
    public function setInvoiceSum(float $invoiceSum):bool
    {
        $this->invoice->invoice_sum = $invoiceSum;
        return $this->invoice->save();
    }

    /**
     * Cancel invoice if it's not implemented
     *
     * @return bool
     */
    public function cancelInvoice():bool
    {
        try {
            $this->databaseManager->beginTransaction();

            if (static::setInvoiceCancelled()) {

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
     * Delete invoice if it's not implemented
     *
     * @return bool
     * @throws \Exception
     */
    public function deleteInvoice():bool
    {
        try {
            $this->databaseManager->beginTransaction();

            if (static::deleteHandlingInvoice()) {

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
    protected function setInvoiceCancelled():bool
    {
        if (!$this->isInvoiceProcessing()){
            return false;
        }

        // set invoice as cancelled
        $this->invoice->invoice_status_id = InvoiceStatusInterface::CANCELLED;
        return $this->invoice->save();
    }

    /**
     * Delete current invoice.
     *
     * @return bool
     * @throws \Exception
     */
    protected function deleteHandlingInvoice():bool
    {
        if (!$this->isInvoiceProcessing()){
            return false;
        }

        return $this->invoice->delete();
    }

    /**
     * Set invoice status as finished.
     *
     * @return bool
     */
    protected function setInvoiceFinished():bool
    {
        $this->invoice->invoice_status_id = InvoiceStatusInterface::FINISHED;

        return $this->invoice->save();
    }

    /**
     * Is invoice processing?
     *
     * @return bool
     */
    protected function isInvoiceProcessing()
    {
        return $this->invoice->invoice_status_id === InvoiceStatusInterface::PROCESSING;
    }
}