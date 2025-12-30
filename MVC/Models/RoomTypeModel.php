<?php
class RoomTypeModel extends connectDB {
    
    // =================================================================
    // PHẦN 1: DÙNG CHO TRANG QUẢN LÝ ADMIN (MỤC 3 CỦA BẠN)
    // =================================================================

    // Hàm lấy tất cả loại phòng (Dùng cho Dropdown khi Thêm Phòng và hiển thị danh sách)
    public function getAll() {
        $sql = "SELECT * FROM rooms_roomtype";
        return mysqli_query($this->con, $sql);
    }

    // Hàm thêm loại phòng mới
    public function insert($maLoai, $tenLoai, $gia, $mota) {
        $sql = "INSERT INTO rooms_roomtype (MaLoaiPhong, TenLoaiPhong, GiaPhong, MoTaPhong) 
                VALUES ('$maLoai', '$tenLoai', '$gia', '$mota')";
        return mysqli_query($this->con, $sql);
    }

    // Hàm xóa loại phòng
    public function delete($id) {
        // Lưu ý: Cần xử lý ràng buộc khóa ngoại trước khi xóa thực tế
        $sql = "DELETE FROM rooms_roomtype WHERE MaLoaiPhong='$id'";
        return mysqli_query($this->con, $sql);
    }

    // Hàm cập nhật loại phòng (Nếu cần sau này)
    public function update($id, $ten, $gia, $mota) {
        $sql = "UPDATE rooms_roomtype 
                SET TenLoaiPhong='$ten', GiaPhong='$gia', MoTaPhong='$mota' 
                WHERE MaLoaiPhong='$id'";
        return mysqli_query($this->con, $sql);
    }

    // =================================================================
    // PHẦN 2: DÙNG CHO CHỨC NĂNG ĐẶT PHÒNG / TRANG CHỦ (CỦA NHÓM)
    // (Đã chỉnh sửa để dùng mysqli_query cho đồng bộ)
    // =================================================================

    // Lấy tất cả loại phòng kèm số lượng phòng còn trống (Cho khách xem)
    public function getAllWithAvailability() {
        $sql = "SELECT rt.*, 
                (SELECT COUNT(*) FROM rooms_room r 
                 WHERE r.MaLoaiPhong = rt.MaLoaiPhong AND r.KhaDung = 'Yes') as SoPhongTrong
                FROM rooms_roomtype rt";
        return mysqli_query($this->con, $sql);
    }
    
    // Lấy thông tin chi tiết 1 loại phòng theo ID
    public function getById($id) {
        $sql = "SELECT * FROM rooms_roomtype WHERE MaLoaiPhong = '$id'";
        $result = mysqli_query($this->con, $sql);
        // Trả về 1 dòng dữ liệu dạng mảng
        return mysqli_fetch_assoc($result);
    }
    
    // Đếm số phòng còn trống (Hàm phụ trợ)
    public function countAvailableRooms($maLoaiPhong) {
        $sql = "SELECT COUNT(*) as total FROM rooms_room 
                WHERE MaLoaiPhong = '$maLoaiPhong' AND KhaDung = 'Yes'";
        $result = mysqli_query($this->con, $sql);
        $row = mysqli_fetch_assoc($result);
        return $row['total'];
    }
}
?>