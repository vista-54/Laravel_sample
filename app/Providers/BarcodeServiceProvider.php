<?php

namespace App\Providers;

use App;
use Illuminate\Support\ServiceProvider;

class BarcodeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        App::bind('Barcode', function()
        {
            return new App\Services\Barcode;
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
