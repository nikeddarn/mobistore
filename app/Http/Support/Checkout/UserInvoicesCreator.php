<?php
/**
 * Created user, storage and vendor invoices to collect user order.
 */

namespace App\Http\Support\Checkout;


use App\Http\Support\Invoices\Fabrics\StorageReplacementInvoiceFabric;
use App\Http\Support\Invoices\Fabrics\UserOrderInvoiceFabric;
use App\Http\Support\Invoices\Fabrics\VendorOrderInvoiceFabric;
use App\Models\Invoice;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Collection;

class UserInvoicesCreator
{
    /**
     * @var UserOrderInvoiceFabric
     */
    private $userOrderInvoiceFabric;

    /**
     * @var VendorOrderInvoiceFabric
     */
    private $vendorOrderInvoiceFabric;

    /**
     * @var StorageReplacementInvoiceFabric
     */
    private $storageReplacementInvoiceFabric;

    /**
     * @var DatabaseManager
     */
    private $databaseManager;

    /**
     * UserInvoicesCreator constructor.
     *
     * @param DatabaseManager $databaseManager
     * @param UserOrderInvoiceFabric $userOrderInvoiceFabric
     * @param VendorOrderInvoiceFabric $vendorOrderInvoiceFabric
     * @param StorageReplacementInvoiceFabric $storageReplacementInvoiceFabric
     */
    public function __construct(DatabaseManager $databaseManager, UserOrderInvoiceFabric $userOrderInvoiceFabric, VendorOrderInvoiceFabric $vendorOrderInvoiceFabric, StorageReplacementInvoiceFabric $storageReplacementInvoiceFabric)
    {
        $this->userOrderInvoiceFabric = $userOrderInvoiceFabric;
        $this->vendorOrderInvoiceFabric = $vendorOrderInvoiceFabric;
        $this->storageReplacementInvoiceFabric = $storageReplacementInvoiceFabric;
        $this->databaseManager = $databaseManager;
    }

    /**
     * Create online order invoices.
     *
     * @param Collection $products
     * @param array $storageInvoices
     * @return Invoice
     * @throws \Exception
     */
    public function createStorageInvoices(Collection $products, array $storageInvoices):Invoice
    {
        $this->databaseManager->beginTransaction();

        // if multi storage && config auto define collect storage else without storage invoice

        // if multi storage && config auto replace by storages -> create all replacement

        $this->databaseManager->commit();
    }

    /**
     * Create vendor order invoices.
     *
     * @param Collection $products
     * @param array $vendorInvoices
     * @return Invoice
     * @throws \Exception
     */
    public function createVendorInvoices(Collection $products, array $vendorInvoices):Invoice
    {
        $this->databaseManager->beginTransaction();

        $this->databaseManager->commit();
    }
}