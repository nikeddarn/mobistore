<?php

namespace App\Providers;

use App\Channels\Phone\Providers\AlphaSmsSender;
use App\Contracts\Channels\SmsChannelSenderInterface;
use Illuminate\Support\ServiceProvider;
use App\Contracts\Localization\LocaleDefinerInterface;
use App\Localization\LocaleDefiner;

class ChannelServiceProvider extends ServiceProvider
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
        // Sms channel sender
        $this->app->bind(SmsChannelSenderInterface::class, AlphaSmsSender::class);
    }
}
