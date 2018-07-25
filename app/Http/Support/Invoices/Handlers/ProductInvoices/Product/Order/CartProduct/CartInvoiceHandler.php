<?php
/**
 * Additional methods for handle cart data.
 */

namespace App\Http\Support\Invoices\Handlers\ProductInvoices\Product\Order\CartProduct;


use App\Http\Support\Currency\ExchangeRates;
use App\Http\Support\Invoices\Handlers\ProductInvoices\Product\Order\OrderProductManager;
use App\Http\Support\Price\ProductPrice;
use App\Contracts\Currency\CurrenciesInterface;
use App\Models\InvoiceProduct;
use Carbon\Carbon;
use Illuminate\Database\DatabaseManager;

final class CartInvoiceHandler extends OrderProductManager
{
    /**
     * @var ProductPrice
     */
    private $productPrice;
    /**
     * @var ExchangeRates
     */
    private $exchangeRates;

    /**
     * CartInvoiceHandler constructor.
     * @param DatabaseManager $databaseManager
     * @param ProductPrice $productPrice
     * @param ExchangeRates $exchangeRates
     */
    public function __construct(DatabaseManager $databaseManager, ProductPrice $productPrice, ExchangeRates $exchangeRates)
    {
        parent::__construct($databaseManager);
        $this->productPrice = $productPrice;
        $this->exchangeRates = $exchangeRates;
    }

    /**
     * Is user cart expired ?
     *
     * @param int $cartDaysToLive
     * @return bool
     */
    public function isUserCartExpired(int $cartDaysToLive): bool
    {
        return $this->invoice->updated_at < Carbon::now()->subDays($cartDaysToLive);
    }

    /**
     * Update prices for all products of cart.
     *
     * @return void
     */
    public function updateProductsPrices()
    {
        $user = $this->invoice->userCart->user;

        $this->invoice->invoiceProduct->each(function (InvoiceProduct $invoiceProduct) use ($user){
            $product = $invoiceProduct->product;

            $invoiceProduct->price = $this->productPrice->getUserPriceByProductModel($product, $user);
        });
    }

    /**
     * Update currency rate of invoice.
     *
     * @return bool
     */
    public function updateExchangeRate()
    {
        $rate = $this->exchangeRates->getRate(CurrenciesInterface::USD);

        if ($rate) {
            $this->invoice->rate = $rate;

            return $this->invoice->save();
        }

        return false;
    }
}