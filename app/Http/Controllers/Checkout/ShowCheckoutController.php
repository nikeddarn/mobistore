<?php

namespace App\Http\Controllers\Checkout;

use App\Contracts\Shop\Delivery\DeliveryTypesInterface;
use App\Contracts\Shop\Invoices\Handlers\ProductInvoiceHandlerInterface;
use App\Contracts\Shop\Invoices\InvoiceTypes;
use App\Http\Controllers\Controller;
use App\Http\Support\Checkout\UserDeliveryRepository;
use App\Http\Support\Checkout\UserInvoiceProductsSorter;
use App\Http\Support\Currency\ExchangeRates;
use App\Http\Support\Invoices\Fabrics\CartInvoiceFabric;
use App\Http\Support\Price\DeliveryPrice;
use App\Http\Support\Shipment\LocalShipmentDispatcher;
use App\Http\Support\Shipment\VendorShipmentDispatcher;
use Carbon\Carbon;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ShowCheckoutController extends Controller
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
     * @var UserInvoiceProductsSorter
     */
    private $productsSorter;

    /**
     * @var ExchangeRates
     */
    private $exchangeRates;

    /**
     * @var DeliveryPrice
     */
    private $deliveryPrice;

    /**
     * @var FilesystemManager
     */
    private $filesystemManager;

    /**
     * @var LocalShipmentDispatcher
     */
    private $localShipmentDispatcher;

    /**
     * @var VendorShipmentDispatcher
     */
    private $vendorShipmentDispatcher;

    /**
     * @var UserDeliveryRepository
     */
    private $deliveryRepository;

    /**
     * ShowCheckoutController constructor.
     * @param Request $request
     * @param CartInvoiceFabric $cartInvoiceFabric
     * @param UserInvoiceProductsSorter $productsSorter
     * @param DeliveryPrice $deliveryPrice
     * @param ExchangeRates $exchangeRates
     * @param FilesystemManager $filesystemManager
     * @param LocalShipmentDispatcher $localShipmentDispatcher
     * @param VendorShipmentDispatcher $vendorShipmentDispatcher
     * @param UserDeliveryRepository $deliveryRepository
     */
    public function __construct(Request $request, CartInvoiceFabric $cartInvoiceFabric, UserInvoiceProductsSorter $productsSorter, DeliveryPrice $deliveryPrice, ExchangeRates $exchangeRates, FilesystemManager $filesystemManager, LocalShipmentDispatcher $localShipmentDispatcher, VendorShipmentDispatcher $vendorShipmentDispatcher, UserDeliveryRepository $deliveryRepository)
    {
        $this->request = $request;
        $this->cartInvoiceFabric = $cartInvoiceFabric;
        $this->productsSorter = $productsSorter;
        $this->exchangeRates = $exchangeRates;
        $this->deliveryPrice = $deliveryPrice;
        $this->filesystemManager = $filesystemManager;
        $this->localShipmentDispatcher = $localShipmentDispatcher;
        $this->vendorShipmentDispatcher = $vendorShipmentDispatcher;
        $this->deliveryRepository = $deliveryRepository;
    }

    /**
     * Create order invoices if cart isn't empty.
     *
     * @return View
     * @throws \Exception
     */
    public function show()
    {
        $response = view('content.checkout.index')->with($this->commonMetaData());

        $cartRepository = $this->cartInvoiceFabric->getRepository();

        if ($cartRepository->cartExists()) {
            // keep cart referrer link
            $this->request->session()->keep(self::CART_REFERRER_SESSION_NAME);

            // login or register user if user is unauthenticated
            if (!auth('web')->check()) {
                return redirect()->guest(route('login'));
            }

            // get cart handler and bind retrieved cart to it
            $cartHandler = $this->cartInvoiceFabric->getHandler()->bindInvoice($cartRepository->getRetrievedInvoice());

            // cart is not empty
            if ($cartHandler->getProductsCount()) {
                // sort products by invoices types
                $sortedProducts = $this->productsSorter->sortProductsByOrderType($cartHandler->getInvoiceProducts());

                // some products is available to order
                if(isset($sortedProducts[InvoiceTypes::USER_ORDER]) || isset($sortedProducts[InvoiceTypes::USER_PRE_ORDER])) {
                    // add user invoices data
                    $response->with([
                        'productsData' => $this->invoicesData($cartHandler, $sortedProducts),
                        'deliveryData' => $this->deliveryData(),
                    ]);
                }
                // remove unavailable products from cart
                if (isset($sortedProducts['unavailable'])){
                    $this->removeUnavailableProducts($cartHandler, $sortedProducts['unavailable']);
                }
            }
        }

        return $response;
    }

    /**
     * Prepare orders data for the view.
     *
     * @param ProductInvoiceHandlerInterface $cartHandler
     * @param array $sortedProducts
     * @return array
     */
    private function invoicesData(ProductInvoiceHandlerInterface $cartHandler, array $sortedProducts)
    {
        $invoicesData = [];

        $exchangeRate = $this->exchangeRates->getRate();
        $productImagePathPrefix = $this->filesystemManager->disk('public')->url('images/products/small/');
        $courierDeliveryPrice = $this->deliveryPrice->calculateDeliveryPrice(auth('web')->user(), $cartHandler->getInvoiceSum(), DeliveryTypesInterface::COURIER);

        if (isset($sortedProducts[InvoiceTypes::USER_ORDER])){
            $invoiceStorages = $this->productsSorter->getInvoiceStorages($sortedProducts[InvoiceTypes::USER_ORDER]);

            $invoicesData['order'] = $this->getOrderInvoiceData($cartHandler, $sortedProducts[InvoiceTypes::USER_ORDER], $productImagePathPrefix, $exchangeRate, $courierDeliveryPrice, $this->localShipmentDispatcher->calculateDeliveryDay($invoiceStorages));
        }

        if (isset($sortedProducts[InvoiceTypes::USER_PRE_ORDER])){
            $invoiceVendors = $this->productsSorter->getInvoiceVendors($sortedProducts[InvoiceTypes::USER_PRE_ORDER]);

            $invoicesData['pre_order'] = $this->getOrderInvoiceData($cartHandler, $sortedProducts[InvoiceTypes::USER_PRE_ORDER], $productImagePathPrefix, $exchangeRate, isset($sortedProducts[InvoiceTypes::USER_ORDER]) ? 0 : $courierDeliveryPrice, $this->vendorShipmentDispatcher->calculateDeliveryDayByVendorShipmentsOrSchedules($invoiceVendors));
        }

        return $invoicesData;
    }

    /**
     * Create order invoice data.
     *
     * @param ProductInvoiceHandlerInterface $cartHandler
     * @param Collection $invoiceProducts
     * @param string $productImagePathPrefix
     * @param float $exchangeRate
     * @param float $deliveryPrice
     * @param Carbon $deliveryDay
     * @return array
     */
    private function getOrderInvoiceData(ProductInvoiceHandlerInterface $cartHandler, Collection $invoiceProducts, string $productImagePathPrefix, float $exchangeRate, float $deliveryPrice, Carbon $deliveryDay = null): array
    {
        $uahCurrencySymbol = trans('shop.currency.uah');
        $usdCurrencySymbol = trans('shop.currency.usd');
        $orderInvoiceSum = $cartHandler->calculateProductsSum($invoiceProducts->pluck('products_id')->toArray());

        return [
            'products' => $cartHandler->getFormattedProducts($invoiceProducts, $productImagePathPrefix),
            'invoice_sum' => $usdCurrencySymbol . $this->formatPrice($orderInvoiceSum),
            'invoice_uah_sum' => $this->formatPrice(ceil($orderInvoiceSum * $exchangeRate)) . '&nbsp;' . $uahCurrencySymbol,
            'delivery_uah_sum' => $this->formatPrice(ceil($deliveryPrice * $exchangeRate)),
            'total_uah_sum' =>   $this->formatPrice(ceil(($orderInvoiceSum + $deliveryPrice) * $exchangeRate)),
            'post_delivery-message' => trans('shop.delivery.price.post'),
            'delivery_time' => $deliveryDay ? $deliveryDay->format('d-m-Y') : trans('shop.delivery.time_undefined'),
        ];
    }

    /**
     * Format invoice prices.
     *
     * @param float $sum
     * @return string
     */
    private function formatPrice(float $sum)
    {
        return number_format($sum, 2, '.', ',');
    }

    /**
     * Get delivery form data.
     *
     * @return array
     */
    private function deliveryData()
    {
        $user = auth('web')->user();

        $lastUserDelivery = $this->deliveryRepository->getLastUserDelivery($user);

        return [
            // last delivery data
            'name' => $lastUserDelivery ? $lastUserDelivery->name : $user->name,
            'phone' => $lastUserDelivery ? $lastUserDelivery->phone : $user->phone,
            'address' => $lastUserDelivery ? $lastUserDelivery->address : null,
            // form data
            'types' => $this->deliveryRepository->getDeliveryTypes(),
            'posts' => $this->deliveryRepository->getPostServices(),
            'cities' => $this->deliveryRepository->getCitiesHaveStorage(),
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

    /**
     * Remove unavailable products from cart.
     *
     * @param ProductInvoiceHandlerInterface $cartHandler
     * @param Collection $unavailableProducts
     */
    private function removeUnavailableProducts(ProductInvoiceHandlerInterface $cartHandler, Collection $unavailableProducts)
    {
        foreach ($unavailableProducts as $product){
            $cartHandler->removeProduct($product->id, $product->quantity);
        }
    }
}
