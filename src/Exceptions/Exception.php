<?php

namespace One\Exceptions;

class Exception
{
    public function render(\Throwable $e)
    {
        $errors = $this->getErrors($e);
        $data = $this->getData($e);
        $code = $this->getStatusCode($e);

        return $this->setBody(e: $e, errors: $errors, code: $code, data: $data);
    }

    public function getErrors(\Throwable $e)
    {
        return match ($e::class) {
            default => $this->default($e),
        };
    }

    public function getData(\Throwable $e)
    {
        return method_exists($e, 'getData') ? $e->getData() : null;
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

    private function setBody(\Throwable $e, mixed $errors, int $code, mixed $data)
    {
        response()->code($code);

        if (response()->getHttpRequest()->isJson()) {
            $body = response()->json($data, $errors, $code);
        } else {
            $file = _APP_PATH_VIEW_ . '/exceptions/' . $code . '.php';
            if (file_exists($file)) {
                $body = response()->tpl('exceptions/' . $code, ['e' => $e]);
            } else {
                $body = response()->json($data, $errors, $code);
            }
        }

        return $body;
    }

}