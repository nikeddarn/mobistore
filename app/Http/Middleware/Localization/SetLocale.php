<?php

namespace App\Http\Middleware\Localization;

use Closure;
use App\Contracts\Localization\LocaleDefinerInterface as LocaleDefiner;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\View;
use Illuminate\View\FileViewFinder;

class SetLocale
{
    /**
     * @var LocaleDefiner
     */
    private $definer;

    /**
     * SetLocale constructor.
     *
     * @param LocaleDefiner $definer
     */
    public function __construct(LocaleDefiner $definer)
    {

        $this->definer = $definer;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $locale = $this->definer->getLocale($request);

            App::setLocale($locale);

            $this->setFileFinder($locale);

            $request->session()->put('locale', $locale);

            $response = $next($request);

            return $response->withCookie(cookie()->forever('locale', $locale));
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

        if (!is_dir($localViewPath)){
            $localViewPath = $viewPath . config('app.fallback_locale');
        }

        $finder = new FileViewFinder(app()['files'], [$localViewPath, $viewPath]);

        View::setFinder($finder);
    }
}
