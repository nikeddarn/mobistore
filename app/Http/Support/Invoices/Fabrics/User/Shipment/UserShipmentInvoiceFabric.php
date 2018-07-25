<?php
/**
 * User balance invoice fabric.
 */

namespace App\Http\Support\Invoices\Fabrics\User\Product;


use App\Contracts\Shop\Invoices\Repositories\InvoiceRepositoryInterface;
use App\Contracts\Shop\Invoices\Viewers\InvoiceViewerInterface;
use App\Http\Support\Invoices\Repositories\User\UserBalanceInvoiceRepository;
use App\Http\Support\Invoices\Viewers\Balance\UserBalanceInvoiceViewer;

final class UserShipmentInvoiceFabric
{
    /**
     * @var UserBalanceInvoiceRepository
     */
    private $userBalanceInvoiceRepository;
    /**
     * @var UserBalanceInvoiceViewer
     */
    private $userBalanceInvoiceViewer;

    /**
     * UserBalanceInvoiceFabric constructor.
     * @param UserBalanceInvoiceRepository $userBalanceInvoiceRepository
     * @param UserBalanceInvoiceViewer $userBalanceInvoiceViewer
     */
    public function __construct(UserBalanceInvoiceRepository $userBalanceInvoiceRepository, UserBalanceInvoiceViewer $userBalanceInvoiceViewer)
    {
        $this->userBalanceInvoiceRepository = $userBalanceInvoiceRepository;
        $this->userBalanceInvoiceViewer = $userBalanceInvoiceViewer;
    }


    /**
     * Get invoice repository.
     *
     * @return InvoiceRepositoryInterface
     */
    public function getRepository(): InvoiceRepositoryInterface
    {
        return $this->userBalanceInvoiceRepository;
    }

    /**
     * Get invoice viewer.
     *
     * @return InvoiceViewerInterface
     */
    public function getViewer(): InvoiceViewerInterface
    {
        return $this->userBalanceInvoiceViewer;
    }
}