<?php
namespace Http;
class Response {
    protected $headers = [];
    protected $statusTexts = [
        100 => 'Continue', 101 => 'Switching Protocols', 200 => 'OK',
        201 => 'Created', 202 => 'Accepted', 204 => 'No Content',
        301 => 'Moved Permanently', 302 => 'Found', 304 => 'Not Modified',
        400 => 'Bad Request', 401 => 'Unauthorized', 403 => 'Forbidden',
        404 => 'Not Found', 405 => 'Method Not Allowed', 409 => 'Conflict',
        422 => 'Unprocessable Entity', 500 => 'Internal Server Error',
        503 => 'Service Unavailable',
    ];
    protected $version;
    protected $content;
    public function __construct () {
        $this->setVersion('1.1');
    }
    public function setVersion(string $version) {
        $this->version = $version;
    }
    public function getVersion() : string {
        return $this->version;
    }
    public function getStatusCodeText(int $code) : string {
        return (string) isset($this->statusTexts[$code]) ? $this->statusTexts[$code] : 'unknown status';
    }
    public function setHeader(String $header) {
        $this->headers[] = $header;
    }
    public function getHeader() {
        return $this->headers;
    }
    public function setContent($content) {
        $this->content = json_encode($content, JSON_UNESCAPED_UNICODE);
    }
    public function getContent() {
        return $this->content;
    }
    public function redirect($url) {
        if (empty($url)) { trigger_error('Cannot redirect to an empty URL.'); exit; }
        header('Location: ' . str_replace(array('&amp;', "\n", "\r"), array('&','', ''), $url), true, 302);
        exit();
    }
    public function isInvalid(int $statusCode) : bool {
        return $statusCode < 100 || $statusCode >= 600;
    }
    public function sendStatus($code) {
        if (!$this->isInvalid($code)) {
            $this->setHeader(sprintf('HTTP/1.1 ' . $code . ' %s' , $this->getStatusCodeText($code)));
        }
    }
    public function render() {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(204);
            exit;
        }
        if ($this->content) {
            $output = $this->content;
            if (!headers_sent()) {
                foreach ($this->headers as $header) {
                    header($header, true);
                }
            }
            echo $output;
        }
    }
}
