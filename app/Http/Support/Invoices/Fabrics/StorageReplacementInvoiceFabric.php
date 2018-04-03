<?php
/**
 * Vendor order invoice fabric.
 */

namespace App\Http\Support\Invoices\Fabrics;


use App\Http\Support\Invoices\Creators\StorageReplacementInvoiceCreator;
use App\Http\Support\Invoices\Handlers\StorageProductInvoiceHandler;
use App\Http\Support\Invoices\Repositories\Storage\StorageInvoiceRepository;

class StorageReplacementInvoiceFabric
{
    /**
     * @var StorageInvoiceRepository
     */
    private $repository;

    /**
     * @var StorageProductInvoiceHandler
     */
    private $handler;

    /**
     * @var StorageReplacementInvoiceCreator
     */
    private $creator;

    /**
     * Initialize parts of fabric.
     *
     * @param StorageInvoiceRepository $repository
     * @param StorageProductInvoiceHandler $handler
     * @param StorageReplacementInvoiceCreator $creator
     */
    public function __construct(StorageInvoiceRepository $repository, StorageProductInvoiceHandler $handler, StorageReplacementInvoiceCreator $creator)
    {
        $this->repository = $repository;
        $this->handler = $handler;
        $this->creator = $creator;
    }

    /**
     * Get repository.
     *
     * @return StorageInvoiceRepository
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
     * @return StorageReplacementInvoiceCreator
     */
    public function getCreator()
    {
        return $this->creator;
    }
}