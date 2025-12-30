<?php
class EmployeeModel extends connectDB {
    
    public function getList($keyword = "") {
        $sql = "SELECT nv.*, bp.TenBoPhan 
                FROM hotels_employees nv 
                LEFT JOIN hotels_departments bp ON nv.MaBoPhan = bp.MaBoPhan";
        
        if (!empty($keyword)) {
            $k = mysqli_real_escape_string($this->con, $keyword);
            $sql .= " WHERE nv.MaNhanVien LIKE '%$k%' 
                      OR nv.TenNhanVien LIKE '%$k%' 
                      OR nv.SoDienThoaiNV LIKE '%$k%'
                      OR nv.CMND_CCCD LIKE '%$k%'";
        }
        $sql .= " ORDER BY nv.MaNhanVien ASC";
        return $this->select($sql);
    }

    public function getDepartments() {
        return $this->select("SELECT MaBoPhan, TenBoPhan FROM hotels_departments");
    }

    //Thêm và Sửa nhân viên
    public function save($data, $isEdit) {
        $ma = mysqli_real_escape_string($this->con, $data['MaNhanVien']);
        $ho = mysqli_real_escape_string($this->con, $data['HoNhanVien']);
        $ten = mysqli_real_escape_string($this->con, $data['TenNhanVien']);
        $chucdanh = mysqli_real_escape_string($this->con, $data['ChucDanhNV']);
        $sdt = mysqli_real_escape_string($this->con, $data['SoDienThoaiNV']);
        $email = mysqli_real_escape_string($this->con, $data['EmailNhanVien']);
        $diachi = mysqli_real_escape_string($this->con, $data['DiaChi']);
        $mabp = !empty($data['MaBoPhan']) ? "'".$data['MaBoPhan']."'" : "NULL";
        $cccd = mysqli_real_escape_string($this->con, $data['CMND_CCCD']);
        $ngayvao = !empty($data['NgayVaoLam']) ? "'".$data['NgayVaoLam']."'" : "NULL";

        if ($isEdit == "1") {
            $sql = "UPDATE hotels_employees SET 
                    HoNhanVien='$ho', TenNhanVien='$ten', ChucDanhNV='$chucdanh', 
                    SoDienThoaiNV='$sdt', EmailNhanVien='$email', DiaChi='$diachi', 
                    MaBoPhan=$mabp, CMND_CCCD='$cccd', NgayVaoLam=$ngayvao
                    WHERE MaNhanVien='$ma'";
        } else {
            $sql = "INSERT INTO hotels_employees (MaNhanVien, HoNhanVien, TenNhanVien, ChucDanhNV, SoDienThoaiNV, EmailNhanVien, DiaChi, MaBoPhan, CMND_CCCD, NgayVaoLam) 
                    VALUES ('$ma', '$ho', '$ten', '$chucdanh', '$sdt', '$email', '$diachi', $mabp, '$cccd', $ngayvao)";
        }
        return $this->execute($sql);
    }

    // Xóa nhân viên
    public function delete($id) {
        $id = mysqli_real_escape_string($this->con, $id);
        return $this->execute("DELETE FROM hotels_employees WHERE MaNhanVien = '$id'");
    }


    // IMPORT EXCEL 
    // kiểm tra mã NV tồn tại
    public function checkDuplicate($id){
        $id = mysqli_real_escape_string($this->con, $id);
        $res = $this->select("SELECT MaNhanVien FROM hotels_employees WHERE MaNhanVien='$id'");
        return !empty($res); // true nếu tồn tại
    }

    // insert trực tiếp khi import excel
    public function insert($MaNhanVien,$HoNhanVien,$TenNhanVien,$SoDienThoaiNV,$EmailNhanVien,$MaBoPhan,$ChucDanhNV,$CMND_CCCD,$NgayVaoLam,$DiaChi){
        $sql = "INSERT INTO hotels_employees VALUES
                ('$MaNhanVien','$HoNhanVien','$TenNhanVien','$SoDienThoaiNV','$EmailNhanVien','$MaBoPhan','$ChucDanhNV','$CMND_CCCD','$NgayVaoLam','$DiaChi')";
        return $this->execute($sql);
    }
}
