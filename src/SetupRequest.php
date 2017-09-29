<?php

namespace Pixwell\LaravelAdyenCheckoutApi;

class SetupRequest
{

    public $channel;

    public $token;

    public $amount = [];


    /**
     * SetupRequest constructor.
     *
     * @param $token
     * @param $channel
     */
    public function __construct($token, $channel)
    {
        $this->token = $token;
        $this->channel = $channel;
    }


    /**
     * @param mixed $channel
     *
     * @return SetupRequest
     */
    public function setChannel($channel): SetupRequest
    {
        $this->channel = $channel;

        return $this;
    }


    /**
     * @param mixed $token
     *
     * @return SetupRequest
     */
    public function setToken($token): SetupRequest
    {
        $this->token = $token;

        return $this;
    }


    /**
     * @param       $amount
     *
     * @param       $currency
     *
     * @return SetupRequest
     */
    public function setAmount($amount, $currency): SetupRequest
    {
        $this->amount = [
            'value' => $amount * 100,
            'currency' => $currency,
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
}
