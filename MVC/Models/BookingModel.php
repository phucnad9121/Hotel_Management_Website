<?php
class BookingModel extends connectDB {
    
    // 1. Tạo booking mới
    public function createBooking($data) {
        // Lưu ý: MaKhachHang là số (INT) nên không cần dấu nháy đơn ''
        $sql = "INSERT INTO bookings_booking 
                (NgayDatPhong, ThoiGianLuuTru, NgayNhanPhong, NgayTraPhong, 
                SoTienDatPhong, MaKhachHang, GhiChu) 
                VALUES ('{$data['NgayDatPhong']}', {$data['ThoiGianLuuTru']}, 
                '{$data['NgayNhanPhong']}', '{$data['NgayTraPhong']}', 
                {$data['SoTienDatPhong']}, {$data['MaKhachHang']}, '{$data['GhiChu']}')";
        return $this->execute($sql);
    }
    
    // 2. Lấy tất cả booking (SỬA LẠI ĐỂ KHỚP VIEW)
    // TÌM HÀM getAll() CŨ VÀ XÓA ĐI, THAY BẰNG ĐOẠN NÀY:
    public function getAll() {
        // Sửa lỗi: Tách riêng HoKhachHang và TenKhachHang thay vì dùng CONCAT
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
    
    // 3. Lấy booking theo ID
    public function getById($id) {
        $sql = "SELECT * FROM bookings_booking WHERE MaDatPhong = $id";
        return $this->selectOne($sql);
    }
    
    // 4. Tìm kiếm booking (SỬA LẠI ĐỂ KHỚP VIEW)
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
    
    // 5. Cập nhật trạng thái booking
    public function updateStatus($id, $status) {
        // Trong SQL TrangThai là ENUM, giá trị phải khớp chính xác (Pending, Confirmed...)
        $sql = "UPDATE bookings_booking SET TrangThai = '$status' WHERE MaDatPhong = $id";
        return $this->execute($sql);
    }
    
    // 6. Gán phòng cho booking
    public function assignRoom($maDatPhong, $maPhong) {
        // Bảng rooms_roombooked của bạn có cột MaPhongDaDat tự tăng, không cần insert
        $sql = "INSERT INTO rooms_roombooked (MaDatPhong, MaPhong) 
                VALUES ($maDatPhong, '$maPhong')";
        return $this->execute($sql);
    }
    
    // 7. Lấy danh sách phòng đã gán
    public function getAssignedRooms($maDatPhong) {
        $sql = "SELECT rb.*, r.SoPhong 
                FROM rooms_roombooked rb
                JOIN rooms_room r ON rb.MaPhong = r.MaPhong
                WHERE rb.MaDatPhong = $maDatPhong";
        return $this->select($sql);
    }
    
    // 8. Hàm phụ trợ tách chuỗi loại phòng
    public function parseRoomType($ghiChu) {
        if (strpos($ghiChu, 'ROOMTYPE:') === 0) {
            $parts = explode('|', $ghiChu, 2);
            $roomTypePart = str_replace('ROOMTYPE:', '', $parts[0]);
            return $roomTypePart;
        }
        return null;
    }
}
?>