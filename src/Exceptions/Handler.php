<?php

namespace One\Exceptions;

use One\Http\Request;
use One\Http\Response;

class Exception
{
    /**
     * @var \One\Http\Response
     */
    private $response;

    public function render(\Throwable $e)
    {
        $errors = $this->getErrors($e);
        $code = $this->getStatusCode($e);

        return $this
            ->setResponse(e: $e)
            ->setBody(e: $e, errors: $errors, code: $code);
    }

    public function getErrors(\Throwable $e)
    {
        return match ($e::class) {
            default => $this->default($e),
        };
    }

    private function default(\Throwable $e)
    {
        error_report($e);
        return $e->getMessage();
    }

    private function getStatusCode(\Throwable $e): int
    {
        $code = $e->getCode();
        if ($code === 0) {
            $code = 500;
        }

        return $code;
    }

    private function setResponse(\Throwable $e): self
    {
        $this->response = isset($e->response) ? $e->response : new Response(new Request);
        return $this;
    }

    private function setBody(\Throwable $e, mixed $errors, int $code)
    {
        $this->response->code($code);

        if ($this->response->getHttpRequest()->isJson()) {
            $body = $this->response->json($errors, $code);
        } else {
            $file = _APP_PATH_VIEW_ . '/exceptions/' . $code . '.php';
            if (file_exists($file)) {
                $body = $this->response->tpl('exceptions/' . $code, ['e' => $e]);
            } else {
                $body = $this->response->json($errors, $code);
            }
        }

        return $body;
    }

}