<?php
class RoomTypeModel extends connectDB {
    
    // Lấy tất cả loại phòng kèm số phòng còn trống
    public function getAllWithAvailability() {
        $sql = "SELECT rt.*, 
                (SELECT COUNT(*) FROM rooms_room r 
                 WHERE r.MaLoaiPhong = rt.MaLoaiPhong AND r.KhaDung = 'Yes') as SoPhongTrong
                FROM rooms_roomtype rt";
        return $this->select($sql);
    }
    
    // Lấy thông tin loại phòng theo ID
    public function getById($id) {
        $sql = "SELECT * FROM rooms_roomtype WHERE MaLoaiPhong = '$id'";
        return $this->selectOne($sql);
    }
    
    // Đếm số phòng còn trống theo loại
    public function countAvailableRooms($maLoaiPhong) {
        $sql = "SELECT COUNT(*) as total FROM rooms_room 
                WHERE MaLoaiPhong = '$maLoaiPhong' AND KhaDung = 'Yes'";
        $result = $this->selectOne($sql);
        return $result['total'];
    }
}
?>