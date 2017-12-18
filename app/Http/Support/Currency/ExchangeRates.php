<?php
/**
 * Get given currency rate from primary source or from auxiliary sources if primary is shut down.
 */

namespace App\Http\Support\Currency;


use App\Contracts\Currency\ExchangeRatesInterface;
use App\Contracts\Currency\ExchangeRateSourcesInterface;
use App\Models\Currency;
use App\Models\CurrencyRate;
use Carbon\Carbon;
use Exception;
use ReflectionClass;

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
    public function getRate(string $currency)
    {
        $storedCurrencyRate = $this->getStoredRate($currency);

        if ($storedCurrencyRate){
            return $storedCurrencyRate;
        }

        $primarySource = $this->getPrimaryRateSource();

        $rate = $primarySource->getRate($currency);

        if (!$rate) {
            $rate = $this->getRateFromAuxiliarySources($currency);
        }

        $this->storeReceivedRate($currency, $rate);

        return $rate;
    }

    private function getStoredRate(string $currency)
    {
        $storedCurrencyRate = $storedCurrencyRate = $this->currencyRate->whereHas('currency', function($query) use($currency){
            $query->where('code', $currency);
        })->where('created_at', '>=', Carbon::today())->first();

        return $storedCurrencyRate && $storedCurrencyRate->created_at->addHours(config('shop.exchange_rate_ttl')) >= Carbon::now() ? $storedCurrencyRate->rate : null;
    }

    private function storeReceivedRate(string $currency, float $rate)
    {
        $this->currencyRate->create([
            'currencies_id' => $this->currency->where('code', $currency)->first()->id,
            'rate' => $rate,
        ]);
    }

    private function getPrimaryRateSource(): ExchangeRatesInterface
    {
        $primarySourceClassName = __NAMESPACE__ . '\\' . config('shop.primary_exchange_rate_source') . 'ExchangeRates';

        if (!class_exists($primarySourceClassName)) {
            throw new Exception('Primary currency exchange rate definer class is not exists!');
        }

        return new $primarySourceClassName();
    }

    private function getRateFromAuxiliarySources(string $currency)
    {
        foreach ((new ReflectionClass(ExchangeRateSourcesInterface::class))->getConstants() as $source) {
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