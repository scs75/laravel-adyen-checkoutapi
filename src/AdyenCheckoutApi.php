<?php

namespace Pixwell\LaravelAdyenCheckoutApi;

use Illuminate\Support\Facades\Redis;
use Pixwell\LaravelAdyenCheckoutApi\Exceptions\AdyenBaseUrlException;
use Pixwell\LaravelAdyenCheckoutApi\Exceptions\AdyenResponseException;
use Pixwell\LaravelAdyenCheckoutApi\Exceptions\PriceMismatchException;
use Pixwell\LaravelAdyenCheckoutApi\Exceptions\RequiredAttributeException;
use Pixwell\LaravelAdyenCheckoutApi\Exceptions\VerificationException;
use Zttp\Zttp;

class AdyenCheckoutApi
{

    const API_VERSION = 'v30';

    public $url;

    public $apiKey;

    public $redisKey = 'adyen:setup.reference';


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

        if (! isset($payload['reference'], $payload['amount']['value'])) {
            throw new RequiredAttributeException();
        }
        Redis::set($this->getRedisKey($payload['reference']), $payload['amount']['value']);

        return $this->makeRequest($this->url . '/setup', $payload);
    }


    public function verify($payload, $price)
    {
        $response = $this->makeRequest($this->url . '/verify', ['payload' => $payload]);

        if (isset($response['errorMessage'])) {
            throw new AdyenResponseException($response['errorMessage'], 400);
        }
        
        if (isset($response['authResponse']) && $response['authResponse'] === 'Error') {
            throw new VerificationException();
        }

        $setupPrice = Redis::get($this->getRedisKey($response['merchantReference']));

        if (! $setupPrice && $setupPrice != ($price * 100)) {
            throw new PriceMismatchException();
        }
        Redis::del($this->getRedisKey($response['merchantReference']));

        return $response;
    }


    public function getRedisKey($reference): string
    {
        return $this->redisKey . '_' . $reference;
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
