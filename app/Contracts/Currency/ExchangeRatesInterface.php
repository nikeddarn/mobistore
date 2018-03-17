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
}