<?php

namespace One\Http\Request;

use One\Facades\Log;

class Request
{

    protected $server = [];

    protected $cookie = [];

    protected $get = [];

    protected $post = [];

    protected $files = [];

    protected $request = [];

    protected $headers = [];

    public $fd = 0;

    public $args = [];

    public $class = '';

    public $func = '';
    
    public $as_name = '';

    private $attr = [];

    public function __construct()
    {
        $this->server  = &$_SERVER;
        $this->cookie  = &$_COOKIE;
        $this->get     = trimArr(arr: $_GET);
        $this->post    = trimArr(arr: $_POST);
        $this->files   = &$_FILES;
        $this->request = trimArr(arr: $_REQUEST);
        $this->headers = $this->getAllHeaders();
    }

    /**
     * @return string|null
     */
    public function ip($ks = ['REMOTE_ADDR'])
    {
        foreach ($ks as $k){
            $ip = $this->server($k);
            if($ip !== null){
                return $ip;
            }
        }
        return null;
    }


    /**
     * @param $name
     * @return mixed|null
     */
    public function server($name = null, $default = null)
    {
        if ($name === null) {
            return $this->server;
        }
        if (isset($this->server[$name])) {
            return $this->server[$name];
        }
        $name = strtolower($name);
        if (isset($this->server[$name])) {
            return $this->server[$name];
        }
        $name = str_replace('_', '-', $name);
        if (isset($this->server[$name])) {
            return $this->server[$name];
        }
        return $default;
    }

    /**
     * @return mixed|null
     */
    public function userAgent()
    {
        return $this->server('HTTP_USER_AGENT');
    }

    /**
     * @return string
     */
    private function getFullUri()
    {
        $path  = urldecode(array_get_not_null($this->server, ['request_uri', 'REQUEST_URI', 'argv.1']));
        $paths = explode('?', $path);
        return '/' . trim($paths[0], '/');
    }

    public function setAttr(string $key, mixed $value): void
    {
        $this->attr[$key] = $value;
    }

    public function getAttr(string $key): mixed
    {
        return $this->attr[$key] ?? Null;
    }

    public function uri()
    {
        $appPath = str_replace('\\', '/', _APP_PATH_);
        $subDirectory = str_replace($this->server('DOCUMENT_ROOT'), '', $appPath.'/public');
        $reqUri = str_replace($subDirectory, '', $this->getFullUri());

        return $reqUri;
    }

    public function baseUrl(): string
    {
        $appPath = str_replace('\\', '/', _APP_PATH_);
        $subDirectory = str_replace($this->server('DOCUMENT_ROOT'), '', $appPath.'/public');
        return $this->server('HTTPS') ? "https" : "http" . "://" . $this->server('HTTP_HOST') . $subDirectory;
    }

    /**
     * request unique id
     * @return string
     */
    public function id()
    {
        return Log::getTraceId();
    }


    protected function getFromArr($arr, $key, $default = null)
    {
        if ($key === null) {
            return $arr;
        }
        return array_get($arr, $key, $default);
    }

    /**
     * @param $key
     * @param $default
     * @return mixed|null
     */
    public function get($key = null, $default = null)
    {
        return $this->getFromArr($this->get, $key, $default);
    }

    /**
     * @param $key
     * @return mixed|null
     */
    public function post($key = null, $default = null)
    {
        return $this->getFromArr($this->post, $key, $default);
    }

    /**
     * @param int $i
     * @return mixed|null
     */
    public function arg($i = null, $default = null)
    {
        global $argv;
        return $this->getFromArr($argv, $i, $default);
    }


    /**
     * @param $key
     * @return mixed|null
     */
    public function res($key = null, $default = null)
    {
        return $this->getFromArr($this->request, $key, $default);
    }


    /**
     * @param $key
     * @return mixed|null
     */
    public function cookie($key = null, $default = null)
    {
        return $this->getFromArr($this->cookie, $key, $default);
    }

    /**
     * @return string
     */
    public function input()
    {
        return file_get_contents('php://input');
    }

    /**
     * @return array
     */
    public function json()
    {
        return json_decode($this->input(), true) ?? [];
    }

    /**
     * @return array
     */
    public function file()
    {
        $files = [];
        foreach ($this->files as $name => $fs) {
            $keys = array_keys($fs);
            if (is_array($fs[$keys[0]])) {
                foreach ($keys as $k => $v) {
                    foreach ($fs[$v] as $i => $val) {
                        $files[$name][$i][$v] = $val;
                    }
                }
            } else {
                $files[$name] = $fs;
            }
        }
        return $files;
    }

    public function all(): array
    {
        return array_merge($this->res(), $this->json(), $this->file());;
    }

    /**
     * @return string
     */
    public function method()
    {
        return strtolower($this->server('REQUEST_METHOD'));
    }

    /**
     * @return bool
     */
    public function isJson()
    {
        if ($this->server('HTTP_X_REQUESTED_WITH') == 'XMLHttpRequest' || strpos($this->server('HTTP_ACCEPT'), '/json') !== false) {
            return true;
        } else {
            return false;
        }
    }

    public function getHeader(?string $key=Null, ?string $default=Null)
    {
        if (is_null($key)) {
            return $this->headers;
        }

        return $this->headers[$this->initHeaderKey(key: $key)] ?? $default;
    }

    public function setHeader(string $key, string $value): void
    {
        $this->headers[$this->initHeaderKey(key: $key)] = $value;
    }

    private function getAllHeaders(): array
    {
        $response = [];

        $headers = function_exists('getallheaders') ? \getallheaders() : [];
        foreach ($headers as $key => $value) {            
            $response[$this->initHeaderKey(key: $key)] = $value;
        }

        return $response;
    }

    private function initHeaderKey(string $key): string
    {
        $key = str_replace(search: '_', replace: '-', subject: $key);
        $key = strtolower(string: $key);
        return $key;
    }


}