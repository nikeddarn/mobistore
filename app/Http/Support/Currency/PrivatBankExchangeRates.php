<?php
/**
 * Get rates from Privat Bank.
 */

namespace App\Http\Support\Currency;


use App\Contracts\Currency\ExchangeRatesInterface;
use SimpleXMLElement;

class PrivatBankExchangeRates implements ExchangeRatesInterface
{

    private $url = 'https://api.privatbank.ua/p24api/pubinfo?exchange&coursid=5';

    private $rates;

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
     * Get average rate of given sources.
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