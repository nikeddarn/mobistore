<?php
/**
 * Cart invoice fabric.
 */

namespace App\Http\Support\Invoices\Fabrics;


use App\Http\Support\Invoices\Handlers\CartInvoiceHandler;
use App\Http\Support\Invoices\Repositories\User\CartRepository;
use App\Http\Support\Invoices\Creators\CartInvoiceCreator;
use App\Http\Support\Invoices\Handlers\ProductInvoiceHandler;

class CartInvoiceFabric
{
    /**
     * @var CartRepository
     */
    private $repository;

    /**
     * @var ProductInvoiceHandler
     */
    private $handler;

    /**
     * @var CartInvoiceCreator
     */
    private $creator;

    /**
     * Initialize parts of fabric.
     *
     * @param CartRepository $repository
     * @param CartInvoiceHandler $handler
     * @param CartInvoiceCreator $creator
     */
    public function __construct(CartRepository $repository, CartInvoiceHandler $handler, CartInvoiceCreator $creator)
    {
        $this->repository = $repository;
        $this->handler = $handler;
        $this->creator = $creator;
    }

    /**
     * Get cart repository.
     *
     * @return CartRepository
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * Get cart handler.
     *
     * @return ProductInvoiceHandler
     */
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * Get cart creator.
     *
     * @return CartInvoiceCreator
     */
    public function getCreator()
    {
        return $this->creator;
    }
}