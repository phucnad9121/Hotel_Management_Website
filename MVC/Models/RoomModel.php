<?php
class RoomModel extends connectDB {
    
    // Lấy phòng trống theo loại
    public function getAvailableByType($maLoaiPhong) {
        $sql = "SELECT * FROM rooms_room 
                WHERE MaLoaiPhong = '$maLoaiPhong' AND KhaDung = 'Yes'";
        return $this->select($sql);
    }
    
    // Cập nhật trạng thái phòng
    public function updateAvailability($maPhong, $status) {
        $sql = "UPDATE rooms_room SET KhaDung = '$status' WHERE MaPhong = '$maPhong'";
        return $this->execute($sql);
    }
}
?>