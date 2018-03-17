<?php
/**
 * User order invoice fabric.
 */

namespace App\Http\Support\Invoices\Fabrics;


use App\Http\Support\Invoices\Creators\IncomingVendorInvoiceCreator;
use App\Http\Support\Invoices\Handlers\StorageProductInvoiceHandler;
use App\Http\Support\Invoices\Repositories\Vendor\VendorInvoiceRepository;

class VendorIncomingOrderInvoiceFabric
{
    /**
     * @var VendorInvoiceRepository
     */
    private $repository;

    /**
     * @var StorageProductInvoiceHandler
     */
    private $handler;

    /**
     * @var IncomingVendorInvoiceCreator
     */
    private $creator;

    /**
     * Initialize parts of fabric.
     *
     * @param VendorInvoiceRepository $repository
     * @param StorageProductInvoiceHandler $handler
     * @param IncomingVendorInvoiceCreator $creator
     */
    public function __construct(VendorInvoiceRepository $repository, StorageProductInvoiceHandler $handler, IncomingVendorInvoiceCreator $creator)
    {
        $this->repository = $repository;
        $this->handler = $handler;
        $this->creator = $creator;
    }

    /**
     * Get cart repository.
     *
     * @return VendorInvoiceRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Get cart handler.
     *
     * @return StorageProductInvoiceHandler
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * Get cart creator.
     *
     * @return IncomingVendorInvoiceCreator
     */
    public function getCreator()
    {
        return $this->creator;
    }
}