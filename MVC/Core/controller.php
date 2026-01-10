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

    protected function requireRole($allowed_roles = []) {
        if (session_status() == PHP_SESSION_NONE) session_start();

        // 1. Kiểm tra đã đăng nhập chưa
        if (!isset($_SESSION['user_role'])) {
            header("Location: ?controller=AuthController&action=login");
            exit();
        }

        // 2. Kiểm tra quyền
        // Nếu user_role hiện tại KHÔNG nằm trong danh sách được phép
        if (!in_array($_SESSION['user_role'], $allowed_roles)) {
            echo "<script>
                alert('BẠN KHÔNG CÓ QUYỀN TRUY CẬP TRANG NÀY!');
                window.history.back(); // Hoặc chuyển về trang chủ
            </script>";
            exit();
        }
    }
}
?>