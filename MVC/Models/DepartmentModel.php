<?php
class DepartmentModel extends connectDB {
    // Lấy tất cả bộ phận
    public function getAll() {
        return $this->select("SELECT * FROM hotels_departments");
    }

    // Tìm kiếm bộ phận theo tên hoặc mã
    public function search($keyword) {
        $sql = "SELECT * FROM hotels_departments WHERE TenBoPhan LIKE '%$keyword%' OR MaBoPhan LIKE '%$keyword%'";
        return $this->select($sql);
    }

    // Kiểm tra trùng mã bộ phận
    public function checkDuplicate($id) {
    // Sử dụng hàm select có sẵn trong class của bạn
    $sql = "SELECT * FROM hotels_departments WHERE MaBoPhan = '$id'";
    $result = $this->select($sql);
    
    // Nếu kết quả trả về không rỗng nghĩa là đã tồn tại mã này
    return !empty($result);
}

    // Thêm bộ phận
    public function insert($id, $name, $desc, $salary, $title) {
        $sql = "INSERT INTO hotels_departments VALUES ('$id', '$name', '$desc', $salary, '$title')";
        return $this->execute($sql);
    }

    // Sửa bộ phận
    public function update($id, $name, $desc, $salary, $title) {
        $sql = "UPDATE hotels_departments SET TenBoPhan='$name', MoTaBoPhan='$desc', LuongKhoiDiem=$salary, ChucDanh='$title' WHERE MaBoPhan='$id'";
        return $this->execute($sql);
    }

    // Xóa bộ phận
    public function delete($id) {
        $sql = "DELETE FROM hotels_departments WHERE MaBoPhan = '$id'";
        return $this->execute($sql);
    }
}
