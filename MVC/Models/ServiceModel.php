<?php
class ServiceModel extends connectDB {
    private function escape($value) {
        return mysqli_real_escape_string($this->con, (string)$value);
    }

    private function generateServiceUsedId() {
        return "SD" . date('YmdHis') . rand(100, 999);
    }
    
    // ==================== MASTER DATA (GIỮ NGUYÊN) ====================
    public function getAll() {
        $sql = "SELECT * FROM hotelservice_services ORDER BY TenDichVu ASC";
        return $this->select($sql);
    }
    
    public function search($keyword) {
        $keyword = mysqli_real_escape_string($this->con, $keyword);
        $sql = "SELECT * FROM hotelservice_services 
                WHERE TenDichVu LIKE '%$keyword%' 
                OR MaDichVu LIKE '%$keyword%'
                OR MoTaDichVu LIKE '%$keyword%'
                ORDER BY TenDichVu ASC";
        return $this->select($sql);
    }
    
    public function getById($id) {
        $id = mysqli_real_escape_string($this->con, $id);
        $sql = "SELECT * FROM hotelservice_services WHERE MaDichVu = '$id'";
        return $this->selectOne($sql);
    }
    
    public function checkIdExists($id) {
        $id = mysqli_real_escape_string($this->con, $id);
        $sql = "SELECT * FROM hotelservice_services WHERE MaDichVu = '$id'";
        $result = $this->select($sql);
        return !empty($result);
    }
    
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
    
    public function insert($name, $desc, $price) {
        $name = mysqli_real_escape_string($this->con, $name);
        $desc = mysqli_real_escape_string($this->con, $desc);
        $price = floatval($price);
        $sql = "INSERT INTO hotelservice_services (TenDichVu, MoTaDichVu, ChiPhiDichVu) 
                VALUES ('$name', '$desc', $price)";
        return $this->execute($sql);
    }
    
    public function insertWithId($id, $name, $desc, $price) {
        $id = mysqli_real_escape_string($this->con, $id);
        $name = mysqli_real_escape_string($this->con, $name);
        $desc = mysqli_real_escape_string($this->con, $desc);
        $price = floatval($price);
        $sql = "INSERT INTO hotelservice_services (MaDichVu, TenDichVu, MoTaDichVu, ChiPhiDichVu) 
                VALUES ('$id', '$name', '$desc', $price)";
        return $this->execute($sql);
    }
    
    public function update($id, $name, $desc, $price) {
        $id = mysqli_real_escape_string($this->con, $id);
        $name = mysqli_real_escape_string($this->con, $name);
        $desc = mysqli_real_escape_string($this->con, $desc);
        $price = floatval($price);
        $sql = "UPDATE hotelservice_services 
                SET TenDichVu = '$name', MoTaDichVu = '$desc', ChiPhiDichVu = $price 
                WHERE MaDichVu = '$id'";
        return $this->execute($sql);
    }
    
    public function delete($id) {
        if (empty($id)) { return false; }
        $id = mysqli_real_escape_string($this->con, $id);
        
        $checkSql = "SELECT COUNT(*) as count FROM hotelservice_servicesused WHERE MaDichVu = '$id'";
        $result = $this->selectOne($checkSql);
        
        if ($result && $result['count'] > 0) {
            return false;
        }
        
        $sql = "DELETE FROM hotelservice_services WHERE MaDichVu = '$id'";
        return $this->execute($sql);
    }
    
    // ==================== DỊCH VỤ ĐÃ SỬ DỤNG (Used Services) ====================
    
    // [QUAN TRỌNG] Lấy cột MaDichVuSuDung
    public function getServicesByBooking($maDatPhong) {
        $maDatPhong = $this->escape($maDatPhong);
        $sql = "SELECT su.*, s.TenDichVu, s.MoTaDichVu, s.ChiPhiDichVu
                FROM hotelservice_servicesused su
                JOIN hotelservice_services s ON su.MaDichVu = s.MaDichVu
                WHERE su.MaDatPhong = '$maDatPhong'
                ORDER BY su.NgaySuDung DESC";
        return $this->select($sql);
    }

    // [QUAN TRỌNG] Kiểm tra MaDichVuSuDung
    public function checkServiceUsedExists($maDatPhong, $maDichVu) {
        $maDatPhong = $this->escape($maDatPhong);
        $maDichVu = $this->escape($maDichVu);
        $sql = "SELECT MaDichVuSuDung FROM hotelservice_servicesused 
                WHERE MaDatPhong = '$maDatPhong' AND MaDichVu = '$maDichVu'";
        $result = $this->select($sql);
        return !empty($result);
    }
    
    public function addServiceUsed($maDatPhong, $maDichVu) {
        $serviceUsedId = $this->escape($this->generateServiceUsedId());
        $maDatPhong = $this->escape($maDatPhong);
        $maDichVu = $this->escape($maDichVu);
        
        $service = $this->getById($maDichVu);
        if (!$service) { return false; }
        
        $price = (int)($service['ChiPhiDichVu'] ?? 0);
        $soLuong = 1;
        $thanhTien = $price * $soLuong;
        
        $sql = "INSERT INTO hotelservice_servicesused (MaDichVuSuDung, MaDatPhong, MaDichVu, SoLuong, DonGia, ThanhTien) 
                VALUES ('$serviceUsedId', '$maDatPhong', '$maDichVu', $soLuong, $price, $thanhTien)";
        return $this->execute($sql);
    }
    
    // [QUAN TRỌNG] Sửa id -> MaDichVuSuDung
    public function removeServiceUsed($id) {
        $id = $this->escape($id);
        $sql = "DELETE FROM hotelservice_servicesused WHERE MaDichVuSuDung = '$id'";
        return $this->execute($sql);
    }
    
    public function getTotalServiceCost($maDatPhong) {
        $maDatPhong = $this->escape($maDatPhong);
        $sql = "SELECT SUM(ThanhTien) as total 
                FROM hotelservice_servicesused 
                WHERE MaDatPhong = '$maDatPhong'";
        $result = $this->selectOne($sql);
        return $result['total'] ?? 0;
    }
}
?>
