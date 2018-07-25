<?php

/**
 * Currency rates interface.
 */

namespace App\Contracts\Currency;


interface ExchangeRatesInterface
{

    /**
     * Get average rate of given sources.
     *
     * @param int $currencyId
     * @return float|null
     */
    public function getRate(int $currencyId);
}