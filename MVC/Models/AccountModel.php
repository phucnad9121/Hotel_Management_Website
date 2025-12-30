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

    // ===== QUẢN LÝ TÀI KHOẢN NHÂN VIÊN (authentication_login) =====

    public function getEmployees() {
        $sql = "SELECT MaNhanVien, HoNhanVien, TenNhanVien FROM hotels_employees ORDER BY MaNhanVien ASC";
        return $this->select($sql);
    }

    public function getAllLoginAccounts() {
        $sql = "SELECT l.*, CONCAT(e.HoNhanVien, ' ', e.TenNhanVien) AS NhanVien
                FROM authentication_login l
                LEFT JOIN hotels_employees e ON l.MaNhanVien = e.MaNhanVien
                ORDER BY l.MaDangNhap ASC";
        return $this->select($sql);
    }

    public function searchLoginAccounts($keyword) {
        $sql = "SELECT l.*, CONCAT(e.HoNhanVien, ' ', e.TenNhanVien) AS NhanVien
                FROM authentication_login l
                LEFT JOIN hotels_employees e ON l.MaNhanVien = e.MaNhanVien
                WHERE l.MaDangNhap LIKE '%$keyword%'
                   OR l.TenDangNhap LIKE '%$keyword%'
                   OR e.HoNhanVien LIKE '%$keyword%'
                   OR e.TenNhanVien LIKE '%$keyword%'
                ORDER BY l.MaDangNhap ASC";
        return $this->select($sql);
    }

    public function checkDuplicateLoginId($id) {
        $sql = "SELECT MaDangNhap FROM authentication_login WHERE MaDangNhap = '$id'";
        $result = $this->select($sql);
        return !empty($result);
    }

    public function checkDuplicateLoginUsername($username, $excludeId = '') {
        $where = "TenDangNhap = '$username'";
        if (!empty($excludeId)) {
            $where .= " AND MaDangNhap <> '$excludeId'";
        }
        $sql = "SELECT TenDangNhap FROM authentication_login WHERE $where";
        $result = $this->select($sql);
        return !empty($result);
    }

    public function insertLoginAccount($id, $username, $password, $employeeId, $isNew) {
        $sql = "INSERT INTO authentication_login (MaDangNhap, TenDangNhap, MatKhau, MaNhanVien, NguoiDungMoi)
                VALUES ('$id', '$username', '$password', '$employeeId', '$isNew')";
        return $this->execute($sql);
    }

    public function updateLoginAccount($id, $username, $password, $employeeId, $isNew) {
        if ($password === null || $password === '') {
            $sql = "UPDATE authentication_login
                    SET TenDangNhap='$username', MaNhanVien='$employeeId', NguoiDungMoi='$isNew'
                    WHERE MaDangNhap='$id'";
        } else {
            $sql = "UPDATE authentication_login
                    SET TenDangNhap='$username', MatKhau='$password', MaNhanVien='$employeeId', NguoiDungMoi='$isNew'
                    WHERE MaDangNhap='$id'";
        }
        return $this->execute($sql);
    }

    public function deleteLoginAccount($id) {
        $sql = "DELETE FROM authentication_login WHERE MaDangNhap = '$id'";
        return $this->execute($sql);
    }
}
?>
