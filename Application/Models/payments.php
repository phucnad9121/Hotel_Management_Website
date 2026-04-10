<?php
use MVC\Model;
class ModelsPayments extends Model {
    public function getAll() { return $this->db->query("SELECT p.*,g.HoKhachHang,g.TenKhachHang FROM payments_payment p JOIN bookings_booking b ON p.MaDatPhong=b.MaDatPhong JOIN hotels_guests g ON b.MaKhachHang=g.MaKhachHang ORDER BY p.NgayThanhToan DESC")->rows; }
    public function getById($id) { $id=$this->db->escape($id); return $this->db->query("SELECT p.*,g.HoKhachHang,g.TenKhachHang FROM payments_payment p JOIN bookings_booking b ON p.MaDatPhong=b.MaDatPhong JOIN hotels_guests g ON b.MaKhachHang=g.MaKhachHang WHERE p.MaThanhToan='$id'")->row; }
    public function getByBooking($bid) { $bid=$this->db->escape($bid); return $this->db->query("SELECT * FROM payments_payment WHERE MaDatPhong='$bid'")->rows; }
    public function create($d) {
        $id='TT'.date('YmdHis').rand(100,999);
        $bp=$this->db->escape($d['MaDatPhong']);$pt=$this->db->escape($d['PhuongThuc']??'Cash');
        $this->db->query("INSERT INTO payments_payment(MaThanhToan,MaDatPhong,TienPhong,TienDichVu,TongTien,PhuongThuc,NgayThanhToan) VALUES('$id','$bp',0,0,0,'$pt',NOW())");
        return $this->getById($id);
    }
}
