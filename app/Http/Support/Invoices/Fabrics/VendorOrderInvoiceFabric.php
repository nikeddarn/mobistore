<?php
/**
 * Vendor order invoice fabric.
 */

namespace App\Http\Support\Invoices\Fabrics;


use App\Http\Support\Invoices\Creators\VendorInvoiceCreator;
use App\Http\Support\Invoices\Handlers\StorageProductInvoiceHandler;
use App\Http\Support\Invoices\Repositories\Vendor\VendorProductInvoiceRepository;

class VendorOrderInvoiceFabric
{
    /**
     * @var VendorProductInvoiceRepository
     */
    private $repository;

    /**
     * @var StorageProductInvoiceHandler
     */
    private $handler;

    /**
     * @var VendorInvoiceCreator
     */
    private $creator;

    /**
     * Initialize parts of fabric.
     *
     * @param VendorProductInvoiceRepository $repository
     * @param StorageProductInvoiceHandler $handler
     * @param VendorInvoiceCreator $creator
     */
    public function __construct(VendorProductInvoiceRepository $repository, StorageProductInvoiceHandler $handler, VendorInvoiceCreator $creator)
    {
        $this->repository = $repository;
        $this->handler = $handler;
        $this->creator = $creator;
    }

    /**
     * Get repository.
     *
     * @return VendorProductInvoiceRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Get handler.
     *
     * @return StorageProductInvoiceHandler
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * Get creator.
     *
     * @return VendorInvoiceCreator
     */
    public function getCreator()
    {
        return $this->creator;
    }
}