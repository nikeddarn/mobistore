<?php

namespace App\Http\Controllers\Checkout;

use App\Http\Support\FormatInvoiceProducts;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ShowCheckoutController extends CheckoutController
{
    use FormatInvoiceProducts;

    /**
     * Create order invoices if cart isn't empty.
     *
     * @return View
     * @throws \Exception
     */
    public function show()
    {
        $this->cartHandler = $this->getCartHandler();

        // products in cart doesn't exist. show view with 'no products' message
        if (!$this->cartHandler) {
            return view('content.checkout.index')->with($this->commonMetaData());
        }

        // keep cart referrer link
        $this->request->session()->keep(self::CART_REFERRER_SESSION_NAME);

        // login or register user if user is unauthenticated
        if (!auth('web')->check()) {
            return redirect()->guest(route('login'));
        }

        return view('content.checkout.index')
            ->with($this->commonMetaData())
            ->with($this->deliveryData())
            ->with($this->invoicesData());
    }

    /**
     * @return array
     * @throws \Exception
     */
    private function invoicesData()
    {
        $invoicesData = [];

        $orderProducts = $this->sortProductsByInvoices();

        $exchangeRate = $this->exchangeRates->getRate();
        $productImagePathPrefix = Storage::disk('public')->url('images/products/small/');
        $calculatedDeliveryPrice = $this->deliveryPrice->getDeliveryPrice(auth('web')->user(), $this->cartHandler->getInvoiceSum());

        // online order invoice data
        if ($orderProducts[self::ORDER]->count()) {
            $deliveryDay = $this->localShipmentDispatcher->getPossibleNearestShipmentArrival()->toDateString();

            $invoicesData['order'] = $this->getOrderInvoiceData($productImagePathPrefix, $exchangeRate, $orderProducts[self::ORDER], $calculatedDeliveryPrice, $deliveryDay);
        }

        // pre order invoice data
        if ($orderProducts[self::PRE_ORDER]->count()) {

            // set free delivery for pre order if online order is present
            if ($orderProducts[self::ORDER]->count()) {
                $calculatedDeliveryPrice = 0;
            }
            $deliveryDay = $this->vendorShipmentDispatcher->getPossibleNearestShipmentArrival()->toDateString();

            $invoicesData['pre_order'] = $this->getOrderInvoiceData($productImagePathPrefix, $exchangeRate, $orderProducts[self::PRE_ORDER], $calculatedDeliveryPrice, $deliveryDay);
        }

        return ['productsData' => $invoicesData];
    }

    /**
     * Create order invoice data.
     *
     * @param string $productImagePathPrefix
     * @param float $exchangeRate
     * @param Collection $orderProducts
     * @param float $deliveryPrice
     * @param string $deliveryDay
     * @return array
     */
    private function getOrderInvoiceData(string $productImagePathPrefix, float $exchangeRate, Collection $orderProducts, float $deliveryPrice, string $deliveryDay): array
    {
        $uahCurrencySymbol = trans('shop.currency.uah');
        $usdCurrencySymbol = trans('shop.currency.usd');

        $invoiceData = [];

        $invoiceData['products'] = $this->getFormattedInvoiceProductsData($orderProducts, $productImagePathPrefix);

        $orderInvoiceSum = $this->cartHandler->calculateProductsSum($orderProducts->pluck('products_id')->toArray());
        $invoiceData['invoice_sum'] = $usdCurrencySymbol . number_format($orderInvoiceSum, 2, '.', ',');
        $invoiceData['invoice_uah_sum'] = number_format(ceil($orderInvoiceSum * $exchangeRate), 2, '.', ',') . '&nbsp;' . $uahCurrencySymbol;

        $invoiceData['delivery_uah_sum'] = number_format(ceil($deliveryPrice * $exchangeRate), 2, '.', ',') . '&nbsp;' . $uahCurrencySymbol;
        $invoiceData['post_delivery-message'] = trans('shop.delivery.price.post');

        $invoiceData['total_uah_sum'] = number_format(ceil(($orderInvoiceSum + $deliveryPrice) * $exchangeRate), 2, '.', ',') . '&nbsp;' . $uahCurrencySymbol;

        $invoiceData['delivery_time'] = $deliveryDay;

        return $invoiceData;
    }

    /**
     * Get delivery form data.
     *
     * @return array
     */
    private function deliveryData()
    {
        $deliveryData = [];

        $user = auth('web')->user();

        // last delivery data
        $lastUserInvoiceWithDelivery = $this->userInvoice->where('users_id', $user->id)->orderByDesc('created_at')->has('userDelivery')->with('userDelivery')->first();

        if ($lastUserInvoiceWithDelivery) {
            $lastUserDelivery = $lastUserInvoiceWithDelivery->userDelivery;
            $deliveryData['name'] = $lastUserDelivery->name;
            $deliveryData['phone'] = $lastUserDelivery->phone;
            $deliveryData['address'] = $lastUserDelivery->address;
        }else{
            $deliveryData['name'] = $user->name;
            $deliveryData['phone'] = $user->phone;
        }

        // delivery types list
        $deliveryData['types'] = $this->deliveryType->all()->pluck('title', 'id')->toArray();

        // post services list
        $deliveryData['posts'] = $this->postService->all()->pluck('title', 'id')->toArray();

        return [
            'deliveryData' => $deliveryData,
        ];
    }
}
