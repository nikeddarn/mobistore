<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\Localization\LocaleDefinerInterface;
use App\Localization\LocaleDefiner;

class LocalizationServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // Locale definer
        $this->app->bind(LocaleDefinerInterface::class, LocaleDefiner::class);
    }
}
