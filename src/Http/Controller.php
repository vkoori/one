<?php

namespace One\Http;

use One\Exceptions\HttpException;

class Controller
{

    /**
     * @return Session
     */
    public static function session()
    {
        return response()->session();
    }

    /**
     * 异常处理
     * @param $msg
     * @param int $code
     * @throws HttpException
     */
    public static function error($msg, $code = 400)
    {
        throw new HttpException(message: $msg, code: $code);
    }

    /**
     * @param $data
     * @return string
     */
    public static function json($data, $msg='', $code=200)
    {
        return response()->json($data, $msg, $code);
    }

    /**
     * @param $data
     * @param string $callback
     * @return string
     */
    public static function jsonP($data, $msg='', $callback = 'callback')
    {
        return response()->json($data, $msg, 0, $callback);
    }

    /**
     * 模板渲染
     * @param string $tpl 模板
     * @param array $data
     * @return string
     * @throws HttpException
     */
    public static function view(string $tpl, array $data = [], bool $auto_set_tpl_dir = true)
    {
        if ($auto_set_tpl_dir) {
            $dir = substr(get_called_class(), 4);
            $dir = str_replace(['Controllers', 'Controller'], '', $dir);
            $dir = str_replace('\\', '/', $dir);
            $dir = str_replace('//', '/', $dir);
            $dir = strtolower(trim($dir, '/'));
            return response()->tpl($dir . '/' . $tpl, $data);
        } else {
            return response()->tpl($tpl, $data);
        }

    }

}