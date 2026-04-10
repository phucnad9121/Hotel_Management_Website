<?php
namespace Router;
class Router {
    private $router = [];
    private $matchRouter = [];
    private $url;
    private $method;
    private $params = [];
    private $response;
    public function __construct(string $url, string $method) {
        // Remove base path for subdirectory projects
        $url = preg_replace('#^/Hotel_Management_Website#', '', $url);
        // Remove query string
        if (($pos = strpos($url, '?')) !== false) {
            $url = substr($url, 0, $pos);
        }
        $this->url = rtrim($url, '/');
        $this->method = $method;
        $this->response = $GLOBALS['response'];
    }
    public function get($pattern, $callback) { $this->addRoute("GET", $pattern, $callback); }
    public function post($pattern, $callback) { $this->addRoute('POST', $pattern, $callback); }
    public function put($pattern, $callback) { $this->addRoute('PUT', $pattern, $callback); }
    public function delete($pattern, $callback) { $this->addRoute('DELETE', $pattern, $callback); }
    public function addRoute($method, $pattern, $callback) {
        array_push($this->router, new Route($method, $pattern, $callback));
    }
    private function getMatchRoutersByRequestMethod() {
        foreach ($this->router as $value) {
            if (strtoupper($this->method) == $value->getMethod())
                array_push($this->matchRouter, $value);
        }
    }
    private function getMatchRoutersByPattern($pattern) {
        $this->matchRouter = [];
        foreach ($pattern as $value) {
            if ($this->dispatch(cleanUrl($this->url), $value->getPattern()))
                array_push($this->matchRouter, $value);
        }
    }
    public function dispatch($uri, $pattern) {
        $parsUrl = explode('?', $uri);
        $url = $parsUrl[0];
        preg_match_all('@:([\w]+)@', $pattern, $params, PREG_PATTERN_ORDER);
        $patternAsRegex = preg_replace_callback('@:([\w]+)@', [$this, 'convertPatternToRegex'], $pattern);
        if (substr($pattern, -1) === '/' ) { $patternAsRegex = $patternAsRegex . '?'; }
        $patternAsRegex = '@^' . $patternAsRegex . '$@';
        if (preg_match($patternAsRegex, $url, $paramsValue)) {
            array_shift($paramsValue);
            foreach ($params[0] as $key => $value) {
                $val = substr($value, 1);
                if (isset($paramsValue[$val])) {
                    $this->setParams($val, urlencode($paramsValue[$val]));
                }
            }
            return true;
        }
        return false;
    }
    public function getRouter() { return $this->router; }
    private function setParams($key, $value) { $this->params[$key] = $value; }
    private function convertPatternToRegex($matches) {
        $key = str_replace(':', '', $matches[0]);
        return '(?P<' . $key . '>[a-zA-Z0-9_\-\.\!\~\*\'\(\)\:\@\&\=\$\+,%]+)';
    }
    public function run() {
        if (!is_array($this->router) || empty($this->router))
            throw new \Exception('NON-Object Route Set');
        $this->getMatchRoutersByRequestMethod();
        $this->getMatchRoutersByPattern($this->matchRouter);
        if (!$this->matchRouter || empty($this->matchRouter)) {
            $this->sendNotFound();
        } else {
            if (is_callable($this->matchRouter[0]->getCallback()))
                call_user_func($this->matchRouter[0]->getCallback(), $this->params);
            else
                $this->runController($this->matchRouter[0]->getCallback(), $this->params);
        }
    }
    private function runController($controller, $params) {
        $parts = explode('@', $controller);
        $file = CONTROLLERS . ucfirst($parts[0]) . '.php';
        if (file_exists($file)) {
            require_once($file);
            $controller = 'Controllers' . ucfirst($parts[0]);
            if (class_exists($controller))
                $controller = new $controller();
            else
                $this->sendNotFound();
            if (isset($parts[1])) {
                $method = $parts[1];
                if (!method_exists($controller, $method))
                    $this->sendNotFound();
            } else {
                $method = 'index';
            }
            if (is_callable([$controller, $method]))
                return call_user_func([$controller, $method], $params);
            else
                $this->sendNotFound();
        }
    }
    private function sendNotFound() {
        $this->response->sendStatus(404);
        $this->response->setContent(['error' => 'Route Not Found', 'status_code' => 404]);
    }
}
