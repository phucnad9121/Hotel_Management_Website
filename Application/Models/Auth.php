<?php

class ModelsAuth extends ApiModel {
    public function authenticate($username, $password) {
        $username = $this->escape($username);
        $password = $this->escape($password);

        $admin = $this->fetchOne(
            "SELECT * FROM authentication_admin 
             WHERE TenDangNhap = '{$username}' AND MatKhau = '{$password}'"
        );
        if ($admin) {
            return [
                'id' => $admin['MaDangNhap'] ?? $admin['MaAdmin'] ?? $username,
                'username' => $admin['TenDangNhap'] ?? $username,
                'display_name' => 'Administrator',
                'role' => 'admin',
                'email' => $admin['Email'] ?? '',
            ];
        }

        $employee = $this->fetchOne(
            "SELECT l.*, e.HoNhanVien, e.TenNhanVien, e.EmailNhanVien
             FROM authentication_login l
             LEFT JOIN hotels_employees e ON l.MaNhanVien = e.MaNhanVien
             WHERE l.TenDangNhap = '{$username}' AND l.MatKhau = '{$password}'"
        );
        if ($employee) {
            $name = trim(($employee['HoNhanVien'] ?? '') . ' ' . ($employee['TenNhanVien'] ?? ''));
            return [
                'id' => $employee['MaDangNhap'] ?? $username,
                'username' => $employee['TenDangNhap'] ?? $username,
                'display_name' => ($name !== '' ? $name : ($employee['TenDangNhap'] ?? $username)),
                'role' => 'employee',
                'email' => $employee['EmailNhanVien'] ?? '',
            ];
        }

        $guest = $this->fetchOne(
            "SELECT * FROM hotels_guests
             WHERE SoDienThoaiKhachHang = '{$username}' AND MatKhau = '{$password}'"
        );
        if ($guest) {
            $name = trim(($guest['HoKhachHang'] ?? '') . ' ' . ($guest['TenKhachHang'] ?? ''));
            return [
                'id' => $guest['MaKhachHang'] ?? $username,
                'username' => $guest['SoDienThoaiKhachHang'] ?? $username,
                'display_name' => ($name !== '' ? $name : ($guest['SoDienThoaiKhachHang'] ?? $username)),
                'role' => 'guest',
                'email' => $guest['EmailKhachHang'] ?? '',
            ];
        }

        return null;
    }

    public function usernameExists($username) {
        $username = $this->escape($username);
        $row = $this->fetchOne(
            "SELECT MaDangNhap FROM authentication_login WHERE TenDangNhap = '{$username}'"
        );
        return !empty($row);
    }

    public function createAccount($username, $password) {
        $id = $this->createId('AD');
        $username = $this->escape($username);
        $password = $this->escape($password);

        $sql = "INSERT INTO authentication_login (MaDangNhap, TenDangNhap, MatKhau, NguoiDungMoi)
                VALUES ('{$id}', '{$username}', '{$password}', 'Yes')";
        $ok = $this->run($sql);

        if (!$ok) {
            return null;
        }

        return [
            'id' => $id,
            'username' => $username,
            'display_name' => $username,
            'role' => 'employee',
            'email' => '',
        ];
    }
}
