<?php

namespace App\Domain\Patient\Exception;

use Exception;

class NoPatientCardsFoundException extends Exception
{
    public function __construct(string $message, int $code = 0, Exception $previous = null)
    {
        $this->message = "Tried to access a non-existing card of patient: " . $message;
        parent::__construct($message, $code, $previous);
    }
}
