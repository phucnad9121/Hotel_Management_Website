<?php
class BookingModel extends connectDB {
    
   // 1. Tạo booking mới (GIỮ NGUYÊN)
    public function createBooking($data) {
        $sql = "INSERT INTO bookings_booking 
                (NgayDatPhong, ThoiGianLuuTru, NgayNhanPhong, NgayTraPhong, 
                SoTienDatPhong, MaKhachHang, GhiChu) 
                VALUES ('{$data['NgayDatPhong']}', {$data['ThoiGianLuuTru']}, 
                '{$data['NgayNhanPhong']}', '{$data['NgayTraPhong']}', 
                {$data['SoTienDatPhong']}, {$data['MaKhachHang']}, '{$data['GhiChu']}')";
        return $this->execute($sql);
    }
    
    // 2. Lấy tất cả booking (GIỮ NGUYÊN)
    public function getAll() {
        $sql = "SELECT b.*, 
                g.HoKhachHang, 
                g.TenKhachHang,
                g.SoDienThoaiKhachHang,
                CONCAT(e.HoNhanVien, ' ', e.TenNhanVien) as TenNhanVien
                FROM bookings_booking b
                JOIN hotels_guests g ON b.MaKhachHang = g.MaKhachHang
                LEFT JOIN hotels_employees e ON b.MaNhanVien = e.MaNhanVien
                ORDER BY b.NgayTao DESC";
        return $this->select($sql);
    }
    
    // 3. Lấy booking theo ID (GIỮ NGUYÊN)
    public function getById($id) {
        $sql = "SELECT * FROM bookings_booking WHERE MaDatPhong = $id";
        return $this->selectOne($sql);
    }
    
    // 4. Tìm kiếm booking (GIỮ NGUYÊN)
    public function search($keyword) {
        $sql = "SELECT b.*, 
                g.HoKhachHang, 
                g.TenKhachHang,
                g.SoDienThoaiKhachHang
                FROM bookings_booking b
                JOIN hotels_guests g ON b.MaKhachHang = g.MaKhachHang
                WHERE b.MaDatPhong LIKE '%$keyword%' 
                OR g.TenKhachHang LIKE '%$keyword%' 
                OR g.HoKhachHang LIKE '%$keyword%'
                OR g.SoDienThoaiKhachHang LIKE '%$keyword%'
                ORDER BY b.NgayTao DESC";
        return $this->select($sql);
    }
    
    // 5. Cập nhật trạng thái booking (GIỮ NGUYÊN)
    public function updateStatus($id, $status) {
        $sql = "UPDATE bookings_booking SET TrangThai = '$status' WHERE MaDatPhong = $id";
        return $this->execute($sql);
    }
    
    // 6. Gán phòng cho booking (GIỮ NGUYÊN)
    public function assignRoom($maDatPhong, $maPhong) {
        $sql = "INSERT INTO rooms_roombooked (MaDatPhong, MaPhong) 
                VALUES ($maDatPhong, '$maPhong')";
        return $this->execute($sql);
    }
    
    // 7. Lấy danh sách phòng đã gán (GIỮ NGUYÊN)
    public function getAssignedRooms($maDatPhong) {
        $sql = "SELECT rb.*, r.SoPhong 
                FROM rooms_roombooked rb
                JOIN rooms_room r ON rb.MaPhong = r.MaPhong
                WHERE rb.MaDatPhong = $maDatPhong";
        return $this->select($sql);
    }
    
    // 8. Hàm phụ trợ tách chuỗi loại phòng (GIỮ NGUYÊN)
    public function parseRoomType($ghiChu) {
        if (strpos($ghiChu, 'ROOMTYPE:') === 0) {
            $parts = explode('|', $ghiChu, 2);
            $roomTypePart = str_replace('ROOMTYPE:', '', $parts[0]);
            return $roomTypePart;
        }
        return null;
    }

    // 9. Kiểm tra booking tồn tại (GIỮ NGUYÊN)
    public function checkBookingExist($maKhachHang) {
        $sql = "SELECT COUNT(*) as total FROM bookings_booking WHERE MaKhachHang = '$maKhachHang'";
        $result = $this->selectOne($sql);
        return $result['total'] > 0;
    }

    // 10. [MỚI - QUAN TRỌNG] Lấy danh sách phòng trống để gán
    public function getAvailableRooms($type) {
        // Lấy tất cả phòng đang Khả dụng
        $sql = "SELECT r.* FROM rooms_room r WHERE r.KhaDung = 'Yes'";

        // Nếu có lọc theo loại phòng thì thêm điều kiện
        if (!empty($type)) {
            $type = mysqli_real_escape_string($this->con, $type);
            $sql .= " AND r.MaLoaiPhong = '$type'";
        }

        // CHỈ LOẠI BỎ những phòng đang có khách ở (Confirmed hoặc Checkin)
        // Các phòng Checkout/Cancelled/Pending vẫn hiện ra để gán lại được
        $sql .= " AND r.MaPhong NOT IN (
                    SELECT rb.MaPhong 
                    FROM rooms_roombooked rb
                    JOIN bookings_booking bb ON rb.MaDatPhong = bb.MaDatPhong
                    WHERE bb.TrangThai IN ('Confirmed', 'Checkin')
                  )";

        $sql .= " ORDER BY r.SoPhong ASC";
        return $this->select($sql);
    }
}
?>