<?php
/**
 * Invoice products sorter.
 * Sort by invoice types.
 * Sort by storages and vendors invoices for creator.
 */

namespace App\Http\Support\Checkout;


use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Http\Support\ProductRepository\StorageProductRepository;
use App\Http\Support\ProductRepository\VendorProductRepository;
use App\Http\Support\Routers\ProductRouter;
use Illuminate\Support\Collection;

class UserInvoiceProductsSorter
{
    /**
     * @var StorageProductRepository
     */
    private $storageProductRepository;

    /**
     * @var VendorProductRepository
     */
    private $vendorProductRepository;

    /**
     * @var ProductRouter
     */
    private $productRouter;

    /**
     * UserInvoiceProductsSorter constructor.
     * @param StorageProductRepository $storageProductRepository
     * @param VendorProductRepository $vendorProductRepository
     * @param ProductRouter $storageProductRouter
     */
    public function __construct(StorageProductRepository $storageProductRepository, VendorProductRepository $vendorProductRepository, ProductRouter $storageProductRouter)
    {
        $this->storageProductRepository = $storageProductRepository;
        $this->vendorProductRepository = $vendorProductRepository;
        $this->productRouter = $storageProductRouter;
    }

    /**
     * Sort collection of product by order and pre order.
     *
     * @param Collection $products
     * @return array
     */
    public function sortProductsByOrderType(Collection $products): array
    {
        $sortedProducts = [];

        $keyedProducts = $products->keyBy('products_id');

        // define products count that available on storages
        $storageAvailableProducts = $this->storageProductRepository->getAvailableProductsCountById($keyedProducts->keys()->toArray());

        // some of products are present on storages
        if ($storageAvailableProducts) {
            // collect storages products
            $storageInvoiceProducts = collect();
            // iterate each product of invoice products
            foreach ($keyedProducts as $invoiceProduct) {
                // handling product are present on some storages
                if (array_key_exists($invoiceProduct->products_id, $storageAvailableProducts)) {
                    // collect available count of product
                    $storageProduct = clone ($invoiceProduct);
                    $orderingCount = min($invoiceProduct->quantity, $storageAvailableProducts[$invoiceProduct->products_id]);
                    $storageProduct->quantity = $orderingCount;
                    $storageInvoiceProducts->push($storageProduct);
                    $invoiceProduct->quantity -= $orderingCount;
                    // forget product from original collection if all needing count of products are reserved
                    if ($invoiceProduct->quantity === 0) {
                        $keyedProducts->forget($invoiceProduct->products_id);
                    }
                }
            }
            // add storages products to sorted result
            if ($storageInvoiceProducts->count()) {
                $sortedProducts[InvoiceTypes::ORDER] = $storageInvoiceProducts;
            }
        }

        // not all products available on storages. collect from vendors
        if ($keyedProducts->count()) {
            // define products count that available on vendors
            $vendorAvailableProducts = $this->vendorProductRepository->getAvailableProductsCountById($keyedProducts->keys()->toArray());
            // some of products are present on vendors
            if ($vendorAvailableProducts) {
                // collect vendors products
                $vendorInvoiceProducts = collect();
                // iterate each product of invoice products
                foreach ($keyedProducts as $invoiceProduct) {
                    // handling product are present on some vendors
                    if (array_key_exists($invoiceProduct->products_id, $vendorAvailableProducts)) {
                        // collect available count of product
                        $vendorProduct = clone ($invoiceProduct);
                        $orderingCount = min($invoiceProduct->quantity, $vendorAvailableProducts[$invoiceProduct->products_id]);
                        $vendorProduct->quantity = $orderingCount;
                        $vendorInvoiceProducts->push($vendorProduct);
                        $invoiceProduct->quantity -= $orderingCount;
                        // forget product from original collection if all needing count of products are reserved
                        if ($invoiceProduct->quantity === 0) {
                            $keyedProducts->forget($invoiceProduct->products_id);
                        }
                    }
                }
                // add vendors products to sorted result
                if ($vendorInvoiceProducts->count()) {
                    $sortedProducts[InvoiceTypes::PRE_ORDER] = $vendorInvoiceProducts;
                }
            }
        }

        // add unavailable products to sorted result
        if ($keyedProducts->count()) {
            $sortedProducts['unavailable'] = $keyedProducts;
        }

        return $sortedProducts;
    }

