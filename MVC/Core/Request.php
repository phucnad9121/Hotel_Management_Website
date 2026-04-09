<?php

class Request {
    private $method;
    private $path;
    private $query;
    private $body;
    private $headers;
    private $routeParams = [];

    public function __construct() {
        $this->method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $this->path = $this->normalizePath(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');
        $this->query = $_GET ?? [];
        $this->headers = $this->collectHeaders();
        $this->body = $this->parseBody();
    }

    public function method() {
        return $this->method;
    }

    public function path() {
        return $this->path;
    }

    public function all() {
        return $this->body;
    }

    public function input($key = null, $default = null) {
        if ($key === null) {
            return $this->body;
        }

        return array_key_exists($key, $this->body) ? $this->body[$key] : $default;
    }

    public function query($key = null, $default = null) {
        if ($key === null) {
            return $this->query;
        }

        return array_key_exists($key, $this->query) ? $this->query[$key] : $default;
    }

    public function header($name, $default = null) {
        $target = strtolower((string) $name);

        foreach ($this->headers as $key => $value) {
            if (strtolower($key) === $target) {
                return $value;
            }
        }

        return $default;
    }

    public function bearerToken() {
        $auth = $this->header('Authorization', '');
        if (!is_string($auth) || $auth === '') {
            return null;
        }

        if (preg_match('/Bearer\s+(\S+)/i', $auth, $matches)) {
            return $matches[1];
        }

        return null;
    }

    public function setRouteParams(array $params) {
        $this->routeParams = $params;
    }

    public function route($key = null, $default = null) {
        if ($key === null) {
            return $this->routeParams;
        }

        return array_key_exists($key, $this->routeParams) ? $this->routeParams[$key] : $default;
    }

    private function parseBody() {
        $rawBody = file_get_contents('php://input');
        if (!is_string($rawBody)) {
            $rawBody = '';
        }

        $contentType = strtolower((string) $this->header('Content-Type', ''));

        if (strpos($contentType, 'application/json') !== false && $rawBody !== '') {
            $json = json_decode($rawBody, true);
            if (is_array($json)) {
                return $json;
            }
            return [];
        }

        if (strpos($contentType, 'application/x-www-form-urlencoded') !== false && $rawBody !== '') {
            $data = [];
            parse_str($rawBody, $data);
            return is_array($data) ? $data : [];
        }

        if (!empty($_POST) && is_array($_POST)) {
            return $_POST;
        }

        return [];
    }

    private function collectHeaders() {
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
            if (is_array($headers)) {
                return $headers;
            }
        }

        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (strpos($key, 'HTTP_') === 0) {
                $headerName = str_replace('_', '-', substr($key, 5));
                $headerName = ucwords(strtolower($headerName), '-');
                $headers[$headerName] = $value;
            }
        }

        if (!isset($headers['Authorization']) && isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
            $headers['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
        }

        if (isset($_SERVER['CONTENT_TYPE'])) {
            $headers['Content-Type'] = $_SERVER['CONTENT_TYPE'];
        }
        if (isset($_SERVER['CONTENT_LENGTH'])) {
            $headers['Content-Length'] = $_SERVER['CONTENT_LENGTH'];
        }

        return $headers;
    }

    private function normalizePath($path) {
        $path = '/' . ltrim((string) $path, '/');
        $path = preg_replace('#/+#', '/', $path);

        $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
        if ($scriptDir !== '/' && $scriptDir !== '.' && strpos($path, $scriptDir) === 0) {
            $path = substr($path, strlen($scriptDir));
            if ($path === '') {
                $path = '/';
            }
        }

        $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
        if ($scriptName !== '' && strpos($path, $scriptName) === 0) {
            $path = substr($path, strlen($scriptName));
            if ($path === '') {
                $path = '/';
            }
        }

        foreach (['/index.php', '/api.php'] as $entryPoint) {
            if (strpos($path, $entryPoint . '/') === 0) {
                $path = substr($path, strlen($entryPoint));
                if ($path === '') {
                    $path = '/';
                }
            }
        }

        if ($path !== '/' && substr($path, -1) === '/') {
            $path = rtrim($path, '/');
        }

        return $path;
    }
}
