<?php

namespace App\Domain\Patient\Exception;

use Exception;

class MoreThanOneActiveCardIsNotAllowedException extends Exception
{

    public function __construct(string $message, int $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->message = "It's allowed to have not more than 1 active card at the moment for patient: " . $message;
    }
}
