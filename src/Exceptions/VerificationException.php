<?php

namespace Pixwell\LaravelAdyenCheckoutApi\Exceptions;

use Throwable;

class VerificationException extends \Exception
{

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct('Check for adyen checkout api documentation', 400, $previous);
    }
}

