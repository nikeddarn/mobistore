<?php
/**
 * Define and return array of preferred locales by cookie or 'Accept-Language' header.
 */

namespace App\Contracts\Localization;

use Illuminate\Http\Request;

interface LocaleDefinerInterface
{

    /**
     * Return defined locale as two character string
     *
     * @param Request $request
     * @return string
     */
    public function getLocale(Request $request);
}