<?php

class ModelsServices extends ApiModel {
    public function getAll() {
        return $this->fetchAll(
            "SELECT * FROM hotelservice_services ORDER BY TenDichVu ASC"
        );
    }

    public function getById($id) {
        $id = $this->escape($id);
        return $this->fetchOne(
            "SELECT * FROM hotelservice_services WHERE MaDichVu = '{$id}'"
        );
    }

    public function exists($id) {
        return !empty($this->getById($id));
    }

    public function nameExists($name, $excludeId = null) {
        $name = $this->escape($name);
        $sql = "SELECT MaDichVu FROM hotelservice_services WHERE TenDichVu = '{$name}'";
        if ($excludeId !== null && $excludeId !== '') {
            $excludeId = $this->escape($excludeId);
            $sql .= " AND MaDichVu <> '{$excludeId}'";
        }

        return !empty($this->fetchOne($sql));
    }

    public function create(array $data) {
        $id = trim((string) ($data['MaDichVu'] ?? ''));
        if ($id === '') {
            $id = $this->createId('DV');
        }

        $name = $this->escape($data['TenDichVu'] ?? '');
        $description = $this->escape($data['MoTaDichVu'] ?? '');
        $price = (float) ($data['ChiPhiDichVu'] ?? 0);
        $id = $this->escape($id);

        $sql = "INSERT INTO hotelservice_services (MaDichVu, TenDichVu, MoTaDichVu, ChiPhiDichVu)
                VALUES ('{$id}', '{$name}', '{$description}', {$price})";
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

        $id = $this->escape($id);
        $name = $this->escape($data['TenDichVu'] ?? $current['TenDichVu']);
        $description = $this->escape($data['MoTaDichVu'] ?? $current['MoTaDichVu']);
        $price = (float) ($data['ChiPhiDichVu'] ?? $current['ChiPhiDichVu']);

        $sql = "UPDATE hotelservice_services
                SET TenDichVu = '{$name}',
                    MoTaDichVu = '{$description}',
                    ChiPhiDichVu = {$price}
                WHERE MaDichVu = '{$id}'";
        $this->run($sql);

        return $this->getById($id);
    }

    public function deleteById($id) {
        $id = $this->escape($id);
        $inUse = $this->fetchOne(
            "SELECT COUNT(*) AS total FROM hotelservice_servicesused WHERE MaDichVu = '{$id}'"
        );

        if ((int) ($inUse['total'] ?? 0) > 0) {
            return ['success' => false, 'reason' => 'in_use'];
        }

        $ok = $this->run("DELETE FROM hotelservice_services WHERE MaDichVu = '{$id}'");
        if (!$ok) {
            return ['success' => false, 'reason' => 'db_error'];
        }

        if ($this->affectedRows() <= 0) {
            return ['success' => false, 'reason' => 'not_found'];
        }

        return ['success' => true];
    }
}
