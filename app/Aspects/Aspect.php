<?php

namespace App\Aspects;

abstract class Aspect
{
    protected $message;
    public function __construct($message){
        $this->message = $message;
    }

}
