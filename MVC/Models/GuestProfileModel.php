<?php
class GuestProfileModel extends connectDB {
    
    // 1. Lấy thông tin cá nhân (Giống hàm getGuestById cũ nhưng tách ra cho an toàn)
    public function getProfile($id) {
        // Chỉ lấy những trường cần thiết hiển thị
        $sql = "SELECT * FROM hotels_guests WHERE MaKhachHang = '$id'";
        return $this->selectOne($sql);
    }

    // 2. Kiểm tra trùng SĐT (Trừ chính mình ra)
    public function checkPhoneUnique($phone, $myId) {
        $sql = "SELECT * FROM hotels_guests WHERE SoDienThoaiKhachHang = '$phone' AND MaKhachHang != '$myId'";
        $result = $this->select($sql);
        return !empty($result); // Trả về true nếu bị trùng
    }

    // 3. Cập nhật thông tin (Có xử lý đổi mật khẩu)
    public function updateProfile($id, $ho, $ten, $email, $sdt, $cmnd, $diachi, $newPassword = null) {
        
        // Nếu người dùng KHÔNG nhập mật khẩu mới -> Giữ nguyên mật khẩu cũ
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
            // Nếu có nhập mật khẩu mới -> Cập nhật luôn mật khẩu
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