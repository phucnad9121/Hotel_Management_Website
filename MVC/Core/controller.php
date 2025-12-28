<?php
class controller {
    public function model($model) {
        require_once "./MVC/Models/".$model.".php";
        return new $model;
    }

    public function view($view, $data = []) {
        // Sử dụng đường dẫn tính từ index.php
        require_once "./MVC/Views/".$view.".php";
    }
}
?>