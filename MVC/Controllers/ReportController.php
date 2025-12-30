<?php
class ReportController extends controller {
    
    // Trang báo cáo tổng hợp
    public function index() {
        $bookingModel = $this->model("BookingModel");
        $serviceModel = $this->model("ServiceModel");
        
        // Lấy danh sách booking (có thể lọc theo ngày)
        $fromDate = $_POST['from_date'] ?? date('Y-m-01');
        $toDate = $_POST['to_date'] ?? date('Y-m-d');
        
        $bookings = $this->getBookingsWithServices($fromDate, $toDate);
        $servicesUsed = $this->getServicesUsedInRange($fromDate, $toDate);
        
        // Thống kê tổng quan
        $stats = $this->calculateStats($bookings);
        
        ob_start();
        $this->view("Pages/ReportManage", [
            "bookings" => $bookings,
            "servicesUsed" => $servicesUsed,
            "stats" => $stats,
            "fromDate" => $fromDate,
            "toDate" => $toDate
        ]);
        $content = ob_get_clean();
        $this->view("Master", ["content" => $content, "page_tab" => "report"]);
    }

    // Trả về HTML danh sách dịch vụ mà một khách hàng đã sử dụng trong khoảng ngày
    public function getCustomerServices() {
        if (!isset($_GET['id'])) return;

        $maKhach = intval($_GET['id']);
        $fromDate = $_GET['from_date'] ?? date('Y-m-01');
        $toDate = $_GET['to_date'] ?? date('Y-m-d');

        $db = new connectDB();
        $sql = "SELECT b.MaDatPhong, su.MaDichVu, s.TenDichVu, su.SoLuong, su.DonGia, su.ThanhTien, su.NgaySuDung
                FROM bookings_booking b
                JOIN hotelservice_servicesused su ON su.MaDatPhong = b.MaDatPhong
                JOIN hotelservice_services s ON su.MaDichVu = s.MaDichVu
                WHERE b.MaKhachHang = $maKhach
                  AND DATE(su.NgaySuDung) BETWEEN '$fromDate' AND '$toDate'
                ORDER BY su.NgaySuDung DESC";

        $rows = $db->select($sql);

        if (!empty($rows)) {
            echo '<div style="color: white;">';
            echo '<table style="width:100%; border-collapse: collapse;">';
            echo '<tr style="background: rgba(56, 189, 248, 0.06);">'
                 . '<th style="padding:8px; text-align:left;">Mã ĐP</th>'
                 . '<th style="padding:8px; text-align:left;">Tên dịch vụ</th>'
                 . '<th style="padding:8px; text-align:right;">Số lượng</th>'
                 . '<th style="padding:8px; text-align:right;">Đơn giá</th>'
                 . '<th style="padding:8px; text-align:right;">Thành tiền</th>'
                 . '<th style="padding:8px; text-align:center;">Ngày sử dụng</th>'
                 . '</tr>';
            foreach ($rows as $r) {
                echo '<tr style="border-bottom:1px solid var(--border-color);">';
                echo '<td style="padding:8px;">#' . $r['MaDatPhong'] . '</td>';
                echo '<td style="padding:8px;">' . $r['TenDichVu'] . '</td>';
                echo '<td style="padding:8px; text-align:right;">' . ($r['SoLuong'] ?? 1) . '</td>';
                echo '<td style="padding:8px; text-align:right;">' . number_format($r['DonGia']) . ' đ</td>';
                echo '<td style="padding:8px; text-align:right; color:#10b981; font-weight:bold;">' . number_format($r['ThanhTien']) . ' đ</td>';
                echo '<td style="padding:8px; text-align:center;">' . date('d/m/Y', strtotime($r['NgaySuDung'])) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
            echo '</div>';
        } else {
            echo '<p style="color: var(--text-muted);">Khách hàng chưa sử dụng dịch vụ trong khoảng thời gian này.</p>';
        }

        exit();
    }

    // Lấy tất cả dịch vụ đã sử dụng trong khoảng ngày (lọc theo ngày sử dụng)
    private function getServicesUsedInRange($fromDate, $toDate) {
        $db = new connectDB();
        $sql = "SELECT su.MaDatPhong, su.MaDichVu, s.TenDichVu, su.SoLuong, su.DonGia, su.ThanhTien, su.NgaySuDung
                FROM hotelservice_servicesused su
                JOIN hotelservice_services s ON su.MaDichVu = s.MaDichVu
                WHERE DATE(su.NgaySuDung) BETWEEN '$fromDate' AND '$toDate'
                ORDER BY su.NgaySuDung DESC";
        return $db->select($sql);
    }
    
    // Lấy booking kèm dịch vụ và tổng tiền
    private function getBookingsWithServices($fromDate, $toDate) {
        $db = new connectDB();
        
        // SỬA LẠI QUERY CHO ĐÚNG VỚI CẤU TRÚC BẢNG CÓ SỐ LƯỢNG
        $sql = "SELECT b.*, 
                CONCAT(g.HoKhachHang, ' ', g.TenKhachHang) as TenKhachHang,
                g.SoDienThoaiKhachHang,
                (SELECT GROUP_CONCAT(r.SoPhong SEPARATOR ', ') 
                 FROM rooms_roombooked rb 
                 JOIN rooms_room r ON rb.MaPhong = r.MaPhong 
                 WHERE rb.MaDatPhong = b.MaDatPhong) as SoPhong,
                (SELECT SUM(ThanhTien) 
                 FROM hotelservice_servicesused su 
                 WHERE su.MaDatPhong = b.MaDatPhong) as TongTienDichVu,
                (b.SoTienDatPhong + COALESCE((SELECT SUM(ThanhTien) 
                 FROM hotelservice_servicesused su 
                 WHERE su.MaDatPhong = b.MaDatPhong), 0)) as TongTien
                FROM bookings_booking b
                JOIN hotels_guests g ON b.MaKhachHang = g.MaKhachHang
                WHERE DATE(b.NgayNhanPhong) BETWEEN '$fromDate' AND '$toDate'
                ORDER BY b.NgayTao DESC";
        
        return $db->select($sql);
    }
    
    // Tính toán thống kê
    private function calculateStats($bookings) {
        $stats = [
            'totalBookings' => count($bookings),
            'totalRevenue' => 0,
            'totalRoomRevenue' => 0,
            'totalServiceRevenue' => 0,
            'avgRevenue' => 0,
            'statusBreakdown' => [
                'Pending' => 0,
                'Confirmed' => 0,
                'Checkin' => 0,
                'Checkout' => 0,
                'Cancelled' => 0
            ]
        ];
        
        foreach ($bookings as $booking) {
            $stats['totalRoomRevenue'] += $booking['SoTienDatPhong'];
            $stats['totalServiceRevenue'] += $booking['TongTienDichVu'] ?? 0;
            $stats['totalRevenue'] += $booking['TongTien'];
            
            if (isset($stats['statusBreakdown'][$booking['TrangThai']])) {
                $stats['statusBreakdown'][$booking['TrangThai']]++;
            }
        }
        
        if ($stats['totalBookings'] > 0) {
            $stats['avgRevenue'] = $stats['totalRevenue'] / $stats['totalBookings'];
        }
        
        return $stats;
    }
    
    // Xuất báo cáo Excel
    public function exportReport() {
        $fromDate = $_GET['from_date'] ?? date('Y-m-01');
        $toDate = $_GET['to_date'] ?? date('Y-m-d');
        
        $bookings = $this->getBookingsWithServices($fromDate, $toDate);
        $stats = $this->calculateStats($bookings);
        
        $filename = "Bao_Cao_Khach_San_" . date('Ymd') . ".xls";
        
        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo "\xEF\xBB\xBF";
        
        echo '<table border="1">';
        
        // Header thống kê
        echo '<tr style="background-color: #38bdf8; color: #ffffff; font-weight: bold;">
                <th colspan="9" style="text-align: center; padding: 10px;">BÁO CÁO DOANH THU KHÁCH SẠN</th>
              </tr>';
        echo '<tr>
                <th colspan="3">Từ ngày: ' . date('d/m/Y', strtotime($fromDate)) . '</th>
                <th colspan="3">Đến ngày: ' . date('d/m/Y', strtotime($toDate)) . '</th>
                <th colspan="3">Ngày xuất: ' . date('d/m/Y') . '</th>
              </tr>';
        echo '<tr style="background-color: #e0f2fe;">
                <th colspan="3">Tổng Booking: ' . $stats['totalBookings'] . '</th>
                <th colspan="3">Doanh thu phòng: ' . number_format($stats['totalRoomRevenue']) . ' đ</th>
                <th colspan="3">Doanh thu dịch vụ: ' . number_format($stats['totalServiceRevenue']) . ' đ</th>
              </tr>';
        echo '<tr style="background-color: #bfdbfe;">
                <th colspan="9" style="text-align: center;">TỔNG DOANH THU: ' . number_format($stats['totalRevenue']) . ' VNĐ</th>
              </tr>';
        
        // Header bảng chi tiết
        echo '<tr style="background-color: #38bdf8; color: #ffffff; font-weight: bold;">
                <th>Mã Booking</th>
                <th>Khách hàng</th>
                <th>SĐT</th>
                <th>Phòng</th>
                <th>Ngày nhận</th>
                <th>Ngày trả</th>
                <th>Tiền phòng</th>
                <th>Tiền DV</th>
                <th>Tổng tiền</th>
              </tr>';
        
        // Dữ liệu
        if (!empty($bookings)) {
            foreach ($bookings as $row) {
                echo '<tr>';
                echo '<td>' . $row['MaDatPhong'] . '</td>';
                echo '<td>' . $row['TenKhachHang'] . '</td>';
                echo '<td>' . $row['SoDienThoaiKhachHang'] . '</td>';
                echo '<td>' . ($row['SoPhong'] ?? 'Chưa gán') . '</td>';
                echo '<td>' . date('d/m/Y', strtotime($row['NgayNhanPhong'])) . '</td>';
                echo '<td>' . date('d/m/Y', strtotime($row['NgayTraPhong'])) . '</td>';
                echo '<td>' . number_format($row['SoTienDatPhong']) . '</td>';
                echo '<td>' . number_format($row['TongTienDichVu'] ?? 0) . '</td>';
                echo '<td style="font-weight: bold;">' . number_format($row['TongTien']) . '</td>';
                echo '</tr>';
            }
        }
        
        echo '</table>';

        // Thêm bảng dịch vụ đã sử dụng vào file Excel
        $servicesUsed = $this->getServicesUsedInRange($fromDate, $toDate);

        echo '<br>';
        echo '<table border="1">';
        echo '<tr style="background-color: #38bdf8; color: #ffffff; font-weight: bold;">'
             . '<th colspan="6" style="text-align: center; padding: 10px;">DANH SÁCH DỊCH VỤ ĐÃ SỬ DỤNG</th>'
             . '</tr>';
        echo '<tr style="background-color: #38bdf8; color: #ffffff; font-weight: bold;">'
             . '<th>Mã Booking</th>'
             . '<th>Tên dịch vụ</th>'
             . '<th>Số lượng</th>'
             . '<th>Đơn giá</th>'
             . '<th>Thành tiền</th>'
             . '<th>Ngày sử dụng</th>'
             . '</tr>';

        if (!empty($servicesUsed)) {
            foreach ($servicesUsed as $svc) {
                echo '<tr>';
                echo '<td>' . $svc['MaDatPhong'] . '</td>';
                echo '<td>' . $svc['TenDichVu'] . '</td>';
                echo '<td>' . ($svc['SoLuong'] ?? 1) . '</td>';
                echo '<td>' . number_format($svc['DonGia']) . '</td>';
                echo '<td>' . number_format($svc['ThanhTien']) . '</td>';
                echo '<td>' . date('d/m/Y', strtotime($svc['NgaySuDung'])) . '</td>';
                echo '</tr>';
            }
        } else {
            echo '<tr><td colspan="6">Không có dịch vụ nào trong khoảng thời gian này.</td></tr>';
        }

        echo '</table>';
        exit();
    }
    
    // Chi tiết booking
    public function getBookingDetail() {
        if (isset($_GET['id'])) {
            $bookingModel = $this->model("BookingModel");
            $serviceModel = $this->model("ServiceModel");
            
            $booking = $bookingModel->getById($_GET['id']);
            $services = $serviceModel->getServicesByBooking($_GET['id']);
            $assignedRooms = $bookingModel->getAssignedRooms($_GET['id']);
            
            $totalService = 0;
            foreach ($services as $s) {
                $totalService += $s['ThanhTien'];
            }
            
            // Render HTML
            echo '<div style="color: white;">';
            echo '<h4 style="color: var(--ocean-blue); border-bottom: 2px solid var(--ocean-blue); padding-bottom: 10px;">Thông tin booking</h4>';
            echo '<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 20px 0;">';
            echo '<div><strong>Mã booking:</strong> #' . $booking['MaDatPhong'] . '</div>';
            echo '<div><strong>Ngày đặt:</strong> ' . date('d/m/Y', strtotime($booking['NgayTao'])) . '</div>';
            echo '<div><strong>Ngày nhận:</strong> ' . date('d/m/Y', strtotime($booking['NgayNhanPhong'])) . '</div>';
            echo '<div><strong>Ngày trả:</strong> ' . date('d/m/Y', strtotime($booking['NgayTraPhong'])) . '</div>';
            echo '<div><strong>Số đêm:</strong> ' . $booking['ThoiGianLuuTru'] . ' đêm</div>';
            echo '<div><strong>Trạng thái:</strong> ' . $booking['TrangThai'] . '</div>';
            echo '</div>';
            
            echo '<h4 style="color: var(--ocean-blue); border-bottom: 2px solid var(--ocean-blue); padding-bottom: 10px; margin-top: 30px;">Phòng đã gán</h4>';
            if (!empty($assignedRooms)) {
                echo '<div style="margin: 15px 0;">';
                foreach ($assignedRooms as $room) {
                    echo '<span style="display: inline-block; background: var(--ocean-blue); padding: 8px 15px; margin: 5px; border-radius: 5px;">Phòng ' . $room['SoPhong'] . '</span>';
                }
                echo '</div>';
            } else {
                echo '<p style="color: var(--text-muted);">Chưa gán phòng</p>';
            }
            
            echo '<h4 style="color: var(--ocean-blue); border-bottom: 2px solid var(--ocean-blue); padding-bottom: 10px; margin-top: 30px;">Dịch vụ đã sử dụng</h4>';
            if (!empty($services)) {
                echo '<table style="width: 100%; border-collapse: collapse; margin: 15px 0;">';
                echo '<tr style="background: rgba(56, 189, 248, 0.1);"><th style="padding: 10px; text-align: left;">Tên dịch vụ</th><th style="padding: 10px; text-align: right;">Số lượng</th><th style="padding: 10px; text-align: right;">Đơn giá</th><th style="padding: 10px; text-align: right;">Thành tiền</th></tr>';
                foreach ($services as $s) {
                    echo '<tr style="border-bottom: 1px solid var(--border-color);">';
                    echo '<td style="padding: 10px;">' . $s['TenDichVu'] . '</td>';
                    echo '<td style="padding: 10px; text-align: right;">' . ($s['SoLuong'] ?? 1) . '</td>';
                    echo '<td style="padding: 10px; text-align: right;">' . number_format($s['DonGia'] ?? $s['ChiPhiDichVu']) . ' đ</td>';
                    echo '<td style="padding: 10px; text-align: right; color: #10b981; font-weight: bold;">' . number_format($s['ThanhTien']) . ' đ</td>';
                    echo '</tr>';
                }
                echo '</table>';
            } else {
                echo '<p style="color: var(--text-muted);">Chưa sử dụng dịch vụ nào</p>';
            }
            
            echo '<div style="background: linear-gradient(135deg, var(--ocean-blue), #0369a1); padding: 20px; border-radius: 10px; margin-top: 30px; text-align: right;">';
            echo '<div style="margin: 10px 0;"><span>Tiền phòng:</span> <strong style="font-size: 1.2rem; margin-left: 20px;">' . number_format($booking['SoTienDatPhong']) . ' đ</strong></div>';
            echo '<div style="margin: 10px 0;"><span>Tiền dịch vụ:</span> <strong style="font-size: 1.2rem; margin-left: 20px;">' . number_format($totalService) . ' đ</strong></div>';
            echo '<div style="border-top: 2px solid rgba(255,255,255,0.3); padding-top: 15px; margin-top: 15px;"><span style="font-size: 1.2rem;">TỔNG CỘNG:</span> <strong style="font-size: 2rem; margin-left: 20px;">' . number_format($booking['SoTienDatPhong'] + $totalService) . ' đ</strong></div>';
            echo '</div>';
            
            echo '</div>';
            exit();
        }
    }
}
?>