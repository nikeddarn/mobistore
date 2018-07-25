<?php
/**
 * Cart invoice fabric.
 */

namespace App\Http\Support\Invoices\Fabrics\User\Product;


use App\Contracts\Shop\Invoices\Creators\InvoiceCreatorInterface;
use App\Contracts\Shop\Invoices\Handlers\InvoiceHandlerInterface;
use App\Contracts\Shop\Invoices\Repositories\InvoiceRepositoryInterface;
use App\Contracts\Shop\Invoices\Viewers\InvoiceViewerInterface;
use App\Http\Support\Invoices\Creators\CartInvoiceCreators\FinalInvoices\CartInvoiceCreator;
use App\Http\Support\Invoices\Handlers\ProductInvoices\Product\Order\CartProduct\CartInvoiceHandler;
use App\Http\Support\Invoices\Repositories\User\CartRepository;
use App\Http\Support\Invoices\Viewers\Product\CartInvoiceViewer;
use App\Models\Invoice;

final class CartInvoiceFabric
{
    /**
     * @var CartRepository
     */
    private $cartRepository;
    /**
     * @var CartInvoiceCreator
     */
    private $cartInvoiceCreator;
    /**
     * @var CartInvoiceHandler
     */
    private $cartInvoiceHandler;
    /**
     * @var CartInvoiceViewer
     */
    private $cartInvoiceViewer;

    /**
     * CartInvoiceFabric constructor.
     * @param CartRepository $cartRepository
     * @param CartInvoiceCreator $cartInvoiceCreator
     * @param CartInvoiceHandler $cartInvoiceHandler
     * @param CartInvoiceViewer $cartInvoiceViewer
     */
    public function __construct(CartRepository $cartRepository, CartInvoiceCreator $cartInvoiceCreator, CartInvoiceHandler $cartInvoiceHandler, CartInvoiceViewer $cartInvoiceViewer)
    {
        $this->cartRepository = $cartRepository;
        $this->cartInvoiceCreator = $cartInvoiceCreator;
        $this->cartInvoiceHandler = $cartInvoiceHandler;
        $this->cartInvoiceViewer = $cartInvoiceViewer;
    }


    /**
     * Get invoice repository.
     *
     * @return InvoiceRepositoryInterface
     */
    public function getRepository(): InvoiceRepositoryInterface
    {
        return $this->cartRepository;
    }

    /**
     * Get creator by invoice type.
     *
     * @return InvoiceCreatorInterface
     */
    public function getCreator(): InvoiceCreatorInterface
    {
        return $this->cartInvoiceCreator;
    }

    /**
     * Get handler by invoice type.
     *
     * @return InvoiceHandlerInterface
     */
    public function getHandler(): InvoiceHandlerInterface
    {
        return $this->cartInvoiceHandler;
    }

    /**
     * Get invoice viewer.
     *
     * @return InvoiceViewerInterface
     */
    public function getViewer(): InvoiceViewerInterface
    {
        return $this->cartInvoiceViewer;
    }

    /**
     * Bind invoice to handler. Return handler.
     *
     * @param Invoice $invoice
     * @return InvoiceHandlerInterface
     */
    public function bindInvoiceToHandler(Invoice $invoice): InvoiceHandlerInterface
    {
        return $this->getHandler()->bindInvoice($invoice);
    }
}