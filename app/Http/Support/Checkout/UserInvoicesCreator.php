<?php
/**
 * Created user, storage and vendor invoices to collect user order.
 */

namespace App\Http\Support\Checkout;


use App\Contracts\Shop\Invoices\Handlers\UserProductInvoiceHandlerInterface;
use App\Contracts\Shop\Invoices\InvoiceDirections;
use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Http\Support\Invoices\Fabrics\StorageReplacementInvoiceFabric;
use App\Http\Support\Invoices\Fabrics\UserOrderInvoiceFabric;
use App\Http\Support\Invoices\Fabrics\VendorOrderInvoiceFabric;
use App\Http\Support\Invoices\Handlers\StorageProductInvoiceHandler;
use App\Http\Support\Routers\ProductRouter;
use App\Models\Invoice;
use App\Models\UserInvoiceHasVendorInvoice;
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
     * @var ProductRouter
     */
    private $productRouter;

    /**
     * @var UserInvoiceHasVendorInvoice
     */
    private $userInvoiceHasVendorInvoice;

    /**
     * UserInvoicesCreator constructor.
     *
     * @param DatabaseManager $databaseManager
     * @param UserOrderInvoiceFabric $userOrderInvoiceFabric
     * @param VendorOrderInvoiceFabric $vendorOrderInvoiceFabric
     * @param StorageReplacementInvoiceFabric $storageReplacementInvoiceFabric
     * @param ProductRouter $productRouter
     * @param UserInvoiceHasVendorInvoice $userInvoiceHasVendorInvoice
     */
    public function __construct(DatabaseManager $databaseManager, UserOrderInvoiceFabric $userOrderInvoiceFabric, VendorOrderInvoiceFabric $vendorOrderInvoiceFabric, StorageReplacementInvoiceFabric $storageReplacementInvoiceFabric, ProductRouter $productRouter, UserInvoiceHasVendorInvoice $userInvoiceHasVendorInvoice)
    {
        $this->userOrderInvoiceFabric = $userOrderInvoiceFabric;
        $this->vendorOrderInvoiceFabric = $vendorOrderInvoiceFabric;
        $this->storageReplacementInvoiceFabric = $storageReplacementInvoiceFabric;
        $this->databaseManager = $databaseManager;
        $this->productRouter = $productRouter;
        $this->userInvoiceHasVendorInvoice = $userInvoiceHasVendorInvoice;
    }

    /**
     * Create online order invoices.
     *
     * @param Collection $products
     * @param array $storageInvoices
     * @param int $orderDeliveryCity
     * @return UserProductInvoiceHandlerInterface
     * @throws \Exception
     */
    public function createUserOrderInvoices(Collection $products, array $storageInvoices, int $orderDeliveryCity): UserProductInvoiceHandlerInterface
    {
        $this->databaseManager->beginTransaction();

        // define user order invoice storage
        $invoiceStorageId = $this->productRouter->defineCollectingOrderStorage(array_keys($storageInvoices), $orderDeliveryCity);

        if (config('shop.invoice.order.create_outgoing_storage_invoice')) {
            // create user invoice with outgoing storage invoice
            $userInvoiceHandler = $this->createUserInvoice(InvoiceTypes::USER_ORDER, $products, $invoiceStorageId);
        }else{
            // create user invoice w/o outgoing storage invoice
            $userInvoiceHandler = $this->createUserInvoice(InvoiceTypes::USER_ORDER, $products);
        }

        // allow auto create replacement between storages invoices
        if (config('shop.invoice.order.create_replacement_storage_invoice')) {
            // create replacement of products between storages
            $this->createReplacementInvoices($storageInvoices, $invoiceStorageId);
        }

        $this->databaseManager->commit();

        return $userInvoiceHandler;
    }

    /**
     * Create vendor order invoices.
     *
     * @param Collection $products
     * @param array $vendorInvoices
     * @param int $orderDeliveryCity
     * @return UserProductInvoiceHandlerInterface
     * @throws \Exception
     */
    public function createUserPreOrderInvoices(Collection $products, array $vendorInvoices, int $orderDeliveryCity): UserProductInvoiceHandlerInterface
    {
        $this->databaseManager->beginTransaction();

        // define user pre order invoice storage
        $invoiceStorageId = $this->productRouter->defineCollectingPreOrderStorage(array_keys($vendorInvoices), $orderDeliveryCity);

        if (config('shop.invoice.pre_order.create_outgoing_storage_invoice')) {
            // create user invoice with outgoing storage invoice
            $userInvoiceHandler = $this->createUserInvoice(InvoiceTypes::USER_PRE_ORDER, $products, $invoiceStorageId);
        }else{
            // create user invoice w/o outgoing storage invoice
            $userInvoiceHandler = $this->createUserInvoice(InvoiceTypes::USER_PRE_ORDER, $products);
        }

        // create vendor invoices and link them with user invoice
        if (config('shop.invoice.pre_order.create_vendor_invoice')) {
            $createdVendorInvoices = $this->createVendorInvoices($vendorInvoices, $invoiceStorageId);
            $this->linkVendorInvoicesWithUserInvoice($userInvoiceHandler->getInvoice(), $createdVendorInvoices);
        }

        $this->databaseManager->commit();

        return $userInvoiceHandler;
    }

    /**
     * Create user invoice with products.
     *
     * @param string $invoiceType
     * @param int $outgoingStorageId
     * @param Collection $products
     * @return UserProductInvoiceHandlerInterface
     * @throws \Exception
     */
    private function createUserInvoice(string $invoiceType, Collection $products, int $outgoingStorageId = null): UserProductInvoiceHandlerInterface
    {
        $invoiceCreator = $this->userOrderInvoiceFabric->getCreator();
        $invoiceHandler = $this->userOrderInvoiceFabric->getHandler();

        // get user
        $user = auth('web')->user();

        // create user invoice
        $userInvoice = $invoiceCreator->createInvoice($invoiceType, $user->id, InvoiceDirections::INCOMING, $outgoingStorageId);

        // bind invoice to handler
        $invoiceHandler->bindInvoice($userInvoice);

        // append products to invoice
        $this->appendProducts($invoiceHandler, $products);

        return $invoiceHandler;
    }

    /**
     * Create replacement between storages invoices.
     *
     * @param array $replacementInvoices
     * @param int $incomingStorageId
     * @throws \Exception
     */
    private function createReplacementInvoices(array $replacementInvoices, int $incomingStorageId)
    {
        $invoiceCreator = $this->storageReplacementInvoiceFabric->getCreator();
        $invoiceHandler = $this->storageReplacementInvoiceFabric->getHandler();

        // create invoices
        foreach ($replacementInvoices as $outgoingStorageId => $products) {

            // this storage is defined as collecting. nothing to replace
            if ($outgoingStorageId === $incomingStorageId) {
                break;
            }

            // create invoice to replace product from outgoing storage to defined storage for collect user invoice
            $replacementInvoice = $invoiceCreator->createInvoice($outgoingStorageId, $incomingStorageId);
            // bind invoice to handler
            $invoiceHandler->bindInvoice($replacementInvoice);
            // append products to invoice
            $this->appendProducts($invoiceHandler, $products);
        }
    }

    /**
     * Create vendor invoices.
     *
     * @param array $vendorInvoices
     * @param int $incomingStorageId
     * @return Collection
     * @throws \Exception
     */
    private function createVendorInvoices(array $vendorInvoices, int $incomingStorageId):Collection
    {
        $createdVendorInvoices = collect();

        $invoiceCreator = $this->vendorOrderInvoiceFabric->getCreator();
        $invoiceHandler = $this->vendorOrderInvoiceFabric->getHandler();

        // create vendor invoices
        foreach ($vendorInvoices as $vendorId => $products) {
            // create invoice
            $vendorInvoice = $invoiceCreator->createInvoice(InvoiceTypes::USER_PRE_ORDER, $vendorId, InvoiceDirections::OUTGOING, $incomingStorageId);
            // bind invoice to handler
            $invoiceHandler->bindInvoice($vendorInvoice);
            // append products to invoice
            $this->appendProducts($invoiceHandler, $products);
            // push created invoice in collection
            $createdVendorInvoices->push($invoiceHandler->getInvoice());
        }

        return $createdVendorInvoices;
    }

    /**
     * Append products to invoice.
     *
     * @param StorageProductInvoiceHandler $invoiceHandler
     * @param Collection $products
     */
    private function appendProducts(StorageProductInvoiceHandler $invoiceHandler, Collection $products)
    {
        foreach ($products as $product) {
            $invoiceHandler->addProduct($product->products_id, $product->price, $product->quantity);
        }
    }

    /**
     * Create a link between the user invoice and the vendor invoices that it contains.
     *
     * @param Invoice $userPreOrderInvoice
     * @param Collection $vendorPreOrderInvoices
     */
    private function linkVendorInvoicesWithUserInvoice(Invoice $userPreOrderInvoice, Collection $vendorPreOrderInvoices)
    {
        $userInvoiceId = $userPreOrderInvoice->userInvoice->id;

        $vendorPreOrderInvoices->each(function (Invoice $vendorPreOrderInvoice) use ($userInvoiceId){
            $this->userInvoiceHasVendorInvoice->create([
                'user_invoices_id' => $userInvoiceId,
                'vendor_invoices_id' => $vendorPreOrderInvoice->vendorInvoice->id,
            ]);
        });
    }
}