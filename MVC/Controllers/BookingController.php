<?php
class BookingController extends controller {
    
    public function __construct() {
        // Các action dành cho khách (như create) thì không cần check hoặc check riêng
        // Nhưng nếu muốn bảo vệ trang quản lý index:
        
        // Lấy action hiện tại từ URL để loại trừ các trang public (nếu cần)
        $action = $_GET['action'] ?? 'index';
        
        // Ví dụ: Action 'create' là khách đặt phòng thì không check role admin/employee
        if ($action != 'create' && $action != 'handleCreate') {
             $this->requireRole(['admin', 'employee']);
        }
    }


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
            
            // Kiểm tra phòng trống... (giữ nguyên code cũ)
            $availableRooms = $roomTypeModel->countAvailableRooms($maLoaiPhong);
            if ($availableRooms <= 0) {
                echo "<script>alert('Loại phòng này đã hết!'); window.history.back();</script>";
                return;
            }
            
            // Tính toán
            $date1 = new DateTime($_POST['ngay_nhan']);
            $date2 = new DateTime($_POST['ngay_tra']);
            $diff = $date1->diff($date2);
            $soNgay = $diff->days;
            
            $roomType = $roomTypeModel->getById($maLoaiPhong);
            $tongTien = $soNgay * $roomType['GiaPhong'];
            $tienCoc = $tongTien * 0.5; // Tính cọc để hiển thị/ghi chú
            
            // Tạo nội dung ghi chú tự động
            $ghiChuKhach = $_POST['ghi_chu'] ?? '';
            $ghiChuHeThong = "ROOMTYPE:$maLoaiPhong | TỔNG: ".number_format($tongTien)." | ĐÃ CỌC 50%: ".number_format($tienCoc);
            
            $data = [
                'NgayDatPhong' => date('Y-m-d'),
                'NgayNhanPhong' => $_POST['ngay_nhan'],
                'NgayTraPhong' => $_POST['ngay_tra'],
                'MaKhachHang' => $_SESSION['guest_id'],
                // Nối ghi chú của khách và ghi chú hệ thống về tiền cọc
                'GhiChu' => $ghiChuHeThong . " | Note: " . $ghiChuKhach,
                'ThoiGianLuuTru' => $soNgay,
                'SoTienDatPhong' => $tongTien // VẪN LƯU TỔNG TIỀN để thống kê đúng
            ];
            
