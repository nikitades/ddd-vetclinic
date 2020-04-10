<?php

namespace App\Domain\Shared\Exception;

use Exception;

class WrongValueException extends Exception
{
    public function __construct(string $message, int $code = 0, Exception $previous = null)
    {
        $this->message = "Wrong input value given: " . $message;
        parent::__construct($message, $code, $previous);
    }
}
