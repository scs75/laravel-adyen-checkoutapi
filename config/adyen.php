<?php
 return [
     'apiKey' => env('ADYEN_API_KEY'),
     'baseUrl' => env('ADYEN_BASE_URL', 'https://checkout-test.adyen.com'),
     'fallback' => [
         'country' => 'EN',
         'locale' => 'en_GB',
     ],
     'currency' => 'EUR',
 ];
