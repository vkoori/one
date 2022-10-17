<?php

namespace One\Http\Response;

use One\Exceptions\HttpException;
use One\Http\Request\Request;

class Response
{

    /**
     * @var \One\Http\Request\Request
     */
    protected $httpRequest;

    protected $_session = null;

    public $_auto_to_json = true;


    public function __construct(Request $request)
    {
        $this->httpRequest = $request;
    }


    public function getHttpRequest()
    {
        return $this->httpRequest;
    }

    /**
     * @return Session
     */
    public function session()
    {
        if (!$this->_session) {
            if (_CLI_) {
                $this->_session = new \One\Swoole\Session($this);
            } else {
                $this->_session = new \One\Http\Session($this);
            }
        }
        return $this->_session;
    }

    /**
     * @return bool
     */
    public function cookie()
    {
        return setcookie(...func_get_args());
    }

    public function write($html)
    {
        echo $html;
    }


    /**
     * @param mixed $data
     * @param int $code
     * @param null|string $callback
     * @return string
     */
    public function json($data, $code = 0, $callback = null)
    {
        $this->header('Content-type', 'application/json');
        if ($callback === null) {
            return format_json($data, $code, $this->httpRequest->id());
        } else {
            return $callback . '(' . format_json($data, $code, $this->httpRequest->id()) . ')';
        }
    }

    public function header($key, $val, $replace = false, $code = null)
    {
        header($key . ':' . $val, $replace, $code);
    }

    /**
     * @param $code
     * @return $this
     */
    public function code($code): self
    {
        http_response_code($code);

        return $this;
    }


    /**
     * 页面跳转
     * @param $url
     * @param array $args
     */
    public function redirect($url, $args = [])
    {
        if (isset($args['time'])) {
            $this->header('Refresh', $args['time'] . ';url=' . $url);
        } else if (isset($args['httpCode'])) {
            $this->header('Location', $url, true, $args['httpCode']);
        } else {
            $this->header('Location', $url, true, 302);
        }
        return '';
    }

    /**
     * 执行路径
     * @param $method
     * @param $url
     * @param null $server
     */
    public function redirectCall($url, $method = 'get', $server = null)
    {
        $router = new Router();
        $req    = $this->httpRequest;
        $res    = $this;
        list($req->class, $req->func, $mids, $action, $req->args, $req->as_name) = $router->explain($method, $url, $req, $res, $server);
        $f = $router->getExecAction($mids, $action, $res, $server);
        return $f();
    }

    /**
     * @param string $file
     * @param array $data
     * @return string
     * @throws HttpException
     */
    public function tpl($file, array $data = [])
    {
        if ($this->_auto_to_json && $this->getHttpRequest()->isJson()) {
            $this->header('Content-type', 'application/json');
            return format_json($data, 0, $this->getHttpRequest()->id());
        } else {
            if (defined('_APP_PATH_VIEW_') === false) {
                throw new HttpException('未定义模板路径:_APP_PATH_VIEW_', 4001);
            }
            $file = _APP_PATH_VIEW_ . '/' . $file . '.php';
            if (!file_exists($file)) {
                throw new HttpException('未定义模板路径:' . $file, 4002);
            }
            ob_start();
            extract($data);
            require $file;
            return ob_get_clean();
        }
    }

}