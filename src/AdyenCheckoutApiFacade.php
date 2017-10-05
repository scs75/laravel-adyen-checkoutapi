<?php

namespace Pixwell\LaravelAdyenCheckoutApi;

use Illuminate\Support\Facades\Facade;

class AdyenCheckoutApiFacade extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return AdyenCheckoutApi::class;
    }
}
