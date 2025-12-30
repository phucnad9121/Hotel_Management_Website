<?php
class PaymentModel extends connectDB {
    private static $resolvedPaymentTable = null;

    private function getPaymentTable() {
        if (self::$resolvedPaymentTable !== null) {
            return self::$resolvedPaymentTable;
        }

        $exists = $this->select("SHOW TABLES LIKE 'payments_payment'");
        if (!empty($exists)) {
            self::$resolvedPaymentTable = "payments_payment";
            return self::$resolvedPaymentTable;
        }

        $exists = $this->select("SHOW TABLES LIKE 'bookings_payments'");
        if (!empty($exists)) {
            self::$resolvedPaymentTable = "bookings_payments";
            return self::$resolvedPaymentTable;
        }

        self::$resolvedPaymentTable = "payments_payment";
        return self::$resolvedPaymentTable;
    }

    private function escape($value) {
        return mysqli_real_escape_string($this->con, (string)$value);
    }

    public function getDiscounts() {
        $sql = "SELECT MaGiamGia, TenGiamGia, TyLeGiamGia FROM bookings_discount ORDER BY MaGiamGia ASC";
        return $this->select($sql);
    }

    public function getDiscountById($id) {
        $id = $this->escape($id);
        $sql = "SELECT MaGiamGia, TenGiamGia, TyLeGiamGia FROM bookings_discount WHERE MaGiamGia = '$id' LIMIT 1";
        return $this->selectOne($sql);
    }

    public function getBookings($keyword = '') {
        $paymentTable = $this->getPaymentTable();
        $where = "";
        if (!empty($keyword)) {
            $k = $this->escape($keyword);
            $where = "WHERE b.MaDatPhong LIKE '%$k%'
                      OR g.HoKhachHang LIKE '%$k%'
                      OR g.TenKhachHang LIKE '%$k%'";
        }

        $sql = "SELECT b.MaDatPhong,
                       b.NgayNhanPhong,
                       b.NgayTraPhong,
                       b.ThoiGianLuuTru,
                       b.TrangThai,
                       b.SoTienDatPhong,
                       b.MaGiamGia,
                       b.MaKhachHang,
                       CONCAT(g.HoKhachHang, ' ', g.TenKhachHang) AS KhachHang,
                       (SELECT GROUP_CONCAT(r.SoPhong SEPARATOR ', ')
                        FROM rooms_roombooked rb
                        JOIN rooms_room r ON rb.MaPhong = r.MaPhong
                        WHERE rb.MaDatPhong = b.MaDatPhong) AS SoPhong,
                       (SELECT IFNULL(SUM(su.ThanhTien), 0)
                        FROM hotelservice_servicesused su
                        WHERE su.MaDatPhong = b.MaDatPhong) AS TienDichVu,
                       EXISTS(SELECT 1 FROM $paymentTable p WHERE p.MaDatPhong = b.MaDatPhong) AS DaThanhToan
                FROM bookings_booking b
                LEFT JOIN hotels_guests g ON b.MaKhachHang = g.MaKhachHang
                $where
                ORDER BY b.NgayNhanPhong DESC, b.MaDatPhong DESC";
        return $this->select($sql);
    }

    public function getBookingById($bookingId) {
        $bookingId = $this->escape($bookingId);
        $sql = "SELECT * FROM bookings_booking WHERE MaDatPhong = '$bookingId' LIMIT 1";
        return $this->selectOne($sql);
    }

    public function getServiceTotalByBooking($bookingId) {
        $bookingId = $this->escape($bookingId);
        $sql = "SELECT IFNULL(SUM(ThanhTien), 0) AS total
                FROM hotelservice_servicesused
                WHERE MaDatPhong = '$bookingId'";
        $row = $this->selectOne($sql);
        return isset($row['total']) ? (int)$row['total'] : 0;
    }

    public function getServicesByBooking($bookingId) {
        $bookingId = $this->escape($bookingId);
        $sql = "SELECT su.MaDichVuSuDung, su.MaDichVu, s.TenDichVu, su.SoLuong, su.DonGia, su.ThanhTien, su.NgaySuDung
                FROM hotelservice_servicesused su
                LEFT JOIN hotelservice_services s ON su.MaDichVu = s.MaDichVu
                WHERE su.MaDatPhong = '$bookingId'
                ORDER BY su.NgaySuDung DESC";
        return $this->select($sql);
    }

    public function paymentExists($bookingId) {
        $paymentTable = $this->getPaymentTable();
        $bookingId = $this->escape($bookingId);
        $sql = "SELECT MaDatPhong FROM $paymentTable WHERE MaDatPhong = '$bookingId' LIMIT 1";
        $row = $this->selectOne($sql);
        return !empty($row);
    }

    public function updateBookingDiscount($bookingId, $discountId) {
        $bookingId = $this->escape($bookingId);
        if (empty($discountId) || $discountId === 'none' || $discountId === '0') {
            $sql = "UPDATE bookings_booking SET MaGiamGia = NULL WHERE MaDatPhong = '$bookingId'";
            return $this->execute($sql);
        }

        $discountId = $this->escape($discountId);
        $sql = "UPDATE bookings_booking SET MaGiamGia = '$discountId' WHERE MaDatPhong = '$bookingId'";
        return $this->execute($sql);
    }

