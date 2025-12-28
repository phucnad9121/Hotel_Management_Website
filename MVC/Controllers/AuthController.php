<?php
class AuthController extends controller {
    // Hiển thị giao diện chào mừng (Hello.php)
    public function index() {
        ob_start();
        $this->view("Pages/Hello"); 
        $content = ob_get_clean();

        $this->view("Master", [
            "content" => $content
        ]);
    }

    // MỚI: Hiển thị giao diện Đăng nhập (Login.php)
    public function login() {
        ob_start();
        $this->view("Pages/Login"); // Gọi file Login.php bạn đã thiết kế
        $content = ob_get_clean();

        $this->view("Master", [
            "content" => $content
        ]);
    }

   public function handleLogin() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $userType = $_POST['account_type'];
        $user = $_POST['username']; // Đối với khách hàng, đây là Số điện thoại
        $pass = $_POST['password'];
        $model = $this->model("AccountModel");

        switch ($userType) {
            case "quan_tri":
                $check = $model->checkAdminLogin($user, $pass);
                if ($check) {
                    header("Location: ?controller=AdminController&action=index");
                    exit();
                }
                break;

            case "nhan_vien":
                $check = $model->checkEmployeeLogin($user, $pass);
                if ($check) {
                    // Chuyển hướng tới trang nhân viên (Bạn cần tạo EmployeeController)
                    header("Location: ?controller=EmployeeController&action=index");
                    exit();
                }
                break;

            case "khach_hang":
                $check = $model->checkGuestLogin($user, $pass);
                if ($check) {
                    // Lưu thông tin khách hàng vào session nếu cần
                    // $_SESSION['guest'] = $check;
                    header("Location: ?controller=GuestController&action=index");
                    exit();
                }
                break;
        }

        // Nếu chạy xuống đến đây tức là không khớp tài khoản nào
        echo "<script>alert('Tài khoản hoặc mật khẩu không chính xác!'); window.history.back();</script>";
    }
}
public function logout() {
        // 1. Khởi động session nếu chưa có
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // 2. Xóa sạch các biến session (User, Role, v.v.)
        session_unset();

        // 3. Hủy bỏ session
        session_destroy();

        // 4. Chuyển hướng người dùng về trang Đăng nhập
        // Thay 'LoginController' bằng tên Controller quản lý trang đăng nhập của bạn
        header("Location: ?controller=AuthController&action=index");
        exit();
    }
}
?>