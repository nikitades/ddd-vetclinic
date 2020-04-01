<?php

namespace App\Domain\Patient\Exception;

use Exception;

class MoreThanOneActiveCardIsNotAllowedException extends Exception
{

    public function __construct($message, $code = 0, Exception $previous = null)
    {
        $this->message = "It's allowed to have not more than 1 active card at the moment for patient: " . $message;
        parent::__construct($message, $code, $previous);
    }
}
