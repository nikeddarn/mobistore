<?php
/**
 * User order invoice fabric.
 */

namespace App\Http\Support\Invoices\Fabrics;


use App\Http\Support\Invoices\Creators\UserInvoiceCreator;
use App\Http\Support\Invoices\Handlers\StorageProductInvoiceHandler;
use App\Http\Support\Invoices\Repositories\User\UserProductInvoiceRepository;

class UserOrderInvoiceFabric
{
    /**
     * @var UserProductInvoiceRepository
     */
    private $repository;

    /**
     * @var StorageProductInvoiceHandler
     */
    private $handler;

    /**
     * @var UserInvoiceCreator
     */
    private $creator;

    /**
     * Initialize parts of fabric.
     *
     * @param UserProductInvoiceRepository $repository
     * @param StorageProductInvoiceHandler $handler
     * @param UserInvoiceCreator $creator
     */
    public function __construct(UserProductInvoiceRepository $repository, StorageProductInvoiceHandler $handler, UserInvoiceCreator $creator)
    {
        $this->repository = $repository;
        $this->handler = $handler;
        $this->creator = $creator;
    }

    /**
     * Get repository.
     *
     * @return UserProductInvoiceRepository
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
     * @return UserInvoiceCreator
     */
    public function getCreator()
    {
        return $this->creator;
    }
}