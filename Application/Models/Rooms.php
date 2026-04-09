<?php

class ModelsRooms extends ApiModel {
    public function getAll() {
        return $this->fetchAll(
            "SELECT r.*, rt.TenLoaiPhong, rt.GiaPhong
             FROM rooms_room r
             LEFT JOIN rooms_roomtype rt ON r.MaLoaiPhong = rt.MaLoaiPhong
             ORDER BY r.SoPhong ASC"
        );
    }

    public function getById($id) {
        $id = $this->escape($id);
        return $this->fetchOne(
            "SELECT r.*, rt.TenLoaiPhong, rt.GiaPhong
             FROM rooms_room r
             LEFT JOIN rooms_roomtype rt ON r.MaLoaiPhong = rt.MaLoaiPhong
             WHERE r.MaPhong = '{$id}'"
        );
    }

    public function getAvailable($type = '') {
        $sql = "SELECT r.*, rt.TenLoaiPhong, rt.GiaPhong
                FROM rooms_room r
                LEFT JOIN rooms_roomtype rt ON r.MaLoaiPhong = rt.MaLoaiPhong
                WHERE r.KhaDung = 'Yes'
                  AND r.MaPhong NOT IN (
                      SELECT rb.MaPhong
                      FROM rooms_roombooked rb
                      JOIN bookings_booking bb ON rb.MaDatPhong = bb.MaDatPhong
                      WHERE bb.TrangThai IN ('Confirmed', 'Checkin')
                  )";

        $type = trim((string) $type);
        if ($type !== '') {
            $type = $this->escape($type);
            $sql .= " AND r.MaLoaiPhong = '{$type}'";
        }

        $sql .= " ORDER BY r.SoPhong ASC";
        return $this->fetchAll($sql);
    }

    public function exists($id) {
        return !empty($this->getById($id));
    }

    public function create(array $data) {
        $id = $this->escape($data['MaPhong'] ?? '');
        $roomNo = $this->escape($data['SoPhong'] ?? '');
        $roomType = $this->escape($data['MaLoaiPhong'] ?? '');
        $available = $this->escape($data['KhaDung'] ?? 'Yes');

        $sql = "INSERT INTO rooms_room (MaPhong, SoPhong, MaLoaiPhong, KhaDung)
                VALUES ('{$id}', '{$roomNo}', '{$roomType}', '{$available}')";
        $ok = $this->run($sql);
        if (!$ok) {
            return null;
        }

        return $this->getById($id);
    }

    public function updateById($id, array $data) {
        $current = $this->getById($id);
        if (!$current) {
            return null;
        }

        $idEscaped = $this->escape($id);
        $roomNo = $this->escape($data['SoPhong'] ?? $current['SoPhong']);
        $roomType = $this->escape($data['MaLoaiPhong'] ?? $current['MaLoaiPhong']);
        $available = $this->escape($data['KhaDung'] ?? $current['KhaDung']);

        $sql = "UPDATE rooms_room
                SET SoPhong = '{$roomNo}',
                    MaLoaiPhong = '{$roomType}',
                    KhaDung = '{$available}'
                WHERE MaPhong = '{$idEscaped}'";
        $this->run($sql);

        return $this->getById($id);
    }

    public function deleteById($id) {
        $id = $this->escape($id);

        $referenced = $this->fetchOne(
            "SELECT COUNT(*) AS total FROM rooms_roombooked WHERE MaPhong = '{$id}'"
        );
        if ((int) ($referenced['total'] ?? 0) > 0) {
            return ['success' => false, 'reason' => 'in_use'];
        }

        $ok = $this->run("DELETE FROM rooms_room WHERE MaPhong = '{$id}'");
        if (!$ok) {
            return ['success' => false, 'reason' => 'db_error'];
        }

        if ($this->affectedRows() <= 0) {
            return ['success' => false, 'reason' => 'not_found'];
        }

        return ['success' => true];
    }

    public function isRoomAssignable($id) {
        $id = $this->escape($id);
        $row = $this->fetchOne(
            "SELECT r.MaPhong
             FROM rooms_room r
             WHERE r.MaPhong = '{$id}'
               AND r.KhaDung = 'Yes'
               AND r.MaPhong NOT IN (
                   SELECT rb.MaPhong
                   FROM rooms_roombooked rb
                   JOIN bookings_booking bb ON rb.MaDatPhong = bb.MaDatPhong
                   WHERE bb.TrangThai IN ('Confirmed', 'Checkin')
               )"
        );

        return !empty($row);
    }
}
