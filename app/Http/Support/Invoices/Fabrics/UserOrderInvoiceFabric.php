<?php
/**
 * User order invoice fabric.
 */

namespace App\Http\Support\Invoices\Fabrics;


use App\Http\Support\Invoices\Creators\UserInvoiceCreator;
use App\Http\Support\Invoices\Handlers\UserStorageProductInvoiceHandler;
use App\Http\Support\Invoices\Repositories\User\UserProductInvoiceRepository;

class UserOrderInvoiceFabric
{
    /**
     * @var UserProductInvoiceRepository
     */
    private $repository;

    /**
     * @var UserStorageProductInvoiceHandler
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
     * @param UserStorageProductInvoiceHandler $handler
     * @param UserInvoiceCreator $creator
     */
    public function __construct(UserProductInvoiceRepository $repository, UserStorageProductInvoiceHandler $handler, UserInvoiceCreator $creator)
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
     * @return UserStorageProductInvoiceHandler
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