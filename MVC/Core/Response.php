<?php

class Response {
    private $statusCode = 200;
    private $headers = [];
    private $body = null;

    public function __construct() {
        $this->header('Content-Type', 'application/json; charset=utf-8');
        $this->header('Access-Control-Allow-Origin', '*');
        $this->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $this->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
    }

    public function status($statusCode) {
        $this->statusCode = (int) $statusCode;
        return $this;
    }

    public function header($name, $value) {
        $this->headers[(string) $name] = (string) $value;
        return $this;
    }

    public function json($data, $statusCode = 200) {
        $this->status((int) $statusCode);
        $this->body = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return $this;
    }

    public function noContent() {
        $this->status(204);
        $this->body = '';
        return $this;
    }

    public function send() {
        if (!headers_sent()) {
            http_response_code($this->statusCode);
            foreach ($this->headers as $name => $value) {
                header($name . ': ' . $value);
            }
        }

        if ($this->statusCode !== 204 && $this->body !== null) {
            echo $this->body;
        }
    }
}
