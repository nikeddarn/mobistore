<?php

namespace App\Providers;

use App\Http\ViewComposers\CommonComposer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if (!$this->app->request->ajax()) {
            // common header data composer
            View::composer('*', CommonComposer::class);

            // add user image to /user/* routes
            View::composer('content.user.*', function ($view){
                $view->with('userImage', Storage::disk('public')->url(auth('web')->user()->image));
            });
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
