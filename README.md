# Laravel Adyen Checkout Api

[![Latest Version on Packagist](https://img.shields.io/packagist/v/pixwell-dev/laravel-adyen-checkoutapi.svg?style=flat-square)](https://packagist.org/packages/pixwell-dev/laravel-adyen-checkoutapi)
[![Build Status](https://img.shields.io/travis/pixwell-dev/laravel-adyen-checkoutapi/master.svg?style=flat-square)](https://travis-ci.org/pixwell-dev/laravel-adyen-checkoutapi)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/45841589-02a4-4ef4-848e-b10cede59cc7.svg?style=flat-square)](https://insight.sensiolabs.com/projects/45841589-02a4-4ef4-848e-b10cede59cc7)
[![Quality Score](https://img.shields.io/scrutinizer/g/pixwell-dev/laravel-adyen-checkoutapi.svg?style=flat-square)](https://scrutinizer-ci.com/g/pixwell-dev/laravel-adyen-checkoutapi)
[![Total Downloads](https://img.shields.io/packagist/dt/pixwell-dev/laravel-adyen-checkoutapi.svg?style=flat-square)](https://packagist.org/packages/pixwell-dev/laravel-adyen-checkoutapi)

This is a laravel bridge for [Adyen Checkout api](https://docs.adyen.com/developers/in-app-integration/checkout-api-reference). Support only two methods `setup` and `verify`. With additional check for price after `verify` call, supported with redis.


## Installation

You can install the package via composer:

```bash
composer require pixwell-dev/laravel-adyen-checkoutapi
```

## Usage

#### Setup method
``` php
$setupRequest = (new SetupRequest('client_sdk_token', 'channel'))
				->setAmount($request->amount);
				
$response = app(AdyenCheckoutApi::class)->setup($setupRequest);
```
##### Additional methods
- `setShopperReference('')`: Internal Shopper reference.
- `setCountryCode('')`: A valid value is an ISO 2-character country code.
- `setShopperLocale('')`:The shopper language: Example: nl_NL or en_GB.
- `setReturnUrl('')`: URL/app that will be opened in case a redirect payment method is used.
- `setReference('')`: A unique reference you provide to the transaction, will be used for updates to the payment like refunds.
- `setSessionValidity(OneSignalButton $button)`: The payment deadline; the payment needs to be processed within the specified time value: [Format: ISO 8601](https://www.w3.org/TR/NOTE-datetime);


#### Verify method
``` php
$response = app(AdyenCheckoutApi::class)->verify($payload, $price);
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email developer@pixwell.sk instead of using the issue tracker.

## Credits

- [pixwell-dev](https://github.com/pixwell-dev)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
