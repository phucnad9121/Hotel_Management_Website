<?php
class GuestModel extends connectDB {
    
    // Tạo tài khoản khách hàng
    public function createGuest($data) {
        $sql = "INSERT INTO hotels_guests (TenKhachHang, HoKhachHang, EmailKhachHang, 
                SoDienThoaiKhachHang, CMND_CCCDKhachHang, DiaChi, MatKhau) 
                VALUES ('{$data['TenKhachHang']}', '{$data['HoKhachHang']}', 
                '{$data['EmailKhachHang']}', '{$data['SoDienThoaiKhachHang']}', 
                '{$data['CMND_CCCDKhachHang']}', '{$data['DiaChi']}', '{$data['MatKhau']}')";
        return $this->execute($sql);
    }
    
    // Kiểm tra số điện thoại đã tồn tại
    public function checkPhoneExists($phone) {
        $sql = "SELECT * FROM hotels_guests WHERE SoDienThoaiKhachHang = '$phone'";
        $result = $this->select($sql);
        return !empty($result);
    }
    
    // Lấy thông tin khách hàng theo ID
    public function getGuestById($id) {
        $sql = "SELECT MaKhachHang, TenKhachHang, HoKhachHang, EmailKhachHang, 
                SoDienThoaiKhachHang, CMND_CCCDKhachHang, DiaChi, TrangThai, NgayTao 
                FROM hotels_guests WHERE MaKhachHang = $id";
        return $this->selectOne($sql);
    }
    
    // Lấy tất cả khách hàng (không bao gồm mật khẩu)
    public function getAll() {
        $sql = "SELECT MaKhachHang, TenKhachHang, HoKhachHang, EmailKhachHang, 
                SoDienThoaiKhachHang, CMND_CCCDKhachHang, DiaChi, TrangThai, NgayTao 
                FROM hotels_guests ORDER BY NgayTao DESC";
        return $this->select($sql);
    }
    
    // Tìm kiếm khách hàng
    public function search($keyword) {
        $sql = "SELECT MaKhachHang, TenKhachHang, HoKhachHang, EmailKhachHang, 
                SoDienThoaiKhachHang, CMND_CCCDKhachHang, DiaChi, TrangThai, NgayTao 
                FROM hotels_guests 
                WHERE TenKhachHang LIKE '%$keyword%' 
                OR HoKhachHang LIKE '%$keyword%' 
                OR SoDienThoaiKhachHang LIKE '%$keyword%' 
                OR CMND_CCCDKhachHang LIKE '%$keyword%'";
        return $this->select($sql);
    }
    
    // Cập nhật trạng thái khách hàng
    public function updateStatus($id, $status) {
        $sql = "UPDATE hotels_guests SET TrangThai = '$status' WHERE MaKhachHang = $id";
        return $this->execute($sql);
    }
}
?>