<?php
namespace Router;
final class Route {
    private $method;
    private $pattern;
    private $callback;
    private $list_method = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'];
    public function __construct(String $method, String $pattern, $callback) {
        $this->method = $this->validateMethod(strtoupper($method));
        $this->pattern = cleanUrl($pattern);
        $this->callback = $callback;
    }
    private function validateMethod(string $method) {
        if (in_array(strtoupper($method), $this->list_method))
            return $method;
        throw new \Exception('Invalid Method Name');
    }
    public function getMethod() { return $this->method; }
    public function getPattern() { return $this->pattern; }
    public function getCallback() { return $this->callback; }
}
