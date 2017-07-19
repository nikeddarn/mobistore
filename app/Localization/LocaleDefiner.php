<?php
/**
 * Define and return array of preferred locales by cookie or 'Accept-Language' header.
 */

namespace App\Localization;

use App\Contracts\Localization\LocaleDefinerInterface;
use Illuminate\Http\Request;

class LocaleDefiner implements LocaleDefinerInterface
{

    /**
     * Return defined locale as two character string
     *
     * @param Request $request
     * @return string
     */
    public function getLocale(Request $request)
    {
        $preferredLocales = $this->defineLocales($request);

        $intersectedLocales = array_intersect($preferredLocales, config('app.available_locales'));

        if (!empty($intersectedLocales)) {
            do {
                $locale = array_shift($intersectedLocales);
            } while (!$locale);
            return $locale;
        } else {
            return config('app.fallback_locale');
        }
    }


    /**
     * Define locales by header and add at start of array stored locale if it's exist.
     *
     * @param Request $request
     * @return array
     */
    private function defineLocales(Request $request)
    {
        $locales = $this->defineLocalesByHeader($request);
        $storedLocale = $this->getStoredLocale($request);
        if ($storedLocale) {
            array_unshift($locales, $storedLocale);
        }

        return array_unique($locales);
    }

    /**
     * @param Request $request
     * @return string|null
     */
    private function getStoredLocale(Request $request)
    {
        if ($request->session()->has('locale')) {
            $locale = $request->session()->get('locale');
        } elseif ($request->cookie('locale')) {
            $locale = $request->cookie('locale');
        } else {
            $locale = null;
        }

        return $locale ? substr($locale, 0, 2) : null;
    }


    /**
     * Define locales by parsing 'Accept-Language' HTTP-header.
     *
     * @param Request $request
     * @return array|bool
     */
    private function defineLocalesByHeader(Request $request)
    {
        $locales = [];

        $header = $this->getHeader($request);

        if (is_string($header) && preg_match_all('/([a-z]{2}(?:-[A-Z]{2}){0,1})(?:;q=(0.[1-9])){0,1}/', $header, $matches, PREG_PATTERN_ORDER)) {
            $locales = array_combine($matches[1], $matches[2]);
            array_walk($locales, function (&$rate) {
                if (!$rate) {
                    $rate = 1;
                }
            });
            arsort($locales, SORT_NUMERIC);
            $locales = array_keys($locales);
            array_walk($locales, function (&$locale) {
                $locale = strtolower(substr($locale, 0, 2));
            });
            $locales = array_unique($locales);
        }

        return $locales;
    }

    /**
     *
     * @param Request $request
     * @return string Filtered and cutted 'Accept-Language' HTTP-header
     */
    private function getHeader(Request $request)
    {
        $acceptLanguageHeader = $request->header('Accept-Language');

        if ($acceptLanguageHeader) {
            return filter_var(substr($acceptLanguageHeader, 0, 64), FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
        } else {
            return null;
        }

    }
}