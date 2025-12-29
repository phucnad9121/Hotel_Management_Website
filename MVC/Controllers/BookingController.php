<?php
class BookingController extends controller {
    
    // Hiển thị form đặt phòng
    public function create() {
        session_start();
        if (!isset($_SESSION['guest_id'])) {
            header("Location: ?controller=AuthController&action=login");
            exit();
        }
        
        $guestModel = $this->model("GuestModel");
        $roomTypeModel = $this->model("RoomTypeModel");
        
        $guest = $guestModel->getGuestById($_SESSION['guest_id']);
        $roomTypes = $roomTypeModel->getAllWithAvailability();
        
        ob_start();
        $this->view("Pages/BookingCreate", [
            "guest" => $guest,
            "roomTypes" => $roomTypes
        ]);
        $content = ob_get_clean();
        $this->view("Master", ["content" => $content]);
    }
    
    // Xử lý tạo booking
    public function handleCreate() {
        session_start();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $model = $this->model("BookingModel");
            $roomTypeModel = $this->model("RoomTypeModel");
            
            $maLoaiPhong = $_POST['ma_loai_phong'];
            
            // Kiểm tra số phòng còn trống
            $availableRooms = $roomTypeModel->countAvailableRooms($maLoaiPhong);
            if ($availableRooms <= 0) {
                echo "<script>alert('Loại phòng này đã hết! Vui lòng chọn loại khác.'); window.history.back();</script>";
                return;
            }
            
            $data = [
                'NgayDatPhong' => date('Y-m-d'),
                'NgayNhanPhong' => $_POST['ngay_nhan'],
                'NgayTraPhong' => $_POST['ngay_tra'],
                'MaKhachHang' => $_SESSION['guest_id'],
                'GhiChu' => "ROOMTYPE:$maLoaiPhong|" . ($_POST['ghi_chu'] ?? '')
            ];
            
            // Tính số ngày và tiền
            $date1 = new DateTime($data['NgayNhanPhong']);
            $date2 = new DateTime($data['NgayTraPhong']);
            $diff = $date1->diff($date2);
            $soNgay = $diff->days;
            
            $roomType = $roomTypeModel->getById($maLoaiPhong);
            $data['ThoiGianLuuTru'] = $soNgay;
            $data['SoTienDatPhong'] = $soNgay * $roomType['GiaPhong'];
            
            if ($model->createBooking($data)) {
                echo "<script>alert('Gửi yêu cầu đặt phòng thành công! Vui lòng chờ xác nhận.'); window.location.href='?controller=GuestController&action=home';</script>";
            } else {
                echo "<script>alert('Đặt phòng thất bại!'); window.history.back();</script>";
            }
        }
    }
    
    // Quản lý đặt phòng (cho admin/nhân viên)
    public function index() {
        $model = $this->model("BookingModel");
        
        // Xử lý tìm kiếm
        if (isset($_POST['search'])) {
            $bookings = $model->search($_POST['keyword']);
        } else {
            $bookings = $model->getAll();
        }

        
        ob_start();
        $this->view("Pages/BookingManage", ["bookings" => $bookings]);
        $content = ob_get_clean();
        $this->view("Master", ["content" => $content, "page_tab" => "booking"]);
    }
    
    // Xác nhận và gán phòng
    public function confirm() {
        if (isset($_POST['ma_dat_phong']) && isset($_POST['ma_phong'])) {
            $bookingModel = $this->model("BookingModel");
            $roomModel = $this->model("RoomModel");
            
            $maDatPhong = $_POST['ma_dat_phong'];
            $maPhong = $_POST['ma_phong'];
            
            // Gán phòng vào bảng rooms_roombooked
            $bookingModel->assignRoom($maDatPhong, $maPhong);
            
            // Cập nhật trạng thái booking
            if ($bookingModel->updateStatus($maDatPhong, 'Confirmed')) {
                // Cập nhật trạng thái phòng
                $roomModel->updateAvailability($maPhong, 'No');
                echo "<script>alert('Xác nhận và gán phòng thành công!'); window.location.href='?controller=BookingController&action=index';</script>";
            }
        }
    }
    
    // Check-in
    public function checkin() {
        if (isset($_GET['id'])) {
            $model = $this->model("BookingModel");
            $guestModel = $this->model("GuestModel");
            
            $maDatPhong = $_GET['id'];
            $booking = $model->getById($maDatPhong);
            
            if ($model->updateStatus($maDatPhong, 'Checkin')) {
                // Cập nhật trạng thái khách hàng
                $guestModel->updateStatus($booking['MaKhachHang'], 'Reserved');
                echo "<script>alert('Check-in thành công!'); window.location.href='?controller=BookingController&action=index';</script>";
            }
        }
    }
    
    // Hủy đặt phòng
    public function cancel() {
        if (isset($_GET['id'])) {
            $model = $this->model("BookingModel");
            $roomModel = $this->model("RoomModel");
            
            $maDatPhong = $_GET['id'];
            $booking = $model->getById($maDatPhong);
            
            // Lấy danh sách phòng đã gán
            $rooms = $model->getAssignedRooms($maDatPhong);
            
            if ($model->updateStatus($maDatPhong, 'Cancelled')) {
                // Chuyển phòng về trạng thái trống
                foreach ($rooms as $room) {
                    $roomModel->updateAvailability($room['MaPhong'], 'Yes');
                }
                echo "<script>alert('Hủy đặt phòng thành công!'); window.location.href='?controller=BookingController&action=index';</script>";
            }
        }
    }
    
    // API lấy danh sách phòng trống (dùng cho AJAX)
    public function getAvailableRooms() {
        header('Content-Type: application/json');
        
        if (isset($_GET['type'])) {
            $roomModel = $this->model("RoomModel");
            $rooms = $roomModel->getAvailableByType($_GET['type']);
            echo json_encode($rooms);
        } else {
            echo json_encode([]);
        }
        exit();
    }
}
?>