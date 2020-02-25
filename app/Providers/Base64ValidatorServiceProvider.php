<?php

namespace App\Providers;

use App;
use Illuminate\Support\ServiceProvider;

class Base64ValidatorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        App::bind('Base64Validator', function()
        {
            return new App\Services\Base64Validator();
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
