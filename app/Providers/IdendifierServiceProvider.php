<?php

namespace App\Providers;

use App;
use Illuminate\Support\ServiceProvider;

class IdendifierServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        App::bind('Identifier', function()
        {
            return new App\Services\Identifier;
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
