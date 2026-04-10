<?php
use MVC\Model;
class ModelsServiceorders extends Model {
    public function getByBooking($bid) { $bid=$this->db->escape($bid); return $this->db->query("SELECT su.*,s.TenDichVu,s.ChiPhiDichVu FROM hotelservice_servicesused su JOIN hotelservice_services s ON su.MaDichVu=s.MaDichVu WHERE su.MaDatPhong='$bid' ORDER BY su.NgaySuDung DESC")->rows; }
    public function add($bid,$d) {
        $bid=$this->db->escape($bid);$dv=$this->db->escape($d['MaDichVu']);$sl=$d['SoLuong']??1;
        $svc=$this->db->query("SELECT * FROM hotelservice_services WHERE MaDichVu='$dv'");
        if (!$svc->row) return null;
        $id='SD'.date('YmdHis').rand(100,999);$gia=$svc->row['ChiPhiDichVu'];$tt=$gia*$sl;
        $this->db->query("INSERT INTO hotelservice_servicesused(MaDichVuSuDung,MaDatPhong,MaDichVu,SoLuong,DonGia,ThanhTien) VALUES('$id','$bid','$dv',$sl,$gia,$tt)");
        return $this->db->query("SELECT su.*,s.TenDichVu FROM hotelservice_servicesused su JOIN hotelservice_services s ON su.MaDichVu=s.MaDichVu WHERE su.MaDichVuSuDung='$id'")->row;
    }
    public function remove($id) { $id=$this->db->escape($id); $this->db->query("DELETE FROM hotelservice_servicesused WHERE MaDichVuSuDung='$id'"); }
}
