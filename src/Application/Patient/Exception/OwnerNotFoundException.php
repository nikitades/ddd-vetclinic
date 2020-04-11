<?php

namespace App\Application\Patient\Exception;

use Exception;

class OwnerNotFoundException extends Exception
{
    public function __construct(string $message, int $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->message = "The owner was not found: " . $message;
    }
    
}
