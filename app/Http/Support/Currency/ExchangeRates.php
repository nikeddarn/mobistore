<?php
/**
 * Define given currency rate from primary source or from auxiliary sources if primary is shut down.
 * Store it in CurrencyRate model.
 */

namespace App\Http\Support\Currency;


use App\Contracts\Currency\ExchangeRatesInterface;
use App\Models\Currency;
use App\Models\CurrencyRate;
use Carbon\Carbon;

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
     * @param string $currency
     * @return float
     */
    public function getRate(string $currency = self::USD):float
    {
        $currencyRateModel = $this->getCurrencyRateModel($currency);
        return $currencyRateModel ? $currencyRateModel->rate : null;
    }

    /**
     * Retrieve actual \App\Models\CurrencyRate if exists or define rate, and return stored CurrencyRate.
     *
     * @param string $currency
     * @return CurrencyRate
     */
    private function getCurrencyRateModel(string $currency):CurrencyRate
    {
        $currencyRate = $this->retrieveCurrencyRate($currency);

        if (!$currencyRate){
            $rate = $this->getRateFromDefinedSources($currency);
            if ($rate) {
                $currencyRate = $this->storeReceivedRate($currency, $rate);
            }else{
                $currencyRate = $this->retrieveLastStoredCurrencyRate($currency);
            }
        }

        return $currencyRate;
    }

    /**
     * Retrieve actual model from DB.
     *
     * @param string $currency
     * @return CurrencyRate|\Illuminate\Database\Eloquent\Builder
     */
    private function retrieveCurrencyRate(string $currency)
    {
       return $this->currencyRate->whereHas('currency', function($query) use($currency){
            $query->where('code', $currency);
        })
           ->where('created_at', '>=', Carbon::today())
           ->where('created_at', '>=', Carbon::now()->subHour(config('shop.exchange_rate_ttl')))
           ->orderByDesc('created_at')
           ->first();
    }

    /**
     * Retrieve last stored model from DB.
     *
     * @param string $currency
     * @return CurrencyRate|\Illuminate\Database\Eloquent\Builder
     */
    private function retrieveLastStoredCurrencyRate(string $currency)
    {
       return $this->currencyRate->whereHas('currency', function($query) use($currency){
            $query->where('code', $currency);
        })
           ->orderByDesc('created_at')
           ->first();
    }

    /**
     * Create and return new CurrencyRate model.
     *
     * @param string $currency
     * @param float $rate
     * @return CurrencyRate
     */
    private function storeReceivedRate(string $currency, float $rate):CurrencyRate
    {
        return $this->currencyRate->create([
            'currencies_id' => $this->currency->where('code', $currency)->first()->id,
            'rate' => $rate,
        ]);
    }

    /**
     * Define rate from external sources.
     *
     * @param string $currency
     * @return float
     */
    private function getRateFromDefinedSources(string $currency):float
    {
        foreach (config('shop.exchange_rate_sources') as $source) {
            $sourceClassName = __NAMESPACE__ . '\\' . $source . 'ExchangeRates';
            if (class_exists($sourceClassName)) {
                $rate = (new $sourceClassName)->getRate($currency);
                if ($rate) {
                    return $rate;
                }
            }
        }
        return null;
    }
}