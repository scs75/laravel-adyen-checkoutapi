<?php

namespace Pixwell\LaravelAdyenCheckoutApi;

use Pixwell\LaravelAdyenCheckoutApi\Exceptions\AdyenBaseUrlException;
use Pixwell\LaravelAdyenCheckoutApi\Exceptions\AdyenResponseException;
use Zttp\Zttp;

class AdyenCheckoutApi
{

    const API_VERSION = 'v30';

    public $url;

    public $apiKey;


    /**
     * Create a new Skeleton Instance.
     *
     * @param string $apiKey
     * @param string $baseUrl
     *
     * @throws AdyenBaseUrlException
     */
    public function __construct(string $apiKey, string $baseUrl)
    {
        $this->url = $this->getUrl($baseUrl);
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
     * @param string $baseUrl
     *
     * @return string
     * @throws AdyenBaseUrlException
     */
    private function getUrl(string $baseUrl): string
    {
        if (preg_match('/checkout-live.adyenpayments.com/', $baseUrl)) { // LIVE
            return $baseUrl . '/checkout/services/PaymentSetupAndVerification/' . self::API_VERSION;
        }

        if (preg_match('/checkout-test.adyen.com/', $baseUrl)) { // TEST
            return $baseUrl . '/services/PaymentSetupAndVerification/' . self::API_VERSION;
        }

        throw new AdyenBaseUrlException();
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
