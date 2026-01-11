<?php
class GuestProfileModel extends connectDB {
    
    // 1. Lấy thông tin cá nhân
    public function getProfile($id) {
        $sql = "SELECT * FROM hotels_guests WHERE MaKhachHang = '$id'";
        return $this->selectOne($sql);
    }

    // 2. Kiểm tra khách hàng đã có booking nào chưa (để khóa CMND)
    public function hasBookings($id) {
        $sql = "SELECT COUNT(*) as total FROM bookings_booking WHERE MaKhachHang = '$id'";
        $result = $this->selectOne($sql);
        return ($result['total'] > 0);
    }

    // 3. Kiểm tra trùng SĐT (Trừ chính mình)
    public function checkPhoneUnique($phone, $myId) {
        $sql = "SELECT * FROM hotels_guests WHERE SoDienThoaiKhachHang = '$phone' AND MaKhachHang != '$myId'";
        $result = $this->select($sql);
        return !empty($result); 
    }

    // [MỚI] 4. Kiểm tra trùng Email (Trừ chính mình)
    public function checkEmailUnique($email, $myId) {
        $sql = "SELECT * FROM hotels_guests WHERE EmailKhachHang = '$email' AND MaKhachHang != '$myId'";
        $result = $this->select($sql);
        return !empty($result); 
    }

    // 5. Cập nhật thông tin
    public function updateProfile($id, $ho, $ten, $email, $sdt, $cmnd, $diachi, $newPassword = null) {
        // Escaping dữ liệu để tránh lỗi SQL Injection cơ bản (nếu framework chưa xử lý)
        // Lưu ý: Tốt nhất nên dùng Prepared Statements nếu có thể
        
        if (empty($newPassword)) {
            $sql = "UPDATE hotels_guests SET 
                    HoKhachHang = '$ho',
                    TenKhachHang = '$ten',
                    EmailKhachHang = '$email',
                    SoDienThoaiKhachHang = '$sdt',
                    CMND_CCCDKhachHang = '$cmnd',
                    DiaChi = '$diachi'
                    WHERE MaKhachHang = '$id'";
        } else {
            $sql = "UPDATE hotels_guests SET 
                    HoKhachHang = '$ho',
                    TenKhachHang = '$ten',
                    EmailKhachHang = '$email',
                    SoDienThoaiKhachHang = '$sdt',
                    CMND_CCCDKhachHang = '$cmnd',
                    DiaChi = '$diachi',
                    MatKhau = '$newPassword'
                    WHERE MaKhachHang = '$id'";
        }
        return $this->execute($sql);
    }
}
?>