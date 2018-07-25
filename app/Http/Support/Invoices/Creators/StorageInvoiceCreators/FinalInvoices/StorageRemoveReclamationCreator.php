<?php
/**
 * Create storage remove reclamation invoice.
 */

namespace App\Http\Support\Invoices\Creators\StorageInvoiceCreators\FinalInvoices;


use App\Contracts\Shop\Invoices\Creators\InvoiceCreatorInterface;
use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Http\Support\Invoices\Creators\StorageInvoiceCreators\StoragesInvoiceCreator;

class StorageRemoveReclamationCreator extends StoragesInvoiceCreator implements InvoiceCreatorInterface
{
    /**
     * Get array of data for create Invoice model.
     *
     * @return array
     */
    protected function getInvoiceData():array
    {
        return array_merge(parent::getInvoiceData(), [
            'invoice_types_id' => InvoiceTypes::STORAGE_REMOVE_RECLAMATION,
        ]);
    }
}