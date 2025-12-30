<?php
class DiscountModel extends connectDB {
    private static $resolvedTableName = null;

    private function getTableName() {
        if (self::$resolvedTableName !== null) {
            return self::$resolvedTableName;
        }

        $exists = $this->select("SHOW TABLES LIKE 'bookings_discount'");
        if (!empty($exists)) {
            self::$resolvedTableName = "bookings_discount";
            return self::$resolvedTableName;
        }

        $exists = $this->select("SHOW TABLES LIKE 'booking_discount'");
        if (!empty($exists)) {
            self::$resolvedTableName = "booking_discount";
            return self::$resolvedTableName;
        }

        self::$resolvedTableName = "bookings_discount";
        return self::$resolvedTableName;
    }

    public function getAll() {
        $table = $this->getTableName();
        $sql = "SELECT d.*, CONCAT(e.HoNhanVien, ' ', e.TenNhanVien) AS NguoiTao
                FROM $table d
                LEFT JOIN hotels_employees e ON d.MaNhanVien = e.MaNhanVien
                ORDER BY d.MaGiamGia ASC";
        return $this->select($sql);
    }

    public function search($keyword) {
        $table = $this->getTableName();
        $sql = "SELECT d.*, CONCAT(e.HoNhanVien, ' ', e.TenNhanVien) AS NguoiTao
                FROM $table d
                LEFT JOIN hotels_employees e ON d.MaNhanVien = e.MaNhanVien
                WHERE d.TenGiamGia LIKE '%$keyword%' OR d.MaGiamGia LIKE '%$keyword%'
                ORDER BY d.MaGiamGia ASC";
        return $this->select($sql);
    }

    public function checkDuplicate($id) {
        $table = $this->getTableName();
        $sql = "SELECT * FROM $table WHERE MaGiamGia = '$id'";
        $result = $this->select($sql);
        return !empty($result);
    }

    public function insert($id, $name, $desc, $rate, $employeeId) {
        $table = $this->getTableName();
        $sql = "INSERT INTO $table (MaGiamGia, TenGiamGia, MoTaGiamGia, TyLeGiamGia, MaNhanVien)
                VALUES ('$id', '$name', '$desc', $rate, '$employeeId')";
        return $this->execute($sql);
    }

    public function update($id, $name, $desc, $rate, $employeeId) {
        $table = $this->getTableName();
        $sql = "UPDATE $table
                SET TenGiamGia='$name', MoTaGiamGia='$desc', TyLeGiamGia=$rate, MaNhanVien='$employeeId'
                WHERE MaGiamGia='$id'";
        return $this->execute($sql);
    }

    public function delete($id) {
        $table = $this->getTableName();
        $sql = "DELETE FROM $table WHERE MaGiamGia = '$id'";
        return $this->execute($sql);
    }
}

