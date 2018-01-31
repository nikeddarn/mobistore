<?php

/**
 * Currency rates interface.
 */

namespace App\Contracts\Currency;


interface ExchangeRateSourceInterface
{

    /**
     * Get average rate of given sources.
     *
     * @param string $currency
     * @return float
     */
    public function getRate(string $currency);
}