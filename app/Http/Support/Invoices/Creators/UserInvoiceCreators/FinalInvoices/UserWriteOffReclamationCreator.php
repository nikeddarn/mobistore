<?php
/**
 * Create write off user reclamation invoice.
 */

namespace App\Http\Support\Invoices\Creators\UserInvoiceCreators\FinalInvoices;


use App\Contracts\Shop\Invoices\Creators\InvoiceCreatorInterface;
use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Http\Support\Invoices\Creators\UserInvoiceCreators\UserInvoiceCreator;

class UserWriteOffReclamationCreator extends UserInvoiceCreator implements InvoiceCreatorInterface
{
    /**
     * Get array of data for create Invoice model.
     *
     * @return array
     */
    protected function getInvoiceData():array
    {
        return array_merge(parent::getInvoiceData(), [
            'invoice_types_id' => InvoiceTypes::USER_WRITE_OFF_RECLAMATION,
        ]);
    }

    /**
     * Get array of data for create UserInvoice model.
     *
     * @return array
     */
    protected function getUserInvoiceData():array
    {
        return array_merge(parent::getUserInvoiceData(), [
            'direction' => InvoiceDirections::OUTGOING,
        ]);
    }
}