<?php
class RoomModel extends connectDB {
    
    // =================================================================
    // PHẦN 1: CODE CŨ CỦA NHÓM (DÙNG CHO ĐẶT PHÒNG & CHECK-IN)
    // =================================================================

    // Lấy phòng trống theo loại (Cho Lễ tân đặt phòng)
    public function getAvailableByType($maLoaiPhong) {
        $sql = "SELECT * FROM rooms_room 
                WHERE MaLoaiPhong = '$maLoaiPhong' AND KhaDung = 'Yes'";
        return mysqli_query($this->con, $sql); // Đã chỉnh lại dùng mysqli_query cho chắc chắn
    }
    
    // Cập nhật nhanh trạng thái (Cho chức năng Check-in/Check-out)
    public function updateAvailability($maPhong, $status) {
        $sql = "UPDATE rooms_room SET KhaDung = '$status' WHERE MaPhong = '$maPhong'";
        return mysqli_query($this->con, $sql);
    }

    // =================================================================
    // PHẦN 2: CODE MỚI(QUẢN LÝ PHÒNG)
    // =================================================================

    // 1. Lấy danh sách phòng (Có JOIN để lấy tên loại phòng)
    // Dùng cho trang Quản lý Admin và Xuất Excel
    public function getAll($keyword = '') {
        $sql = "SELECT r.*, rt.TenLoaiPhong 
                FROM rooms_room r 
                LEFT JOIN rooms_roomtype rt ON r.MaLoaiPhong = rt.MaLoaiPhong";
        
        if($keyword != '') {
            $sql .= " WHERE r.SoPhong LIKE '%$keyword%' OR r.MaPhong LIKE '%$keyword%'";
        }
        
        $sql .= " ORDER BY r.MaPhong ASC";
        return mysqli_query($this->con, $sql);
    }

    // 2. Kiểm tra trùng mã (Khi thêm mới)
    public function checkExists($id) {
        $sql = "SELECT * FROM rooms_room WHERE MaPhong = '$id'";
        return mysqli_query($this->con, $sql);
    }

    // 3. Thêm phòng mới (Admin nhập tay)
    public function insert($id, $soPhong, $maLoai, $khaDung) {
        $sql = "INSERT INTO rooms_room (MaPhong, SoPhong, MaLoaiPhong, KhaDung) 
                VALUES ('$id', '$soPhong', '$maLoai', '$khaDung')";
        return mysqli_query($this->con, $sql);
    }

    // 4. Cập nhật thông tin phòng (Sửa tên, loại, trạng thái)
    // Hàm này khác updateAvailability ở trên là nó sửa được cả Tên và Loại phòng
    public function updateRoomInfo($id, $soPhong, $maLoai, $khaDung) {
        $sql = "UPDATE rooms_room 
                SET SoPhong='$soPhong', MaLoaiPhong='$maLoai', KhaDung='$khaDung' 
                WHERE MaPhong='$id'";
        return mysqli_query($this->con, $sql);
    }

    // 5. Xóa phòng
    public function delete($id) {
        // Lưu ý: Cần thêm logic kiểm tra xem phòng có đang được đặt không trước khi xóa
        // ở Controller hoặc ở đây. Nếu xóa phòng đang có booking sẽ lỗi khóa ngoại.
        $sql = "DELETE FROM rooms_room WHERE MaPhong='$id'";
        return mysqli_query($this->con, $sql);
    }
}
?>