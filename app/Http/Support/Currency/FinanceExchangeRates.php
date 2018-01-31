<?php
/**
 * Get rates from finance.ua.
 */

namespace App\Http\Support\Currency;


use App\Contracts\Currency\ExchangeRateSourceInterface;
use SimpleXMLElement;

class FinanceExchangeRates implements ExchangeRateSourceInterface
{
    /**
     * Url of XML with courses.
     *
     * @var string
     */
    private $url = 'http://resources.finance.ua/ru/public/currency-cash.xml';

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

        $rateElements = $this->rates->xpath('/source/organizations/organization/currencies/c[@id="' . $currency . '"]');

        $rateSourcesCount = count($rateElements);

        if (!$rateSourcesCount) {
            return null;
        }

        $sumRate = 0;

        foreach ($rateElements as $rateSource) {
            $sumRate += (float)$rateSource->attributes()->ar;
        }

        return $sumRate / $rateSourcesCount;
    }
}