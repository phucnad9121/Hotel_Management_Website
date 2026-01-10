<?php
class GuestProfileController extends Controller {

    // Hiển thị và xử lý form thông tin cá nhân
    public function index() {
        // 1. Kiểm tra đăng nhập (Bắt buộc)
        if (session_status() === PHP_SESSION_NONE) session_start();
        
        if (!isset($_SESSION['guest_id'])) {
            header("Location: ?controller=AuthController&action=login");
            exit();
        }

        $myId = $_SESSION['guest_id'];
        $model = $this->model("GuestProfileModel");
        
        // 2. Xử lý khi bấm nút LƯU (POST)
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $ho = $_POST['ho'];
            $ten = $_POST['ten'];
            $email = $_POST['email'];
            $sdt = $_POST['sdt'];
            $cmnd = $_POST['cmnd'];
            $diachi = $_POST['diachi'];
            $newPass = $_POST['password'];       // Mật khẩu mới
            $confirmPass = $_POST['confirm_password']; // Xác nhận mật khẩu

            // Validate dữ liệu
            if (empty($ho) || empty($ten) || empty($sdt)) {
                echo "<script>alert('Vui lòng điền đầy đủ Họ tên và SĐT!');</script>";
            } 
            // Kiểm tra mật khẩu (nếu có nhập)
            else if (!empty($newPass) && $newPass !== $confirmPass) {
                echo "<script>alert('Mật khẩu xác nhận không khớp!');</script>";
            }
            // Kiểm tra trùng SĐT
            else if ($model->checkPhoneUnique($sdt, $myId)) {
                echo "<script>alert('Số điện thoại này đã được sử dụng bởi người khác!');</script>";
            } 
            else {
                // Thực hiện cập nhật
                $result = $model->updateProfile($myId, $ho, $ten, $email, $sdt, $cmnd, $diachi, $newPass);
                
                if ($result) {
                    echo "<script>alert('Cập nhật hồ sơ thành công!'); window.location.href='?controller=GuestProfileController&action=index';</script>";
                } else {
                    echo "<script>alert('Có lỗi xảy ra, vui lòng thử lại!');</script>";
                }
            }
        }

        // 3. Lấy thông tin mới nhất để hiển thị ra View
        $guest = $model->getProfile($myId);

        // Gọi View (Tạo file GuestProfile.php ở Bước 3)
        ob_start();
        $this->view("Pages/GuestProfile", ["guest" => $guest]);
        $content = ob_get_clean();
        $this->view("Master", ["content" => $content]);
    }
}
?>