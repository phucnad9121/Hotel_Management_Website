<?php

class ControllersBookings extends ApiController {
    public function index() {
        if (!$this->authorize(['admin', 'employee'])) {
            return;
        }

        $bookings = $this->model('Bookings')->getAll();
        $this->send(200, 'Bookings retrieved successfully', $bookings);
    }

    public function show($params) {
        $id = $params['id'] ?? '';
        if ($id === '') {
            $this->send(400, 'Booking ID is required');
            return;
        }

        $booking = $this->model('Bookings')->getById($id);
        if (!$booking) {
            $this->send(404, 'Booking not found');
            return;
        }

        $this->send(200, 'Booking retrieved successfully', $booking);
    }

    public function store() {
        $data = $this->request->all();
        if (!$this->validateRequired($data, ['MaKhachHang', 'NgayNhanPhong', 'NgayTraPhong', 'MaLoaiPhong'])) {
            return;
        }

        $model = $this->model('Bookings');

        if (!$model->roomTypeExists($data['MaLoaiPhong'])) {
            $this->send(404, 'Room type not found');
            return;
        }

        $checkinDate = DateTime::createFromFormat('Y-m-d', (string) $data['NgayNhanPhong']);
        $checkoutDate = DateTime::createFromFormat('Y-m-d', (string) $data['NgayTraPhong']);

        if (!$checkinDate || !$checkoutDate) {
            $this->send(422, 'Invalid date format. Expected Y-m-d');
            return;
        }

        $nights = (int) $checkinDate->diff($checkoutDate)->days;
        if ($checkoutDate <= $checkinDate || $nights <= 0) {
            $this->send(422, 'Check-out date must be later than check-in date');
            return;
        }

        if ($model->countAvailableRoomsByType($data['MaLoaiPhong']) <= 0) {
            $this->send(409, 'No available rooms for this room type');
            return;
        }

        $price = $model->getRoomTypePrice($data['MaLoaiPhong']);
        if ($price === null) {
            $this->send(404, 'Room type price not found');
            return;
        }

        $payload = [
            'NgayDatPhong' => date('Y-m-d'),
            'ThoiGianLuuTru' => $nights,
            'NgayNhanPhong' => $data['NgayNhanPhong'],
            'NgayTraPhong' => $data['NgayTraPhong'],
            'SoTienDatPhong' => $price * $nights,
            'MaKhachHang' => $data['MaKhachHang'],
            'GhiChu' => $data['GhiChu'] ?? ('ROOMTYPE:' . $data['MaLoaiPhong']),
            'TrangThai' => 'Pending',
        ];

        $created = $model->create($payload);
        if (!$created) {
            $this->send(500, 'Failed to create booking');
            return;
        }

        $this->send(201, 'Booking created successfully', $created);
    }

    public function update($params) {
        if (!$this->authorize(['admin', 'employee'])) {
            return;
        }

        $id = $params['id'] ?? '';
        if ($id === '') {
            $this->send(400, 'Booking ID is required');
            return;
        }

        $model = $this->model('Bookings');
        if (!$model->getById($id)) {
            $this->send(404, 'Booking not found');
            return;
        }

        $data = $this->request->all();
        $updated = $model->updateById($id, $data);
        if (!$updated) {
            $this->send(500, 'Failed to update booking');
            return;
        }

        $this->send(200, 'Booking updated successfully', $updated);
    }

    public function confirm($params) {
        if (!$this->authorize(['admin', 'employee'])) {
            return;
        }

        $id = $params['id'] ?? '';
        if ($id === '') {
            $this->send(400, 'Booking ID is required');
            return;
        }

        $data = $this->request->all();
        if (!$this->validateRequired($data, ['MaPhong'])) {
            return;
        }

        $bookingModel = $this->model('Bookings');
        $roomsModel = $this->model('Rooms');
        $booking = $bookingModel->getById($id);
        if (!$booking) {
            $this->send(404, 'Booking not found');
            return;
        }

        if (($booking['TrangThai'] ?? '') !== 'Pending') {
            $this->send(409, 'Only pending bookings can be confirmed');
            return;
        }

        $roomId = $data['MaPhong'];
        if (!$roomsModel->isRoomAssignable($roomId)) {
            $this->send(409, 'Room is not available for assignment');
            return;
        }

        $bookingModel->assignRoom($id, $roomId);
        $bookingModel->updateStatus($id, 'Confirmed');
        $bookingModel->setRoomAvailability($roomId, 'No');

        $this->send(200, 'Booking confirmed successfully', $bookingModel->getById($id));
    }

    public function checkin($params) {
        if (!$this->authorize(['admin', 'employee'])) {
            return;
        }

        $id = $params['id'] ?? '';
        if ($id === '') {
            $this->send(400, 'Booking ID is required');
            return;
        }

        $model = $this->model('Bookings');
        $booking = $model->getById($id);
        if (!$booking) {
            $this->send(404, 'Booking not found');
            return;
        }

        if (($booking['TrangThai'] ?? '') !== 'Confirmed') {
            $this->send(409, 'Booking must be confirmed before check-in');
            return;
        }

        $model->updateStatus($id, 'Checkin');
        $this->send(200, 'Check-in successful', $model->getById($id));
    }

    public function cancel($params) {
        $user = $this->authorize(['admin', 'employee', 'guest']);
        if (!$user) {
            return;
        }

        $id = $params['id'] ?? '';
        if ($id === '') {
            $this->send(400, 'Booking ID is required');
            return;
        }

        $model = $this->model('Bookings');
        $booking = $model->getById($id);
        if (!$booking) {
            $this->send(404, 'Booking not found');
            return;
        }

        $assignedRoomIds = $model->getAssignedRoomIds($id);
        foreach ($assignedRoomIds as $roomId) {
            $model->setRoomAvailability($roomId, 'Yes');
        }
        $model->removeAssignedRooms($id);

        $model->updateStatus($id, 'Cancelled');
        $this->send(200, 'Booking cancelled successfully', $model->getById($id));
    }

    public function destroy($params) {
        $this->cancel($params);
    }
}
