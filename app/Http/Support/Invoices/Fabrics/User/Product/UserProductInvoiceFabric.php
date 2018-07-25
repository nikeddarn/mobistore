<?php
/**
 * User product invoice fabric.
 */

namespace App\Http\Support\Invoices\Fabrics\User\Product;


use App\Contracts\Shop\Invoices\Creators\InvoiceCreatorInterface;
use App\Contracts\Shop\Invoices\Handlers\InvoiceHandlerInterface;
use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Contracts\Shop\Invoices\Repositories\InvoiceRepositoryInterface;
use App\Contracts\Shop\Invoices\Viewers\InvoiceViewerInterface;
use App\Http\Support\Invoices\Creators\UserInvoiceCreators\FinalInvoices\UserOrderCreator;
use App\Http\Support\Invoices\Creators\UserInvoiceCreators\FinalInvoices\UserPreOrderCreator;
use App\Http\Support\Invoices\Creators\UserInvoiceCreators\FinalInvoices\UserReturnOrderCreator;
use App\Http\Support\Invoices\Fabrics\InvoiceFabric;
use App\Http\Support\Invoices\Handlers\ProductInvoices\Product\Order\OrderProduct\UserOrderInvoiceHandler;
use App\Http\Support\Invoices\Repositories\User\Product\UserProductInvoiceRepository;
use App\Http\Support\Invoices\Viewers\Product\UserProductInvoiceViewer;
use Exception;

final class UserProductInvoiceFabric extends InvoiceFabric
{
    /**
     * @var UserProductInvoiceRepository
     */
    private $userProductInvoiceRepository;
    /**
     * @var UserOrderCreator
     */
    private $userOrderCreator;
    /**
     * @var UserPreOrderCreator
     */
    private $userPreOrderCreator;
    /**
     * @var UserReturnOrderCreator
     */
    private $userReturnOrderCreator;
    /**
     * @var UserOrderInvoiceHandler
     */
    private $userOrderInvoiceHandler;
    /**
     * @var UserProductInvoiceViewer
     */
    private $userProductInvoiceViewer;

    /**
     * CartInvoiceFabric constructor.
     * @param UserProductInvoiceRepository $userProductInvoiceRepository
     * @param UserOrderCreator $userOrderCreator
     * @param UserPreOrderCreator $userPreOrderCreator
     * @param UserReturnOrderCreator $userReturnOrderCreator
     * @param UserOrderInvoiceHandler $userOrderInvoiceHandler
     * @param UserProductInvoiceViewer $userProductInvoiceViewer
     */
    public function __construct(UserProductInvoiceRepository $userProductInvoiceRepository, UserOrderCreator $userOrderCreator, UserPreOrderCreator $userPreOrderCreator, UserReturnOrderCreator $userReturnOrderCreator, UserOrderInvoiceHandler $userOrderInvoiceHandler, UserProductInvoiceViewer $userProductInvoiceViewer)
    {
        $this->userProductInvoiceRepository = $userProductInvoiceRepository;
        $this->userOrderCreator = $userOrderCreator;
        $this->userPreOrderCreator = $userPreOrderCreator;
        $this->userReturnOrderCreator = $userReturnOrderCreator;
        $this->userOrderInvoiceHandler = $userOrderInvoiceHandler;
        $this->userProductInvoiceViewer = $userProductInvoiceViewer;
    }


    /**
     * Get invoice repository.
     *
     * @return InvoiceRepositoryInterface
     */
    protected function getRepository(): InvoiceRepositoryInterface
    {
        return new UserProductInvoiceRepository($this->invoice);
    }

    /**
     * Get creator by invoice type.
     *
     * @param int $invoiceType
     * @return InvoiceCreatorInterface
     * @throws Exception
     */
    protected function getCreator(int $invoiceType): InvoiceCreatorInterface
    {
        switch ($invoiceType) {

            case InvoiceTypes::USER_ORDER:
                return $this->userOrderCreator;

            case InvoiceTypes::USER_PRE_ORDER:
                return $this->userPreOrderCreator;

            case InvoiceTypes::USER_RETURN_ORDER:
                return $this->userReturnOrderCreator;

            default:
                throw new Exception('Cant construct invoice creator. Wrong invoice type.');
        }
    }

    /**
     * Get handler by invoice type.
     *
     * @param int $invoiceType
     * @return InvoiceHandlerInterface
     * @throws Exception
     */
    protected function getHandler(int $invoiceType): InvoiceHandlerInterface
    {
        switch ($invoiceType) {

            case InvoiceTypes::USER_ORDER:
            case InvoiceTypes::USER_PRE_ORDER:
            case InvoiceTypes::USER_RETURN_ORDER:
                return $this->userOrderInvoiceHandler;

            default:
                throw new Exception('Cant construct invoice handler. Wrong invoice type.');
        }
    }

    /**
     * Get invoice viewer.
     *
     * @param int $invoiceType
     * @return InvoiceViewerInterface
     * @throws Exception
     */
    protected function getViewer(int $invoiceType): InvoiceViewerInterface
    {
        switch ($invoiceType) {

            case InvoiceTypes::USER_ORDER:
            case InvoiceTypes::USER_PRE_ORDER:
            case InvoiceTypes::USER_RETURN_ORDER:
                return $this->userProductInvoiceViewer;

            default:
                throw new Exception('Cant construct invoice viewer. Wrong invoice type.');
        }
    }
}