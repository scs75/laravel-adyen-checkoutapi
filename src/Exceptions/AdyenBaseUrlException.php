<?php

namespace Pixwell\LaravelAdyenCheckoutApi\Exceptions;

use Throwable;

class AdyenBaseUrlException extends \Exception
{

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct('Bad base url config value', 400, $previous);
    }
}
