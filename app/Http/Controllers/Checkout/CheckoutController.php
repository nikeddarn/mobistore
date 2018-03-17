<?php

namespace App\Http\Controllers\Checkout;

use App\Contracts\Shop\Invoices\Handlers\ProductInvoiceHandlerInterface;
use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Http\Support\Currency\ExchangeRates;
use App\Http\Support\Invoices\Fabrics\CartInvoiceFabric;
use App\Http\Support\Invoices\Fabrics\UserOutgoingOrderInvoiceFabric;
use App\Http\Support\Invoices\Fabrics\VendorIncomingOrderInvoiceFabric;
use App\Http\Support\Invoices\Handlers\ProductInvoiceHandler;
use App\Http\Support\Price\DeliveryPrice;
use App\Http\Support\Price\ProductPrice;
use App\Http\Support\ProductRepository\StorageProductRepository;
use App\Http\Support\ProductRepository\StorageProductRouter;
use App\Http\Support\ProductRepository\VendorProductRepository;
use App\Http\Support\ProductRepository\VendorProductRouter;
use App\Http\Support\Shipment\LocalShipmentDispatcher;
use App\Http\Support\Shipment\VendorShipmentDispatcher;
use App\Models\DeliveryType;
use App\Models\InvoiceProduct;
use App\Models\PostService;
use App\Models\PreOrderInvoice;
use App\Models\UserInvoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

abstract class CheckoutController extends Controller implements InvoiceTypes
{
    /**
     * @var string
     */
    const CART_COOKIE_NAME = 'cart';

    /**
     * @var string
     */
    const CART_REFERRER_SESSION_NAME = 'cart_referrer';

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var CartInvoiceFabric
     */
    protected $cartInvoiceFabric;

    /**
     * @var StorageProductRepository
     */
    private $storageProductRepository;

    /**
     * @var ExchangeRates
     */
    protected $exchangeRates;

    /**
     * @var UserOutgoingOrderInvoiceFabric
     */
    protected $userOutgoingOrderInvoiceFabric;

    /**
     * @var VendorProductRepository
     */
    private $vendorProductRepository;

    /**
     * @var StorageProductRouter
     */
    protected $storageProductRouter;

    /**
     * @var VendorProductRouter
     */
    protected $vendorProductRouter;

    /**
     * @var VendorIncomingOrderInvoiceFabric
     */
    protected $vendorIncomingOrderInvoiceFabric;

    /**
     * @var PreOrderInvoice
     */
    protected $preOrderInvoice;

    /**
     * @var DeliveryPrice
     */
    protected $deliveryPrice;

    /**
     * @var ProductInvoiceHandlerInterface
     */
    protected $cartHandler;

    /**
     * @var array
     */
    protected $ordersDeliveryPrices;

    /**
     * @var ProductPrice
     */
    protected $productPrice;

    /**
     * @var UserInvoice
     */
    protected $userInvoice;

    /**
     * @var DeliveryType
     */
    protected $deliveryType;

    /**
     * @var PostService
     */
    protected $postService;

    /**
     * @var LocalShipmentDispatcher
     */
    protected $localShipmentDispatcher;

    /**
     * @var VendorShipmentDispatcher
     */
    protected $vendorShipmentDispatcher;


