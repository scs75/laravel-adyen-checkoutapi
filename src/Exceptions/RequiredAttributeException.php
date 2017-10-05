<?php

namespace Pixwell\LaravelAdyenCheckoutApi\Exceptions;

use Throwable;

class RequiredAttributeException extends \Exception
{

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct('Required attribute missing check adyen setup api reference', 400, $previous);
    }
}
