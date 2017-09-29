<?php

namespace Pixwell\LaravelAdyenCheckoutApi;

use Pixwell\LaravelAdyenCheckoutApi\Exceptions\AdyenEnvironmentException;
use Pixwell\LaravelAdyenCheckoutApi\Exceptions\AdyenResponseException;
use Zttp\Zttp;

class AdyenCheckoutApi
{

    public $url;

    const SANDBOX_URL = 'https://checkout-test.adyen.com/services/PaymentSetupAndVerification/';
    const PRODUCTION_URL = 'https://:random-:companyName-checkout-live.adyenpayments.com/checkout/services/PaymentSetupAndVerification/';
    const API_VERSION = 'v30';

    public $apiKey;


    /**
     * Create a new Skeleton Instance.
     *
     * @param string $environment
     * @param string $apiKey
     * @param string $random
     * @param string $companyName
     *
     * @throws AdyenEnvironmentException
     */
    public function __construct(string $environment, string $apiKey, string $random = '', string $companyName = '')
    {
        if ($environment === 'live') {
            $this->url = self::PRODUCTION_URL;
            $this->url = str_replace(':random', $random, $this->url);
            $this->url = str_replace(':companyName', $companyName, $this->url);
        } elseif ($environment === 'test') {
            $this->url = self::SANDBOX_URL;
        } else {
            throw new AdyenEnvironmentException();
        }
        $this->url .= self::API_VERSION;
        $this->apiKey = $apiKey;
    }


    public function setup(SetupRequest $setupRequest)
    {
        $payload = $setupRequest->toArray();

        return $this->makeRequest($this->url . '/setup', $payload);
    }


    public function verify($payload)
    {
        $data = [
            'payload' => $payload,
        ];

        return $this->makeRequest($this->url . '/verify', $data);
    }


    /**
     * @param string $url
     * @param array  $body
     *
     * @return mixed
     * @throws AdyenResponseException
     */
    private function makeRequest(string $url, array $body = [])
    {
        $headers = [
            'Content-Type' => 'application/json',
            'x-api-key' => $this->apiKey,
            'Content-Length' => strlen(json_encode($body)),
        ];

        $response = Zttp::asJson()->withHeaders($headers)->post($url, $body);

        if ((int)$response->response->getStatusCode() !== 200) {
            $response = json_decode($response->response->getBody());
            throw new AdyenResponseException($response->message, $response->status);
        }

        return $response->json();
    }
}
