<?php
class AccountModel extends connectDB {
    // Kiểm tra Admin
    public function checkAdminLogin($user, $pass) {
        $sql = "SELECT * FROM authentication_admin WHERE TenDangNhap = '$user' AND MatKhau = '$pass'";
        return $this->selectOne($sql); // Sử dụng hàm selectOne có sẵn trong lớp Database của bạn
    }

    // Kiểm tra Nhân viên
    public function checkEmployeeLogin($user, $pass) {
        $sql = "SELECT * FROM authentication_login WHERE TenDangNhap = '$user' AND MatKhau = '$pass'";
        return $this->selectOne($sql);
    }

    // MỚI: Kiểm tra Khách hàng (Đăng nhập bằng Số điện thoại)
    public function checkGuestLogin($phone, $pass) {
        $sql = "SELECT * FROM hotels_guests WHERE SoDienThoaiKhachHang = '$phone' AND MatKhau = '$pass'";
        return $this->selectOne($sql);
    }
}
?>