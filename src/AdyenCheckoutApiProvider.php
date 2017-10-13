<?php

namespace Pixwell\LaravelAdyenCheckoutApi;

use Illuminate\Support\ServiceProvider;

class AdyenCheckoutApiProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     * @throws \Pixwell\LaravelAdyenCheckoutApi\Exceptions\AdyenBaseUrlException
     */
    public function boot()
    {
        if (file_exists(config_path('adyen.php'))) {
            $this->mergeConfigFrom(__DIR__ . '/../config/adyen.php', 'adyen');
        } else {
            $this->publishes([
                __DIR__ . '/../config/adyen.php' => config_path('adyen.php'),
            ], 'config');
        }

        $this->app->bind(AdyenCheckoutApi::class, function () {
            $config = $this->app['config']['adyen'];

            return new AdyenCheckoutApi($config['apiKey'], $config['baseUrl']);
        });
    }


    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/adyen.php', 'adyen');
    }
}
