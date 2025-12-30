<?php

namespace iutnc\SAE_APP_WEB\exception;

class TokenException extends \Exception{
    public function __construct(string $message){
        parent::__construct($message);
    }
}