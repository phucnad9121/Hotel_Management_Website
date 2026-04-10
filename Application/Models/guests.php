<?php
use MVC\Model;
class ModelsGuests extends Model {
    public function getAll() { return $this->db->query("SELECT * FROM hotels_guests ORDER BY HoKhachHang,TenKhachHang")->rows; }
    public function getById($id) { $id=$this->db->escape($id); return $this->db->query("SELECT * FROM hotels_guests WHERE MaKhachHang='$id'")->row; }
    public function create($d) {
        $id=$d['MaKhachHang']?:'KH'.date('YmdHis').rand(100,999);
        $ho=$this->db->escape($d['HoKhachHang']);$ten=$this->db->escape($d['TenKhachHang']);
        $sdt=$this->db->escape($d['SoDienThoaiKhachHang']??'');$email=$this->db->escape($d['EmailKhachHang']??'');
        $cccd=$this->db->escape($d['CMND_CCCDKhachHang']??'');$dc=$this->db->escape($d['DiaChi']??'');
        $pw=$this->db->escape($d['MatKhau']??'123456');
        $this->db->query("INSERT INTO hotels_guests(MaKhachHang,HoKhachHang,TenKhachHang,SoDienThoaiKhachHang,EmailKhachHang,CMND_CCCDKhachHang,DiaChi,MatKhau) VALUES('$id','$ho','$ten','$sdt','$email','$cccd','$dc','$pw')");
        return $this->getById($id);
    }
    public function update($id,$d) {
        $id=$this->db->escape($id);$s=[];
        foreach(['HoKhachHang','TenKhachHang','SoDienThoaiKhachHang','EmailKhachHang','DiaChi'] as $f){if(isset($d[$f]))$s[]="$f='".$this->db->escape($d[$f])."'";}
        if($s)$this->db->query("UPDATE hotels_guests SET ".implode(',',$s)." WHERE MaKhachHang='$id'");
    }
    public function delete($id) { $id=$this->db->escape($id);$this->db->query("DELETE FROM hotels_guests WHERE MaKhachHang='$id'"); }
}
