<?php

namespace App\Application\Patient\Exception;

use Exception;

class NoActivePatientCardsFoundException extends Exception
{
    public function __construct(string $message = "", int $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->message = "No active cards found";
    }
    
}
