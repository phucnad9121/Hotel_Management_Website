<?php
namespace Http;
class Request {
    public $cookie;
    public $request;
    public $files;
    public function __construct() {
        $this->request = ($_REQUEST);
        $this->cookie = $this->clean($_COOKIE);
        $this->files = $this->clean($_FILES);
    }
    public function get(String $key = '') {
        if ($key != '')
            return isset($_GET[$key]) ? $this->clean($_GET[$key]) : null;
        return  $this->clean($_GET);
    }
    public function post(String $key = '') {
        if ($key != '')
            return isset($_POST[$key]) ? $this->clean($_POST[$key]) : null;
        return  $this->clean($_POST);
    }
    public function input(String $key = '') {
        $postdata = file_get_contents("php://input");
        $request = json_decode($postdata, true);
        if ($key != '') {
            return isset($request[$key]) ? $this->clean($request[$key]) : null;
        }
        return ($request);
    }
    public function server(String $key = '') {
        return isset($_SERVER[strtoupper($key)]) ? $this->clean($_SERVER[strtoupper($key)]) : $this->clean($_SERVER);
    }
    public function getMethod() {
        return strtoupper($this->server('REQUEST_METHOD'));
    }
    public function getClientIp() {
        return $this->server('REMOTE_ADDR');
    }
    public function getUrl() {
        return $this->server('REQUEST_URI');
    }
    private function clean($data) {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                unset($data[$key]);
                $data[$this->clean($key)] = $this->clean($value);
            }
        } else {
            $data = htmlspecialchars($data, ENT_COMPAT, 'UTF-8');
        }
        return $data;
    }
}
