<?php
/**
 * Methods for base invoice handling.
 */

namespace App\Http\Support\Invoices\Handlers\ProductInvoices;


use App\Contracts\Shop\Invoices\InvoiceStatusInterface;
use App\Http\Support\Invoices\Handlers\InvoiceHandler;
use Exception;
use Illuminate\Database\DatabaseManager;

abstract class ProductInvoiceManager extends InvoiceHandler
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
     * Set invoice delivery sum.
     *
     * @param float $deliverySum
     */
    public function setInvoiceDeliverySum(float $deliverySum)
    {
        $this->invoice->delivery_sum = $deliverySum;
        $this->invoice->invoice_sum += $deliverySum;
        $this->invoice->save();
    }

    /**
     * Bind invoice with shipment by its id
     *
     * @param int $shipmentId
     * @return bool
     */
    public function bindInvoiceToShipment(int $shipmentId)
    {
        $this->invoice->shipments_id = $shipmentId;
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

    /**
     * Is invoice cancelled?
     *
     * @return bool
     */
    protected function isInvoiceCancelled()
    {
        return $this->invoice->invoice_status_id === InvoiceStatusInterface::CANCELLED;
    }

    /**
     * Increase invoice sum.
     *
     * @param float $sum
     * @return bool
     */
    protected function increaseInvoiceSum(float $sum)
    {
        // increase total invoice sum
        $this->invoice->invoice_sum += $sum;

        return $this->invoice->save();
    }

    /**
     * Decrease invoice sum.
     *
     * @param float $sum
     * @return bool
     */
    protected function decreaseInvoiceSum(float $sum)
    {
        $this->invoice->invoice_sum -= $sum;

        return $this->invoice->save();
    }
}