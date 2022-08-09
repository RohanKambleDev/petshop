<?php

namespace App\Providers;

use App\Services\Auth\Jwt;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        /**
         * binding Jwt to service container
         * which is also used as reference to 
         * create facade for JWT
         */
        app()->bind('Jwt', function () {
            return new Jwt();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
