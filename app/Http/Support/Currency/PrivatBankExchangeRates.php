<?php
/**
 * Get rates from Privat Bank.
 */

namespace App\Http\Support\Currency;


use App\Contracts\Currency\ExchangeRateSourceInterface;
use SimpleXMLElement;

class PrivatBankExchangeRates implements ExchangeRateSourceInterface
{
    /**
     * Url of XML with courses.
     *
     * @var string
     */
    private $url = 'https://api.privatbank.ua/p24api/pubinfo?exchange&coursid=5';

    /**
     * SimpleXMLElement with courses.
     *
     * @var null|SimpleXMLElement
     */
    private $rates;

    /**
     * FinanceExchangeRates constructor.
     * Get courses by Curl.
     */
    public function __construct()
    {
        $curl = curl_init($this->url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT_MS, config('shop.exchange_rate_source_timeout'));

        $response = curl_exec($curl);

        curl_close($curl);

        $this->rates = $response ? new SimpleXMLElement($response) : null;
    }

    /**
     * Get rate of given sources.
     *
     * @param string $currency
     * @return float
     */
    public function getRate(string $currency)
    {
        if (!$this->rates instanceof SimpleXMLElement){
            return null;
        }

        $rateElements = $this->rates->xpath('/exchangerates/row/exchangerate[@ccy="' . $currency . '"]');

        return isset($rateElements[0]['sale']) ? (float)$rateElements[0]['sale'] : null;
    }
}