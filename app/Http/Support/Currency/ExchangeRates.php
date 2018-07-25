<?php
/**
 * Define given currency rate from primary source or from auxiliary sources if primary is shut down.
 * Store it in CurrencyRate model.
 */

namespace App\Http\Support\Currency;


use App\Contracts\Currency\CurrenciesInterface;
use App\Contracts\Currency\ExchangeRatesInterface;
use App\Models\Currency;
use App\Models\CurrencyRate;
use Carbon\Carbon;
use Exception;

class ExchangeRates implements ExchangeRatesInterface
{
    /**
     * @var Currency
     */
    private $currency;

    /**
     * @var CurrencyRate
     */
    private $currencyRate;

    /**
     * ExchangeRates constructor.
     *
     * @param Currency $currency
     * @param CurrencyRate $currencyRate
     */
    public function __construct(Currency $currency, CurrencyRate $currencyRate)
    {

        $this->currency = $currency;
        $this->currencyRate = $currencyRate;
    }

    /**
     * Get average rate of given sources.
     *
     * @param int $currencyId
     * @return float|null
     */
    public function getRate(int $currencyId = CurrenciesInterface::USD)
    {
        $currencyRateModel = $this->retrieveActualCurrencyRateModel($currencyId);

        if ($currencyRateModel){
            return $currencyRateModel->rate;
        }

        $rateFromSource = $this->getRateFromDefinedSources($currencyId);

        if ($rateFromSource){
            $this->createCurrencyRateModel($currencyId, $rateFromSource);

            return $rateFromSource;
        }

        $currencyRateModel = $this->retrieveLastStoredCurrencyRate($currencyId);

        if ($currencyRateModel){
            return $currencyRateModel->rate;
        }

        return null;
    }

    /**
     * Retrieve actual model from DB.
     *
     * @param int $currencyId
     * @return CurrencyRate|null
     */
    private function retrieveActualCurrencyRateModel(int $currencyId)
    {
        return $this->currencyRate
            ->where('currencies_id', $currencyId)
            ->where('created_at', '>=', Carbon::today())
            ->where('created_at', '>=', Carbon::now()->subHour(config('shop.exchange_rate.update_rate_hours')))
            ->orderByDesc('created_at')
            ->first();
    }

    /**
     * Retrieve last stored model from DB that is valid.
     *
     * @param int $currencyId
     * @return CurrencyRate|null
     */
    private function retrieveLastStoredCurrencyRate(int $currencyId)
    {
        return $this->currencyRate
            ->where('currencies_id', $currencyId)
            ->where('created_at', '>=', Carbon::now()->subDays(config('shop.exchange_rate.valid_stored_rate_days')))
            ->orderByDesc('created_at')
            ->first();
    }

    /**
     * Create CurrencyRate model.
     *
     * @param int $currencyId
     * @param float $rate
     */
    private function createCurrencyRateModel(int $currencyId, float $rate)
    {
        $this->currencyRate->create([
            'currencies_id' => $currencyId,
            'rate' => $rate,
        ]);
    }

    /**
     * Define rate from external sources.
     *
     * @param int $currency
     * @return float
     */
    private function getRateFromDefinedSources(int $currency): float
    {
        foreach (config('shop.exchange_rate.sources') as $source) {
            if (class_exists($source)) {
                try {
                    $rate = (new $source)->getRate($currency);
                    if ($rate) {
                        return $rate;
                    }
                } catch (Exception $exception) {
                    break;
                }
            }
        }
        return null;
    }
}