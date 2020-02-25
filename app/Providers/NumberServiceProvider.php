<?php

namespace App\Providers;

use App;
use Illuminate\Support\ServiceProvider;

class NumberServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        App::bind('Number', function()
        {
            return new App\Services\Number;
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
