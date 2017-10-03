<?php

namespace Pixwell\LaravelAdyenCheckoutApi;

use Illuminate\Contracts\Cache\Repository;
use Pixwell\LaravelAdyenCheckoutApi\Exceptions\AdyenBaseUrlException;
use Pixwell\LaravelAdyenCheckoutApi\Exceptions\AdyenResponseException;
use Pixwell\LaravelAdyenCheckoutApi\Exceptions\PriceMismatchException;
use Pixwell\LaravelAdyenCheckoutApi\Exceptions\RequiredAttributeException;
use Zttp\Zttp;

class AdyenCheckoutApi
{

    const API_VERSION = 'v30';

    public $url;

    public $apiKey;

    public $cacheKey = 'adyen:setup.reference';

    /**
     * @var Repository
     */
    private $cache;


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
        $this->cache = app(Repository::class);
    }


    public function setup(SetupRequest $setupRequest)
    {
        $payload = $setupRequest->toArray();

        $references = $this->cache->get($this->cacheKey);

        if (! isset($payload['amount']['value'])) {
            throw new RequiredAttributeException();
        }
        $references->put(str_random(40), $payload['amount']['value']);
        $this->cache->put($this->cacheKey, $references, null);

        return $this->makeRequest($this->url . '/setup', $payload);
    }


    public function verify($payload, $price)
    {
        $response = $this->makeRequest($this->url . '/verify', ['payload' => $payload]);

        $references = $this->cache->get($this->cacheKey);
        $setupPrice = $references->first(function ($value, $key) use ($response) {
            return $key == $response['merchantReference'];
        });

        if (! $setupPrice && $setupPrice != $price) {
            throw new PriceMismatchException();
        }

        return $response;
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
