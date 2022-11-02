<?php

namespace One\Exceptions;

class HttpException extends \Exception
{
	private ?array $data;

	public function __construct(string $message="", int $code=0, array $data=null, ?\Throwable $previous=null)
	{
		$this->data = $data;
		parent::__construct(message: $message, code: $code, previous: $previous);
	}

	public function getData()
	{
		return $this->data;
	}
}