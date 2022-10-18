<?php

namespace One\Exceptions;

class Exception
{
    public function render(\Throwable $e)
    {
        $errors = $this->getErrors($e);
        $code = $this->getStatusCode($e);

        return $this->setBody(e: $e, errors: $errors, code: $code);
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

    private function setBody(\Throwable $e, mixed $errors, int $code)
    {
        response()->code($code);

        if (response()->getHttpRequest()->isJson()) {
            $body = response()->json(null, $errors, $code);
        } else {
            $file = _APP_PATH_VIEW_ . '/exceptions/' . $code . '.php';
            if (file_exists($file)) {
                $body = response()->tpl('exceptions/' . $code, ['e' => $e]);
            } else {
                $body = response()->json(null, $errors, $code);
            }
        }

        return $body;
    }

}