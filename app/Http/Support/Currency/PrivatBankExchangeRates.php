<?php
/**
 * Get rates from Privat Bank.
 */

namespace App\Http\Support\Currency;


use App\Contracts\Currency\CurrenciesInterface;
use App\Contracts\Currency\ExchangeRatesInterface;
use ReflectionClass;
use SimpleXMLElement;

class PrivatBankExchangeRates implements ExchangeRatesInterface
{
    const RATE_SOURCE_URL = 'https://api.privatbank.ua/p24api/pubinfo?exchange&coursid=5';

    const CURL_TIMEOUT = 500;

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
        $curl = curl_init(self::RATE_SOURCE_URL);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT_MS, self::CURL_TIMEOUT);

        $response = curl_exec($curl);

        curl_close($curl);

        $this->rates = $response ? new SimpleXMLElement($response) : null;
    }

    /**
     * Get rate of given sources.
     *
     * @param int $currencyId
     * @return float|null
     */
    public function getRate(int $currencyId)
    {
        if (!$this->rates instanceof SimpleXMLElement) {
            return null;
        }

        $rateElements = $this->rates->xpath('/exchangerates/row/exchangerate[@ccy="' . $this->getCurrencyCode($currencyId) . '"]');

        return isset($rateElements[0]['sale']) ? (float)$rateElements[0]['sale'] : null;
    }

    /**
     * Get currency code by id.
     *
     * @param int $currencyId
     * @return mixed
     */
    private function getCurrencyCode(int $currencyId)
    {
        $class = new ReflectionClass(CurrenciesInterface::class);
        $constants = array_flip($class->getConstants());

        return $constants[$currencyId];
    }
}