            if ($model->createBooking($data)) {
                // Thông báo kiểu "Giả lập thanh toán thành công"
                $msg = "Đặt phòng thành công! \\n";
                $msg .= "Hệ thống ghi nhận bạn đã cọc: " . number_format($tienCoc) . " VNĐ (50%). \\n";
                $msg .= "Số tiền còn lại cần thanh toán tại quầy: " . number_format($tongTien - $tienCoc) . " VNĐ.";
                
                echo "<script>alert('$msg'); window.location.href='?controller=GuestController&action=home';</script>";
            } else {
                echo "<script>alert('Lỗi hệ thống!'); window.history.back();</script>";
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

    public function addServiceAdmin() {
        if (!isset($_GET['booking_id'])) {
            echo "<script>alert('Không tìm thấy booking!'); window.history.back();</script>";
            return;
        }

        $bookingModel = $this->model("BookingModel");
        $serviceModel = $this->model("ServiceModel");
        $guestModel = $this->model("GuestModel");

        $maDatPhong = $_GET['booking_id'];
        $booking = $bookingModel->getById($maDatPhong);
        
        // Lấy thông tin khách hàng gắn liền với booking đó
        $guest = $guestModel->getGuestById($booking['MaKhachHang']);
        $services = $serviceModel->getAll();
        
        $usedServices = $serviceModel->getServicesByBooking($maDatPhong);

        ob_start();
        // Chúng ta sẽ tạo một View mới tên là BookingAddService.php (copy từ GuestServiceOrder)
        $this->view("Pages/BookingAddService", [
            "booking" => array_merge($booking, $guest),
            "services" => $services,
            "usedServices" => $usedServices
        ]);
        $content = ob_get_clean();
        $this->view("Master", ["content" => $content, "page_tab" => "booking"]);
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

    public function exportExcel() {
        // 1. Lấy dữ liệu
        $model = $this->model("BookingModel");
        $bookings = $model->getAll(); // Lấy toàn bộ danh sách
        
        // 2. Thiết lập Header để tải file Excel
        $filename = "Danh_Sach_Dat_Phong_" . date('Y-m-d') . ".xls";
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache"); 
        header("Expires: 0");

        // 3. Xuất dữ liệu dưới dạng bảng HTML (Excel đọc tốt định dạng này)
        // Lưu ý: Dùng <meta charset='utf-8'> để không bị lỗi phông tiếng Việt
        echo "
        <html xmlns:x='urn:schemas-microsoft-com:office:excel'>
        <head>
            <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
            <style>
                table { border-collapse: collapse; width: 100%; }
                th, td { border: 1px solid #000; padding: 5px; text-align: left; }
                th { background-color: #f2f2f2; font-weight: bold; }
                .text-center { text-align: center; }
                .text-right { text-align: right; }
            </style>
        </head>
        <body>
            <h2 style='text-align: center'>DANH SÁCH ĐẶT PHÒNG KHÁCH SẠN</h2>
            <table>
                <thead>
                    <tr>
                        <th>Mã ĐP</th>
                        <th>Họ Tên Khách</th>
                        <th>SĐT</th>
                        <th>Ngày Đặt</th>
                        <th>Ngày Nhận</th>
                        <th>Ngày Trả</th>
                        <th>Số Đêm</th>
                        <th>Tổng Tiền</th>
                        <th>Trạng Thái</th>
                        <th>Ghi Chú</th>
                    </tr>
                </thead>
                <tbody>";
        
        if (!empty($bookings)) {
            foreach ($bookings as $row) {
                // Xử lý hiển thị ngày tháng
                $ngayDat = date('d/m/Y', strtotime($row['NgayDatPhong']));
                $ngayNhan = date('d/m/Y', strtotime($row['NgayNhanPhong']));
                $ngayTra = date('d/m/Y', strtotime($row['NgayTraPhong']));
                $tien = number_format($row['SoTienDatPhong']);
                
                // Trạng thái (Dịch sang tiếng Việt cho đẹp nếu cần)
                $statusMap = [
                    'Pending' => 'Chờ xác nhận',
                    'Confirmed' => 'Đã xác nhận',
                    'Checkin' => 'Đang ở',
                    'Checkout' => 'Đã trả phòng',
                    'Cancelled' => 'Đã hủy'
                ];
                $trangThai = $statusMap[$row['TrangThai']] ?? $row['TrangThai'];

                echo "<tr>
                        <td class='text-center'>#{$row['MaDatPhong']}</td>
                        <td>{$row['HoKhachHang']} {$row['TenKhachHang']}</td>
                        <td class='text-center'>'{$row['SoDienThoaiKhachHang']}</td> <td class='text-center'>$ngayDat</td>
                        <td class='text-center'>$ngayNhan</td>
                        <td class='text-center'>$ngayTra</td>
                        <td class='text-center'>{$row['ThoiGianLuuTru']}</td>
                        <td class='text-right'>{$tien} đ</td>
                        <td>$trangThai</td>
                        <td>{$row['GhiChu']}</td>
                    </tr>";
            }
        }

        echo "  </tbody>
            </table>
        </body>
        </html>";
        exit(); // Dừng script để không in thêm nội dung thừa
    }
    
    // API lấy danh sách phòng trống (ĐÃ SỬA ĐỂ GỌI ĐÚNG BOOKING MODEL)
    public function getAvailableRooms() {
        header('Content-Type: application/json');
        
        // Gọi BookingModel vì ta vừa viết hàm getAvailableRooms ở đó
        $bookingModel = $this->model("BookingModel");
        
        // Lấy type từ URL, nếu không có thì để rỗng (để lấy tất cả)
        $type = isset($_GET['type']) ? $_GET['type'] : '';

        // Gọi hàm getAvailableRooms trong BookingModel
        $rooms = $bookingModel->getAvailableRooms($type);
        
        echo json_encode($rooms);
        exit();
    }
}
?>