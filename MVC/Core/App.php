<?php
class App {
    protected $controller = "AuthController";
    protected $action = "index";
    protected $params = [];

    public function __construct() {

        if (isset($_GET['controller'])) {
            $this->controller = $_GET['controller'];
        }

        require_once "./MVC/Controllers/" . $this->controller . ".php";
        $this->controller = new $this->controller;

        if (isset($_GET['action'])) {
            $this->action = $_GET['action'];
        }

        call_user_func([$this->controller, $this->action]);
    }
}
