<?php
class BookingModel extends connectDB {
    private function escape($value) {
        return mysqli_real_escape_string($this->con, (string)$value);
    }

    private function generateBookingId() {
        return "DP" . date('YmdHis') . rand(100, 999);
    }

    private function generateRoomBookedId() {
        return "RB" . date('YmdHis') . rand(100, 999);
    }

    public function createBooking($data) {
        $bookingId = trim($data['MaDatPhong'] ?? '');
        if ($bookingId === '') {
            $bookingId = $this->generateBookingId();
        }

        $bookingId = $this->escape($bookingId);
        $ngayDatPhong = $this->escape($data['NgayDatPhong'] ?? '');
        $ngayNhanPhong = $this->escape($data['NgayNhanPhong'] ?? '');
        $ngayTraPhong = $this->escape($data['NgayTraPhong'] ?? '');
        $maKhachHang = $this->escape($data['MaKhachHang'] ?? '');
        $ghiChu = $this->escape($data['GhiChu'] ?? '');

        $thoiGianLuuTru = (int)($data['ThoiGianLuuTru'] ?? 0);
        $soTienDatPhong = (int)($data['SoTienDatPhong'] ?? 0);

        $sql = "INSERT INTO bookings_booking
                (MaDatPhong, NgayDatPhong, ThoiGianLuuTru, NgayNhanPhong, NgayTraPhong,
                 SoTienDatPhong, MaKhachHang, GhiChu)
                VALUES ('$bookingId', '$ngayDatPhong', $thoiGianLuuTru,
                        '$ngayNhanPhong', '$ngayTraPhong', $soTienDatPhong,
                        '$maKhachHang', '$ghiChu')";
        return $this->execute($sql);
    }

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

    public function getById($id) {
        $id = $this->escape($id);
        $sql = "SELECT * FROM bookings_booking WHERE MaDatPhong = '$id'";
        return $this->selectOne($sql);
    }

    public function getByGuestId($guestId) {
        $guestId = $this->escape($guestId);
        $sql = "SELECT b.*,
                       (SELECT GROUP_CONCAT(r.SoPhong SEPARATOR ', ')
                        FROM rooms_roombooked rb
                        JOIN rooms_room r ON rb.MaPhong = r.MaPhong
                        WHERE rb.MaDatPhong = b.MaDatPhong) as SoPhong
                FROM bookings_booking b
                WHERE b.MaKhachHang = '$guestId'
                ORDER BY b.NgayTao DESC";
        return $this->select($sql);
    }

    public function search($keyword) {
        $keyword = $this->escape($keyword);
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

    public function updateStatus($id, $status) {
        $id = $this->escape($id);
        $status = $this->escape($status);
        $sql = "UPDATE bookings_booking SET TrangThai = '$status' WHERE MaDatPhong = '$id'";
        return $this->execute($sql);
    }

    public function assignRoom($maDatPhong, $maPhong) {
        $roomBookedId = $this->escape($this->generateRoomBookedId());
        $maDatPhong = $this->escape($maDatPhong);
        $maPhong = $this->escape($maPhong);

        $sql = "INSERT INTO rooms_roombooked (MaPhongDaDat, MaDatPhong, MaPhong)
                VALUES ('$roomBookedId', '$maDatPhong', '$maPhong')";
        return $this->execute($sql);
    }

    public function getAssignedRooms($maDatPhong) {
        $maDatPhong = $this->escape($maDatPhong);
        $sql = "SELECT rb.*, r.SoPhong
                FROM rooms_roombooked rb
                JOIN rooms_room r ON rb.MaPhong = r.MaPhong
                WHERE rb.MaDatPhong = '$maDatPhong'";
        return $this->select($sql);
    }

    public function parseRoomType($ghiChu) {
        if (strpos($ghiChu, 'ROOMTYPE:') === 0) {
            $parts = explode('|', $ghiChu, 2);
            $roomTypePart = str_replace('ROOMTYPE:', '', $parts[0]);
            return $roomTypePart;
        }
        return null;
    }

    public function checkBookingExist($maKhachHang) {
        $maKhachHang = $this->escape($maKhachHang);
        $sql = "SELECT COUNT(*) as total FROM bookings_booking WHERE MaKhachHang = '$maKhachHang'";
        $result = $this->selectOne($sql);
        return !empty($result) && (int)$result['total'] > 0;
    }

    public function getAvailableRooms($type) {
        $sql = "SELECT r.* FROM rooms_room r WHERE r.KhaDung = 'Yes'";

        if (!empty($type)) {
            $type = $this->escape($type);
            $sql .= " AND r.MaLoaiPhong = '$type'";
        }

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
