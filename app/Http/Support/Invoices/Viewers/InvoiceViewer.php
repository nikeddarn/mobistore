<?php
/**
 * Invoice viewer.
 */

namespace App\Http\Support\Invoices\Viewers;


use App\Contracts\Currency\CurrenciesInterface;
use App\Contracts\Shop\Invoices\Viewers\InvoiceViewerInterface;
use Carbon\Carbon;

abstract class InvoiceViewer implements InvoiceViewerInterface
{
    /**
     * @var string
     */
    const INVOICE_DATE_FORMAT = '%d %B %Y';

    /**
     * Format price in usd.
     *
     * @param float $price
     * @return string
     */
    public function formatUsdPrice(float $price):string
    {
        return number_format($price, 2);
    }

    /**
     * Format price in local cash.
     *
     * @param float $price
     * @param int $currencyId
     * @return string
     */
    public function formatLocalPrice(float $price, int $currencyId = CurrenciesInterface::UAH):string
    {
        switch ($currencyId){

            case CurrenciesInterface::UAH:
                $roundedPrice = ceil($price * 10) / 10;
                return number_format($roundedPrice, 2);

            default:
                return number_format($price, 2);
        }
    }

    /**
     * Format invoice date.
     *
     * @param Carbon $date
     * @return string
     */
    public function formatDate(Carbon $date)
    {
        return $date->formatLocalized(self::INVOICE_DATE_FORMAT);
    }
}