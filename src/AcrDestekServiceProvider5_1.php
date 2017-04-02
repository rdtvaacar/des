<?php

namespace Acr\Destek;

use Illuminate\Support\ServiceProvider;
use Acr\Destek\Destek;

class AcrDestekServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/Views', 'acr_destek');
        require __DIR__ . '/Routes/routes.php';
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('acr-destek', function () {
            return new Destek();
        });
    }

}
