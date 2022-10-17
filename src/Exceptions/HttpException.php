<?php

namespace One\Exceptions;

class HttpException extends \Exception
{
    public function __construct($message = "", $code = 0, $previous = null)
    {
    }
}