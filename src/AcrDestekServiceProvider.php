<?php

namespace Acr\Destek;

use Illuminate\Support\ServiceProvider;


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
        $this->publishes([
            __DIR__ . '/../config/destek_config.php.php' => config_path('destek_config.php'),
        ]);
        $this->loadRoutesFrom(__DIR__ . '/Routes/routes.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('acr-destek', function () {
            return new destek();
        });
        config([
            '/../config/destek_config.php',
        ]);
    }

}
