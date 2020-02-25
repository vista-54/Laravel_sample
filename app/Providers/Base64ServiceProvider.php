<?php

namespace App\Providers;

use App;
use Illuminate\Support\ServiceProvider;

class Base64ServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        App::bind('Base64', function()
        {
            return new App\Services\Base64;
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
