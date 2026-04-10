<?php
use MVC\Model;
class ModelsRoomtypes extends Model {
    public function getAll() { return $this->db->query("SELECT rt.*,(SELECT COUNT(*) FROM rooms_room WHERE MaLoaiPhong=rt.MaLoaiPhong) as TotalRooms FROM rooms_roomtype rt ORDER BY rt.GiaPhong")->rows; }
    public function getById($id) { $id=$this->db->escape($id); return $this->db->query("SELECT * FROM rooms_roomtype WHERE MaLoaiPhong='$id'")->row; }
    public function create($d) { $id=$this->db->escape($d['MaLoaiPhong']);$n=$this->db->escape($d['TenLoaiPhong']);$g=floatval($d['GiaPhong']);$m=$this->db->escape($d['MoTaLoaiPhong']??'');$this->db->query("INSERT INTO rooms_roomtype(MaLoaiPhong,TenLoaiPhong,GiaPhong,MoTaLoaiPhong) VALUES('$id','$n',$g,'$m')"); return $this->getById($id); }
    public function update($id,$d) { $id=$this->db->escape($id);$s=[];if(isset($d['TenLoaiPhong']))$s[]="TenLoaiPhong='".$this->db->escape($d['TenLoaiPhong'])."'";if(isset($d['GiaPhong']))$s[]="GiaPhong=".floatval($d['GiaPhong']);if(isset($d['MoTaLoaiPhong']))$s[]="MoTaLoaiPhong='".$this->db->escape($d['MoTaLoaiPhong'])."'";if($s)$this->db->query("UPDATE rooms_roomtype SET ".implode(',',$s)." WHERE MaLoaiPhong='$id'"); }
    public function delete($id) { $id=$this->db->escape($id);$this->db->query("DELETE FROM rooms_roomtype WHERE MaLoaiPhong='$id'"); }
}
