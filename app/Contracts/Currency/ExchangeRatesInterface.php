<?php

/**
 * Currency rates interface.
 */

namespace App\Contracts\Currency;


interface ExchangeRatesInterface extends ExchangeRateSourcesInterface, CurrenciesInterface
{

    /**
     * Get average rate of given sources.
     *
     * @param string $currency
     * @return float
     */
    public function getRate(string $currency): float;

    /**
     * Get id of actual \App\Models\CurrencyRate.
     *
     * @param string $currency
     * @return int
     */
    public function getCurrencyRateModelId(string $currency): int;
}