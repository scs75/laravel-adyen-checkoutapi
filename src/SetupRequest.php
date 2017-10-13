<?php

namespace Pixwell\LaravelAdyenCheckoutApi;

use Carbon\Carbon;

class SetupRequest
{

    public $channel;

    public $token;

    public $amount = [];

    public $returnUrl;

    public $shopperLocale;

    public $sessionValidity;

    public $countryCode;

    public $reference;

    public $shopperReference;

    public $merchantAccount;


    /**
     * SetupRequest constructor.
     *
     * @param $token
     * @param $channel
     */
    public function __construct($token, $channel)
    {
        $this->merchantAccount = config('adyen.merchantAccount');
        $this->token = $token;
        $this->channel = $channel;
        $this->setCountryCode(config('adyen.fallback.country'));
        $this->setShopperLocale(config('adyen.fallback.locale'));
        $this->setSessionValidity(Carbon::now()->addMinutes(10)->toIso8601String());
        $this->setReturnUrl(config('app.url'));
        $this->setReference(str_random(40));
    }


    /**
     * @param       $amount
     *
     * @param       $currency
     *
     * @return SetupRequest
     */
    public function setAmount($amount, $currency = null): SetupRequest
    {
        $this->amount = [
            'value' => $amount * 100,
            'currency' => $currency ?? config('adyen.currency'),
        ];

        return $this;
    }


    /**
     * @return array
     */
    public function toArray(): array
    {
        return (array)$this;
    }


    /**
     * @param mixed $shopperReference
     *
     * @return $this
     */
    public function setShopperReference($shopperReference)
    {
        $this->shopperReference = $shopperReference;

        return $this;
    }


    /**
     * @param string $sessionValidity
     *
     * @return $this
     */
    public function setSessionValidity(string $sessionValidity)
    {
        $this->sessionValidity = $sessionValidity;

        return $this;
    }


    /**
     * @param string $reference
     *
     * @return $this
     */
    public function setReference(string $reference)
    {
        $this->reference = $reference;

        return $this;
    }


    /**
     * @param mixed $countryCode
     *
     * @return $this
     */
    public function setCountryCode($countryCode)
    {
        if ($countryCode) {
            $this->countryCode = $countryCode;
        }

        return $this;
    }


    /**
     * @param mixed $shopperLocale
     *
     * @return $this
     */
    public function setShopperLocale($shopperLocale)
    {
        if ($shopperLocale) {
            $this->shopperLocale = $shopperLocale;
        }

        return $this;
    }


    /**
     * @param mixed $returnUrl
     *
     * @return $this
     */
    public function setReturnUrl($returnUrl)
    {
        $this->returnUrl = $returnUrl;

        return $this;
    }
}
