<?php
class AuthController extends controller {
    // Hiển thị giao diện chào mừng (Hello.php)
    public function index() {
        ob_start();
        $this->view("Pages/Login"); 
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
            $user = $_POST['username'];
            $pass = $_POST['password'];
            $model = $this->model("AccountModel");

            // Khởi động session nếu chưa có
            if (session_status() == PHP_SESSION_NONE) session_start();

            switch ($userType) {
                case "quan_tri":
                    $check = $model->checkAdminLogin($user, $pass);
                    if ($check) {
                        // LƯU SESSION CHO ADMIN
                        $_SESSION['user_id'] = $check['MaAdmin'] ?? $user; // Hoặc ID tương ứng
                        $_SESSION['user_role'] = 'admin'; // Đánh dấu là Admin
                        $_SESSION['user_name'] = "Quản Trị Viên";
                        
                        header("Location: ?controller=AdminController&action=index");
                        exit();
                    }
                    break;

                case "nhan_vien":
                    $check = $model->checkEmployeeLogin($user, $pass);
                    if ($check) {
                        // LƯU SESSION CHO NHÂN VIÊN
                        $_SESSION['user_id'] = $check['MaDangNhap']; 
                        $_SESSION['user_role'] = 'employee'; // Đánh dấu là Nhân viên
                        // Lấy tên nhân viên nếu cần thiết từ model (đã join bảng)
                        $_SESSION['user_name'] = $user;

                        // Chuyển hướng tới trang đặt phòng hoặc trang chủ nhân viên
                        header("Location: ?controller=BookingController&action=index"); 
                        exit();
                    }
                    break;

                case "khach_hang":
                    $check = $model->checkGuestLogin($user, $pass);
                    if ($check) {
                        if (session_status() == PHP_SESSION_NONE) session_start();
                        
                        $_SESSION['guest_id'] = $check['MaKhachHang'];
                        $_SESSION['guest_name'] = $check['HoTen'];
                        
                        // --- DÒNG QUAN TRỌNG MỚI THÊM ---
                        $_SESSION['user_role'] = 'customer'; // Đánh dấu đây là Khách hàng
                        // --------------------------------
                        
                        header("Location: ?controller=GuestController&action=home");
                        exit();
                    }
                    break;
            }

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