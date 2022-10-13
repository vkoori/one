<?php 

namespace One\Validation;

class ValidationException extends \Exception
{
    /**
     * @var array
     */
    private $errors = [];

    public function __construct(
        array $errors = [],
        string $message = 'Error getting validation data', 
        int $code = 0, 
        \Throwable $previous = null
    )
    {
        $this->errors = $errors;

        parent::__construct($message, $code, $previous);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}