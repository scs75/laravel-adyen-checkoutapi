<?php

namespace Pixwell\LaravelAdyenCheckoutApi\Exceptions;

use Throwable;

class AdyenEnvironmentException extends \Exception
{

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct('Bad environment config value', 400, $previous);
    }
}
