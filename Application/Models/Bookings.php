<?php

class ModelsBookings extends ApiModel {
    public function getAll() {
        return $this->fetchAll(
            "SELECT b.*,
                    g.HoKhachHang,
                    g.TenKhachHang,
                    g.SoDienThoaiKhachHang,
                    g.EmailKhachHang
             FROM bookings_booking b
             JOIN hotels_guests g ON b.MaKhachHang = g.MaKhachHang
             ORDER BY b.NgayTao DESC, b.NgayDatPhong DESC"
        );
    }

    public function getById($id) {
        $id = $this->escape($id);
        return $this->fetchOne(
            "SELECT b.*,
                    g.HoKhachHang,
                    g.TenKhachHang,
                    g.SoDienThoaiKhachHang,
                    g.EmailKhachHang,
                    (SELECT GROUP_CONCAT(r.SoPhong SEPARATOR ', ')
                     FROM rooms_roombooked rb
                     JOIN rooms_room r ON rb.MaPhong = r.MaPhong
                     WHERE rb.MaDatPhong = b.MaDatPhong) AS SoPhong
             FROM bookings_booking b
             JOIN hotels_guests g ON b.MaKhachHang = g.MaKhachHang
             WHERE b.MaDatPhong = '{$id}'"
        );
    }

    public function roomTypeExists($roomTypeId) {
        $roomTypeId = $this->escape($roomTypeId);
        $row = $this->fetchOne(
            "SELECT MaLoaiPhong FROM rooms_roomtype WHERE MaLoaiPhong = '{$roomTypeId}'"
        );
        return !empty($row);
    }

    public function countAvailableRoomsByType($roomTypeId) {
        $roomTypeId = $this->escape($roomTypeId);
        $row = $this->fetchOne(
            "SELECT COUNT(*) AS total
             FROM rooms_room r
             WHERE r.MaLoaiPhong = '{$roomTypeId}'
               AND r.KhaDung = 'Yes'
               AND r.MaPhong NOT IN (
                   SELECT rb.MaPhong
                   FROM rooms_roombooked rb
                   JOIN bookings_booking bb ON rb.MaDatPhong = bb.MaDatPhong
                   WHERE bb.TrangThai IN ('Confirmed', 'Checkin')
               )"
        );

        return (int) ($row['total'] ?? 0);
    }

    public function getRoomTypePrice($roomTypeId) {
        $roomTypeId = $this->escape($roomTypeId);
        $row = $this->fetchOne(
            "SELECT GiaPhong FROM rooms_roomtype WHERE MaLoaiPhong = '{$roomTypeId}'"
        );
        return $row ? (float) $row['GiaPhong'] : null;
    }

    public function create(array $data) {
        $bookingId = $this->escape($data['MaDatPhong'] ?? $this->createId('DP'));
        $bookingDate = $this->escape($data['NgayDatPhong'] ?? date('Y-m-d'));
        $stayDays = (int) ($data['ThoiGianLuuTru'] ?? 0);
        $checkin = $this->escape($data['NgayNhanPhong'] ?? '');
        $checkout = $this->escape($data['NgayTraPhong'] ?? '');
        $deposit = (float) ($data['SoTienDatPhong'] ?? 0);
        $guestId = $this->escape($data['MaKhachHang'] ?? '');
        $note = $this->escape($data['GhiChu'] ?? '');
        $status = $this->escape($data['TrangThai'] ?? 'Pending');

        $sql = "INSERT INTO bookings_booking
                (MaDatPhong, NgayDatPhong, ThoiGianLuuTru, NgayNhanPhong, NgayTraPhong,
                 SoTienDatPhong, MaKhachHang, GhiChu, TrangThai)
                VALUES
                ('{$bookingId}', '{$bookingDate}', {$stayDays}, '{$checkin}', '{$checkout}',
                 {$deposit}, '{$guestId}', '{$note}', '{$status}')";
        $ok = $this->run($sql);
        if (!$ok) {
            return null;
        }

        return $this->getById($bookingId);
    }

    public function updateById($id, array $data) {
        $current = $this->getById($id);
        if (!$current) {
            return null;
        }

        $allowed = [
            'NgayNhanPhong',
            'NgayTraPhong',
            'GhiChu',
            'TrangThai',
            'SoTienDatPhong',
            'ThoiGianLuuTru',
        ];

        $sets = [];
        foreach ($allowed as $field) {
            if (!array_key_exists($field, $data)) {
                continue;
            }

            if (in_array($field, ['SoTienDatPhong'], true)) {
                $value = (float) $data[$field];
                $sets[] = "{$field} = {$value}";
                continue;
            }

            if (in_array($field, ['ThoiGianLuuTru'], true)) {
                $value = (int) $data[$field];
                $sets[] = "{$field} = {$value}";
                continue;
            }

            $value = $this->escape($data[$field]);
            $sets[] = "{$field} = '{$value}'";
        }

        if (!empty($sets)) {
            $idEscaped = $this->escape($id);
            $sql = "UPDATE bookings_booking SET " . implode(', ', $sets) . " WHERE MaDatPhong = '{$idEscaped}'";
            $this->run($sql);
        }

        return $this->getById($id);
    }

    public function updateStatus($id, $status) {
        $id = $this->escape($id);
        $status = $this->escape($status);
        return $this->run("UPDATE bookings_booking SET TrangThai = '{$status}' WHERE MaDatPhong = '{$id}'");
    }

    public function assignRoom($bookingId, $roomId) {
        $bookingId = $this->escape($bookingId);
        $roomId = $this->escape($roomId);
        $assignedId = $this->createId('RB');

        $sql = "INSERT INTO rooms_roombooked (MaPhongDaDat, MaDatPhong, MaPhong)
                VALUES ('{$assignedId}', '{$bookingId}', '{$roomId}')";
        return $this->run($sql);
    }

    public function getAssignedRoomIds($bookingId) {
        $bookingId = $this->escape($bookingId);
        $rows = $this->fetchAll(
            "SELECT MaPhong FROM rooms_roombooked WHERE MaDatPhong = '{$bookingId}'"
        );

        $ids = [];
        foreach ($rows as $row) {
            $ids[] = $row['MaPhong'];
        }
        return $ids;
    }

    public function removeAssignedRooms($bookingId) {
        $bookingId = $this->escape($bookingId);
        return $this->run("DELETE FROM rooms_roombooked WHERE MaDatPhong = '{$bookingId}'");
    }

    public function setRoomAvailability($roomId, $status) {
        $roomId = $this->escape($roomId);
        $status = $this->escape($status);
        return $this->run("UPDATE rooms_room SET KhaDung = '{$status}' WHERE MaPhong = '{$roomId}'");
    }
}
