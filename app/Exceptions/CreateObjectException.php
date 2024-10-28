<?php

namespace App\Exceptions;

use Exception;

class CreateObjectException extends Exception
{
    public function __construct($message = "Failed to create object.", $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
