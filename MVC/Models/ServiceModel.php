<?php
class ServiceModel extends connectDB {
    
    // Lấy tất cả dịch vụ
    public function getAll() {
        $sql = "SELECT * FROM hotelservice_services ORDER BY TenDichVu ASC";
        return $this->select($sql);
    }
    
    // Tìm kiếm dịch vụ
    public function search($keyword) {
        $keyword = mysqli_real_escape_string($this->con, $keyword);
        $sql = "SELECT * FROM hotelservice_services 
                WHERE TenDichVu LIKE '%$keyword%' 
                OR MaDichVu LIKE '%$keyword%'
                OR MoTaDichVu LIKE '%$keyword%'
                ORDER BY TenDichVu ASC";
        return $this->select($sql);
    }
    
    // Lấy dịch vụ theo ID
    public function getById($id) {
        $id = mysqli_real_escape_string($this->con, $id);
        $sql = "SELECT * FROM hotelservice_services WHERE MaDichVu = '$id'";
        return $this->selectOne($sql);
    }
    
    // Kiểm tra mã dịch vụ đã tồn tại chưa
    public function checkIdExists($id) {
        $id = mysqli_real_escape_string($this->con, $id);
        $sql = "SELECT * FROM hotelservice_services WHERE MaDichVu = '$id'";
        $result = $this->select($sql);
        return !empty($result);
    }
    
    // Kiểm tra trùng tên dịch vụ
    public function checkDuplicate($name, $excludeId = null) {
        $name = mysqli_real_escape_string($this->con, $name);
        $sql = "SELECT * FROM hotelservice_services WHERE TenDichVu = '$name'";
        if ($excludeId) {
            $excludeId = mysqli_real_escape_string($this->con, $excludeId);
            $sql .= " AND MaDichVu != '$excludeId'";
        }
        $result = $this->select($sql);
        return !empty($result);
    }
    
    // Thêm dịch vụ mới (không dùng)
    public function insert($name, $desc, $price) {
        $name = mysqli_real_escape_string($this->con, $name);
        $desc = mysqli_real_escape_string($this->con, $desc);
        $price = floatval($price);
        
        $sql = "INSERT INTO hotelservice_services (TenDichVu, MoTaDichVu, ChiPhiDichVu) 
                VALUES ('$name', '$desc', $price)";
        return $this->execute($sql);
    }
    
    // Thêm dịch vụ với mã tự nhập
    public function insertWithId($id, $name, $desc, $price) {
        $id = mysqli_real_escape_string($this->con, $id);
        $name = mysqli_real_escape_string($this->con, $name);
        $desc = mysqli_real_escape_string($this->con, $desc);
        $price = floatval($price);
        
        $sql = "INSERT INTO hotelservice_services (MaDichVu, TenDichVu, MoTaDichVu, ChiPhiDichVu) 
                VALUES ('$id', '$name', '$desc', $price)";
        
        return $this->execute($sql);
    }
    
    // Cập nhật dịch vụ
    public function update($id, $name, $desc, $price) {
        $id = mysqli_real_escape_string($this->con, $id);
        $name = mysqli_real_escape_string($this->con, $name);
        $desc = mysqli_real_escape_string($this->con, $desc);
        $price = floatval($price);
        
        $sql = "UPDATE hotelservice_services 
                SET TenDichVu = '$name', 
                    MoTaDichVu = '$desc', 
                    ChiPhiDichVu = $price 
                WHERE MaDichVu = '$id'";
        return $this->execute($sql);
    }
    
    // Xóa dịch vụ
    public function delete($id) {
        if (empty($id)) {
            return false;
        }
        
        $id = mysqli_real_escape_string($this->con, $id);
        
        // Kiểm tra dịch vụ có đang được sử dụng không
        $checkSql = "SELECT COUNT(*) as count FROM hotelservice_servicesused WHERE MaDichVu = '$id'";
        $result = $this->selectOne($checkSql);
        
        if ($result && $result['count'] > 0) {
            return false;
        }
        
        $sql = "DELETE FROM hotelservice_services WHERE MaDichVu = '$id'";
        return $this->execute($sql);
    }
    
    // ==================== DỊCH VỤ ĐÃ SỬ DỤNG ====================
    
    // Lấy dịch vụ đã sử dụng theo booking
    public function getServicesByBooking($maDatPhong) {
        $maDatPhong = intval($maDatPhong);
        $sql = "SELECT su.*, s.TenDichVu, s.MoTaDichVu, s.ChiPhiDichVu,
                su.ThanhTien, su.SoLuong, su.DonGia, su.NgaySuDung
                FROM hotelservice_servicesused su
                JOIN hotelservice_services s ON su.MaDichVu = s.MaDichVu
                WHERE su.MaDatPhong = $maDatPhong
                ORDER BY su.NgaySuDung DESC";
        return $this->select($sql);
    }
    
    // Thêm dịch vụ đã sử dụng
    public function addServiceUsed($maDatPhong, $maDichVu) {
        $maDatPhong = intval($maDatPhong);
        $maDichVu = mysqli_real_escape_string($this->con, $maDichVu);
        
        // Kiểm tra dịch vụ có tồn tại không
        $service = $this->getById($maDichVu);
        if (!$service) {
            return false;
        }
        
        $price = $service['ChiPhiDichVu'];
        $soLuong = 1;
        $thanhTien = $price * $soLuong;
        
        $sql = "INSERT INTO hotelservice_servicesused (MaDatPhong, MaDichVu, SoLuong, DonGia, ThanhTien, NgaySuDung) 
                VALUES ($maDatPhong, '$maDichVu', $soLuong, $price, $thanhTien, NOW())";
        
        return $this->execute($sql);
    }
    
    // Xóa dịch vụ đã sử dụng
    public function removeServiceUsed($id) {
        $id = intval($id);
        $sql = "DELETE FROM hotelservice_servicesused WHERE MaDichVuSuDung = $id";
        return $this->execute($sql);
    }
    
    // Tính tổng tiền dịch vụ của một booking
    public function getTotalServiceCost($maDatPhong) {
        $maDatPhong = intval($maDatPhong);
        $sql = "SELECT SUM(ThanhTien) as total 
                FROM hotelservice_servicesused 
                WHERE MaDatPhong = $maDatPhong";
        $result = $this->selectOne($sql);
        return $result['total'] ?? 0;
    }
}
?>