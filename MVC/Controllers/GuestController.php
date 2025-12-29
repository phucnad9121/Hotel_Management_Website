<?php
class GuestController extends controller {
    
    // Hiển thị form đăng ký
    public function register() {
        ob_start();
        $this->view("Pages/GuestRegister");
        $content = ob_get_clean();
        $this->view("Master", ["content" => $content]);
    }
    
    // Xử lý đăng ký
    public function handleRegister() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $model = $this->model("GuestModel");
            
            $data = [
                'TenKhachHang' => $_POST['ten'],
                'HoKhachHang' => $_POST['ho'],
                'EmailKhachHang' => $_POST['email'],
                'SoDienThoaiKhachHang' => $_POST['sdt'],
                'CMND_CCCDKhachHang' => $_POST['cmnd'],
                'DiaChi' => $_POST['diachi'],
                'MatKhau' => $_POST['password']
            ];
            
            // Kiểm tra mật khẩu xác nhận
            if ($_POST['password'] !== $_POST['confirm_password']) {
                echo "<script>alert('Mật khẩu xác nhận không khớp!'); window.history.back();</script>";
                return;
            }
            
            // Kiểm tra số điện thoại đã tồn tại
            if ($model->checkPhoneExists($data['SoDienThoaiKhachHang'])) {
                echo "<script>alert('Số điện thoại đã được đăng ký!'); window.history.back();</script>";
                return;
            }
            
            if ($model->createGuest($data)) {
                echo "<script>alert('Đăng ký thành công! Vui lòng đăng nhập.'); window.location.href='?controller=AuthController&action=login';</script>";
            } else {
                echo "<script>alert('Đăng ký thất bại!'); window.history.back();</script>";
            }
        }
    }
    
    // Trang chủ khách hàng
    public function home() {
        session_start();
        if (!isset($_SESSION['guest_id'])) {
            header("Location: ?controller=AuthController&action=login");
            exit();
        }
        
        $model = $this->model("GuestModel");
        $roomTypeModel = $this->model("RoomTypeModel");
        
        $guest = $model->getGuestById($_SESSION['guest_id']);
        $roomTypes = $roomTypeModel->getAllWithAvailability();
        
        ob_start();
        $this->view("Pages/GuestHome", [
            "guest" => $guest,
            "roomTypes" => $roomTypes
        ]);
        $content = ob_get_clean();
        $this->view("Master", ["content" => $content]);
    }
    
    // Hiển thị danh sách khách hàng (cho admin/nhân viên)
    public function index() {
        $model = $this->model("GuestModel");
        
        // Xử lý tìm kiếm
        if (isset($_POST['search'])) {
            $guests = $model->search($_POST['keyword']);
        } else {
            $guests = $model->getAll();
        }
        
        ob_start();
        $this->view("Pages/GuestList", ["guests" => $guests]);
        $content = ob_get_clean();
        $this->view("Master", ["content" => $content, "page_tab" => "guest"]);
    }
}
?>