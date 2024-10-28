<?php

namespace App\Exceptions;

use Exception;

class ObjectNotFoundException extends Exception
{
    public function __construct($message = "The requested object was not found.", $code = 404, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