    public function updateBookingStatus($bookingId, $status) {
        $bookingId = $this->escape($bookingId);
        $status = $this->escape($status);
        $sql = "UPDATE bookings_booking SET TrangThai = '$status' WHERE MaDatPhong = '$bookingId'";
        return $this->execute($sql);
    }

    public function releaseRooms($bookingId) {
        $bookingId = $this->escape($bookingId);
        $sql = "UPDATE rooms_room r
                JOIN rooms_roombooked rb ON r.MaPhong = rb.MaPhong
                SET r.KhaDung = 'Yes'
                WHERE rb.MaDatPhong = '$bookingId'";
        return $this->execute($sql);
    }

    public function updateGuestStatusNotReserved($bookingId) {
        $bookingId = $this->escape($bookingId);
        $sql = "UPDATE hotels_guests g
                JOIN bookings_booking b ON g.MaKhachHang = b.MaKhachHang
                SET g.TrangThai = 'Not Reserved'
                WHERE b.MaDatPhong = '$bookingId'";
        return $this->execute($sql);
    }

    public function createPayment($bookingId, $roomCost, $serviceCost, $totalCost, $method) {
        $paymentTable = $this->getPaymentTable();
        $bookingId = $this->escape($bookingId);
        $method = $this->escape($method);

        $roomCost = (int)$roomCost;
        $serviceCost = (int)$serviceCost;
        $totalCost = (int)$totalCost;

        $paymentId = "TT" . date('YmdHis') . rand(100, 999);
        $paymentId = $this->escape($paymentId);

        if ($paymentTable === 'payments_payment') {
            $date = date('Y-m-d');
            $sql = "INSERT INTO payments_payment (MaThanhToan, MaDatPhong, TienPhong, TienDichVu, TongTien, PhuongThuc, NgayThanhToan)
                    VALUES ('$paymentId', '$bookingId', $roomCost, $serviceCost, $totalCost, '$method', '$date')";
            return $this->execute($sql);
        }

        $sql = "INSERT INTO bookings_payments (MaThanhToan, TrangThaiThanhToan, LoaiThanhToan, SoTienThanhToan, MaDatPhong)
                VALUES ('$paymentId', 'Paid', '$method', $totalCost, '$bookingId')";
        return $this->execute($sql);
    }

    public function getPayments($keyword = '') {
        $paymentTable = $this->getPaymentTable();
        $where = "";
        if (!empty($keyword)) {
            $k = $this->escape($keyword);
            $where = "WHERE p.MaThanhToan LIKE '%$k%' OR p.MaDatPhong LIKE '%$k%'";
        }

        if ($paymentTable === 'payments_payment') {
            $sql = "SELECT p.MaThanhToan, p.MaDatPhong, p.TienPhong, p.TienDichVu, p.TongTien, p.PhuongThuc, p.NgayThanhToan,
                           b.NgayNhanPhong, b.NgayTraPhong, b.ThoiGianLuuTru, b.TrangThai,
                           CONCAT(g.HoKhachHang, ' ', g.TenKhachHang) AS KhachHang,
                           (SELECT GROUP_CONCAT(r.SoPhong SEPARATOR ', ')
                            FROM rooms_roombooked rb
                            JOIN rooms_room r ON rb.MaPhong = r.MaPhong
                            WHERE rb.MaDatPhong = b.MaDatPhong) AS SoPhong
                    FROM payments_payment p
                    LEFT JOIN bookings_booking b ON p.MaDatPhong = b.MaDatPhong
                    LEFT JOIN hotels_guests g ON b.MaKhachHang = g.MaKhachHang
                    $where
                    ORDER BY p.NgayThanhToan DESC, p.MaThanhToan DESC";
            return $this->select($sql);
        }

        $sql = "SELECT p.MaThanhToan, p.MaDatPhong, p.SoTienThanhToan AS TongTien, p.LoaiThanhToan AS PhuongThuc, p.TrangThaiThanhToan, p.NgayThanhToan,
                       b.NgayNhanPhong, b.NgayTraPhong, b.ThoiGianLuuTru, b.TrangThai,
                       CONCAT(g.HoKhachHang, ' ', g.TenKhachHang) AS KhachHang,
                       (SELECT GROUP_CONCAT(r.SoPhong SEPARATOR ', ')
                        FROM rooms_roombooked rb
                        JOIN rooms_room r ON rb.MaPhong = r.MaPhong
                        WHERE rb.MaDatPhong = b.MaDatPhong) AS SoPhong
                FROM bookings_payments p
                LEFT JOIN bookings_booking b ON p.MaDatPhong = b.MaDatPhong
                LEFT JOIN hotels_guests g ON b.MaKhachHang = g.MaKhachHang
                $where
                ORDER BY p.NgayThanhToan DESC, p.MaThanhToan DESC";
        return $this->select($sql);
    }
}

