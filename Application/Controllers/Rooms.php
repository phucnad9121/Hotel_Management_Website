<?php

class ControllersRooms extends ApiController {
    public function index() {
        $rooms = $this->model('Rooms')->getAll();
        $this->send(200, 'Rooms retrieved successfully', $rooms);
    }

    public function show($params) {
        $id = $params['id'] ?? '';
        if ($id === '') {
            $this->send(400, 'Room ID is required');
            return;
        }

        $room = $this->model('Rooms')->getById($id);
        if (!$room) {
            $this->send(404, 'Room not found');
            return;
        }

        $this->send(200, 'Room retrieved successfully', $room);
    }

    public function available() {
        $roomType = $this->request->query('type', '');
        $rooms = $this->model('Rooms')->getAvailable($roomType);
        $this->send(200, 'Available rooms retrieved successfully', $rooms);
    }

    public function store() {
        if (!$this->authorize(['admin'])) {
            return;
        }

        $data = $this->request->all();
        if (!$this->validateRequired($data, ['MaPhong', 'SoPhong', 'MaLoaiPhong'])) {
            return;
        }

        $model = $this->model('Rooms');
        if ($model->exists($data['MaPhong'])) {
            $this->send(409, 'Room ID already exists');
            return;
        }

        $created = $model->create($data);
        if (!$created) {
            $this->send(500, 'Failed to create room');
            return;
        }

        $this->send(201, 'Room created successfully', $created);
    }

    public function update($params) {
        if (!$this->authorize(['admin'])) {
            return;
        }

        $id = $params['id'] ?? '';
        if ($id === '') {
            $this->send(400, 'Room ID is required');
            return;
        }

        $model = $this->model('Rooms');
        if (!$model->exists($id)) {
            $this->send(404, 'Room not found');
            return;
        }

        $data = $this->request->all();
        $updated = $model->updateById($id, $data);
        if (!$updated) {
            $this->send(500, 'Failed to update room');
            return;
        }

        $this->send(200, 'Room updated successfully', $updated);
    }

    public function destroy($params) {
        if (!$this->authorize(['admin'])) {
            return;
        }

        $id = $params['id'] ?? '';
        if ($id === '') {
            $this->send(400, 'Room ID is required');
            return;
        }

        $result = $this->model('Rooms')->deleteById($id);
        if (($result['success'] ?? false) === true) {
            $this->send(200, 'Room deleted successfully');
            return;
        }

        $reason = $result['reason'] ?? 'db_error';
        if ($reason === 'not_found') {
            $this->send(404, 'Room not found');
            return;
        }
        if ($reason === 'in_use') {
            $this->send(409, 'Cannot delete room that is assigned to bookings');
            return;
        }

        $this->send(500, 'Failed to delete room');
    }
}
