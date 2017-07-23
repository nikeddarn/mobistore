<?php

namespace App\Http\Controllers\Localization;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\View\FileViewFinder;

class LanguageController extends Controller
{
    public function changeLanguage(Request $request, string $language){
        $locale = substr($language, 0, 2);
        if (in_array($locale, config('app.available_locales'))){
            $this->setFileFinder($locale);
            $request->session()->put('locale', $locale);
        }
        return redirect()->back()->withCookie(cookie()->forever('locale', $locale));
    }

    /**
     * Create FileViewFinder with path to the defined local view.
     * Set the finder to the view.
     *
     * @param string $locale
     */
    private function setFileFinder(string $locale){
        $viewPath = resource_path() . '/views/';
        $localViewPath = $viewPath . $locale;

        $finder = new FileViewFinder(app()['files'], [$localViewPath, $viewPath]);

        View::setFinder($finder);
    }
}
