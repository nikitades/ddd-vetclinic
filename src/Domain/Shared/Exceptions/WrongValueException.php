<?php

namespace App\Domain\Shared\Exceptions;

use Exception;

class WrongValueException extends Exception
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        $this->message = "Wrong input value given: " . $message;
        parent::__construct($message, $code, $previous);
    }
}
