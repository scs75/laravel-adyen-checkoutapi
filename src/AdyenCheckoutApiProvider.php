<?php

namespace Pixwell\LaravelAdyenCheckoutApi;

use Illuminate\Support\ServiceProvider;

class AdyenCheckoutApiProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     * @throws \Pixwell\LaravelAdyenCheckoutApi\Exceptions\AdyenEnvironmentException
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            if (file_exists(config_path('adyen.php'))) {
                $this->mergeConfigFrom(__DIR__ . '/../config/adyen.php', 'adyen');
            } else {
                $this->publishes([
                    __DIR__ . '/../config/adyen.php' => config_path('adyen.php'),
                ], 'config');
            }

            $this->app->bind(AdyenCheckoutApi::class, function () {
                $config = $this->app['config']['adyen'];
                $service = new AdyenCheckoutApi(
                    $config['environment'],
                    $config['apiKey'],
                    $config['random'],
                    $config['companyName']
                );

                return $service;
            });
        }
    }


    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/adyen.php', 'adyen');
    }
}