    /**
     * Get all storages used to collect invoice.
     *
     * @param Collection $products
     * @return array
     */
    public function getInvoiceStorages(Collection $products): array
    {
        return array_keys($this->sortProductByStorages($products));
    }

    /**
     * Get all vendors used to collect invoice.
     *
     * @param Collection $products
     * @return array
     */
    public function getInvoiceVendors(Collection $products): array
    {
        return array_keys($this->sortProductByVendors($products));
    }

    /**
     * Sort ordering product by storages.
     *
     * @param Collection $products
     * @param int|null $deliveryCity
     * @return array
     */
    public function sortProductByStorages(Collection $products, int $deliveryCity = null): array
    {
        // define storages that have all needing products
        $productsQuantityById = $products->pluck('quantity', 'products_id')->toArray();
        $storagesHaveAllProducts = $this->storageProductRepository->getStoragesHaveAllProducts($productsQuantityById);

        // collect products from one storage
        if (!empty($storagesHaveAllProducts)) {
            // define collecting storage
            $collectingOrderStorageId = $this->productRouter->defineCollectingOrderStorage($storagesHaveAllProducts, $deliveryCity);
            // return storage id as key and all products as value
            return [$collectingOrderStorageId => $products];
        }

        // collect products from multiply storages
        $storageInvoices = [];
        $keyedProducts = $products->keyBy('products_id');

        // iterate each needing product
        foreach ($keyedProducts as $productsId => $invoiceProduct) {
            // set needing quantity
            $needingQuantity = $invoiceProduct->quantity;
            // get available product count keyed by storage id
            $availableProductQuantityByStorageId = $this->storageProductRepository->getProductsCountKeyedByStorageId($productsId);

            // iterate storages that have product
            foreach ($availableProductQuantityByStorageId as $storageId => $storageProductQuantity) {
                // define ordering count of product on current storage
                $orderingCount = min($needingQuantity, $storageProductQuantity);

                // clone invoice product
                $storageProduct = clone $invoiceProduct;
                // set ordering quantity
                $storageProduct->quantity = $orderingCount;
                // collect invoice product in current storage collection
                if (!isset($storageInvoices[$storageId])) {
                    $storageInvoices[$storageId] = collect();
                }
                $storageInvoices[$storageId]->push($storageProduct);

                // decrease needing quantity
                $needingQuantity -= $orderingCount;

                // all count of products delivered
                if ($needingQuantity === 0) {
                    $keyedProducts->forget($invoiceProduct->products_id);
                    break;
                }
            }
        }

        // return array of storages id as keys with  collection of invoice product that will be ordered from this storage as value
        return $storageInvoices;
    }

    /**
     * Sort ordering products by vendors.
     *
     * @param Collection $products
     * @return array
     */
    public function sortProductByVendors(Collection $products): array
    {
        $vendorInvoices = [];

        $keyedProducts = $products->keyBy('products_id');

        // iterate each needing product
        foreach ($keyedProducts as $productsId => $invoiceProduct) {
            // set needing quantity
            $needingQuantity = $invoiceProduct->quantity;
            // get available product count keyed by vendor id sorted by price ascending
            $availableProductQuantityByVendorId = $this->vendorProductRepository->getProductsCountKeyedByVendorId($productsId);

            // iterate vendors that have product
            foreach ($availableProductQuantityByVendorId as $vendorId => $vendorProductQuantity) {
                // define ordering count of product on current vendor
                $orderingCount = min($needingQuantity, $vendorProductQuantity);

                // clone invoice product
                $vendorProduct = clone $invoiceProduct;
                // set ordering quantity
                $vendorProduct->quantity = $orderingCount;
                // collect invoice product in current vendor collection
                if (!isset($vendorInvoices[$vendorId])) {
                    $vendorInvoices[$vendorId] = collect();
                }
                $vendorInvoices[$vendorId]->push($vendorProduct);

                // decrease needing quantity
                $needingQuantity -= $orderingCount;

                // all count of products delivered
                if ($needingQuantity === 0) {
                    $keyedProducts->forget($invoiceProduct->products_id);
                    break;
                }
            }
        }

        return $vendorInvoices;
    }
}