    /**
     * CheckoutController constructor.
     *
     * @param Request $request
     * @param CartInvoiceFabric $cartInvoiceFabric
     * @param UserOutgoingOrderInvoiceFabric $userOutgoingOrderInvoiceFabric
     * @param VendorIncomingOrderInvoiceFabric $vendorIncomingOrderInvoiceFabric
     * @param StorageProductRepository $storageProductRepository
     * @param VendorProductRepository $vendorProductRepository
     * @param ExchangeRates $exchangeRates
     * @param PreOrderInvoice $preOrderInvoice
     * @param StorageProductRouter $storageProductRouter
     * @param VendorProductRouter $vendorProductRouter
     * @param DeliveryPrice $deliveryPrice
     * @param ProductPrice $productPrice
     * @param UserInvoice $userInvoice
     * @param DeliveryType $deliveryType
     * @param PostService $postService
     * @param LocalShipmentDispatcher $localShipmentDispatcher
     * @param VendorShipmentDispatcher $vendorShipmentDispatcher
     */
    public function __construct(
        Request $request,
        CartInvoiceFabric $cartInvoiceFabric,
        UserOutgoingOrderInvoiceFabric $userOutgoingOrderInvoiceFabric,
        VendorIncomingOrderInvoiceFabric $vendorIncomingOrderInvoiceFabric,
        StorageProductRepository $storageProductRepository,
        VendorProductRepository $vendorProductRepository,
        ExchangeRates $exchangeRates,
        PreOrderInvoice $preOrderInvoice,
        StorageProductRouter $storageProductRouter,
        VendorProductRouter $vendorProductRouter,
        DeliveryPrice $deliveryPrice,
        ProductPrice $productPrice,
        UserInvoice $userInvoice,
        DeliveryType $deliveryType,
        PostService $postService,
    LocalShipmentDispatcher $localShipmentDispatcher,
    VendorShipmentDispatcher $vendorShipmentDispatcher
    )
    {
        $this->request = $request;
        $this->cartInvoiceFabric = $cartInvoiceFabric;
        $this->vendorIncomingOrderInvoiceFabric = $vendorIncomingOrderInvoiceFabric;
        $this->userOutgoingOrderInvoiceFabric = $userOutgoingOrderInvoiceFabric;
        $this->storageProductRepository = $storageProductRepository;
        $this->vendorProductRepository = $vendorProductRepository;
        $this->storageProductRouter = $storageProductRouter;
        $this->vendorProductRouter = $vendorProductRouter;
        $this->deliveryPrice = $deliveryPrice;
        $this->productPrice = $productPrice;
        $this->preOrderInvoice = $preOrderInvoice;
        $this->userInvoice = $userInvoice;
        $this->exchangeRates = $exchangeRates;
        $this->deliveryType = $deliveryType;
        $this->postService = $postService;
        $this->localShipmentDispatcher = $localShipmentDispatcher;
        $this->vendorShipmentDispatcher = $vendorShipmentDispatcher;
    }

    /**
     * Get cart handler with bound updated cart.
     *
     * @return ProductInvoiceHandler|null
     */
    protected function getCartHandler()
    {
        $cartRepository = $this->cartInvoiceFabric->getRepository();
        $cartHandler = $this->cartInvoiceFabric->getHandler();

        // products in cart doesn't exist. show view with 'no products' message
        if ($cartRepository->cartExists() && $cartHandler->bindInvoice($cartRepository->getRetrievedInvoice())->getProductsCount()) {

            // update cart prices
            if (config('shop.recalculate_cart_prices') && $cartHandler->getUpdateTime() < Carbon::today()->subDays(1)) {
                $cartHandler->updateProductsPrices();
            }

            return $cartHandler;
        } else {
            return null;
        }
    }

    /**
     * Sort collection of product invoices by order and pre order.
     *
     * @return array
     */
    protected function sortProductsByInvoices():array
    {
        $cartProductsId = $this->cartHandler->getInvoiceProducts()->keyBy('products_id')->pluck('products_id')->toArray();

        $storageAvailableProducts = $this->storageProductRepository->getAvailableProductsCountById($cartProductsId);
        $vendorAvailableProducts = $this->vendorProductRepository->getAvailableProductsCountById($cartProductsId);

        $orderInvoiceProducts = collect();
        $preOrderInvoiceProducts = collect();

        $this->cartHandler->getInvoiceProducts()->each(function (InvoiceProduct $invoiceProduct) use ($storageAvailableProducts, $vendorAvailableProducts, $orderInvoiceProducts, $preOrderInvoiceProducts) {

            $orderedCount = 0;

            if (array_key_exists($invoiceProduct->products_id, $storageAvailableProducts)) {
                $orderInvoiceProduct = clone ($invoiceProduct);
                $orderedCount = min($invoiceProduct->quantity, $storageAvailableProducts[$invoiceProduct->products_id]);
                $orderInvoiceProduct->quantity = $orderedCount;
                $orderInvoiceProducts->push($orderInvoiceProduct);
            }

            if ($invoiceProduct->quantity > $orderedCount && array_key_exists($invoiceProduct->products_id, $vendorAvailableProducts)) {
                $vendorInvoiceProduct = clone ($invoiceProduct);
                $vendorInvoiceProduct->quantity = min(($invoiceProduct->quantity - $orderedCount), $vendorAvailableProducts[$invoiceProduct->products_id]);
                $preOrderInvoiceProducts->push($vendorInvoiceProduct);
            }
        });

        return [
            self::ORDER => $orderInvoiceProducts,
            self::PRE_ORDER => $preOrderInvoiceProducts,
        ];
    }

    /**
     * Create meta data for the view.
     *
     * @return array
     */
    protected function commonMetaData(): array
    {
        return [
            'commonMetaData' => [
                'title' => trans('meta.title.checkout_order'),
            ],
        ];
    }
}
