<?php

namespace One\Database\Mysql;

use One\Facades\Log;

class DbException extends \Exception
{
    public function __construct(string $message="", int $code = 1, ?\Throwable $previous=null, int $exceptionCode=0)
    {
        Log::error($message, 3 + $code);
        parent::__construct($message, $exceptionCode, $previous);
    }
}