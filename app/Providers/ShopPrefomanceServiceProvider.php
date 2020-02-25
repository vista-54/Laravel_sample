<?php

namespace App\Providers;

use App;
use Illuminate\Support\ServiceProvider;

class ShopPrefomanceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        App::bind('ShopPerformance', function()
        {
            return new App\Services\ShopPerformance;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
