<?php
/**
 * User order invoice fabric.
 */

namespace App\Http\Support\Invoices\Fabrics;


use App\Http\Support\Invoices\Creators\OutgoingUserInvoiceCreator;
use App\Http\Support\Invoices\Handlers\OutgoingUserInvoiceHandler;
use App\Http\Support\Invoices\Repositories\User\UserProductInvoiceRepository;

class UserOutgoingOrderInvoiceFabric
{
    /**
     * @var UserProductInvoiceRepository
     */
    private $repository;

    /**
     * @var OutgoingUserInvoiceHandler
     */
    private $handler;

    /**
     * @var OutgoingUserInvoiceCreator
     */
    private $creator;

    /**
     * Initialize parts of fabric.
     *
     * @param UserProductInvoiceRepository $repository
     * @param OutgoingUserInvoiceHandler $handler
     * @param OutgoingUserInvoiceCreator $creator
     */
    public function __construct(UserProductInvoiceRepository $repository, OutgoingUserInvoiceHandler $handler, OutgoingUserInvoiceCreator $creator)
    {
        $this->repository = $repository;
        $this->handler = $handler;
        $this->creator = $creator;
    }

    /**
     * Get cart repository.
     *
     * @return UserProductInvoiceRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Get cart handler.
     *
     * @return OutgoingUserInvoiceHandler
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * Get cart creator.
     *
     * @return OutgoingUserInvoiceCreator
     */
    public function getCreator()
    {
        return $this->creator;
    }
}