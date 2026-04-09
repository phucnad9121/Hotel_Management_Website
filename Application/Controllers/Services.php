<?php

class ControllersServices extends ApiController {
    public function index() {
        $services = $this->model('Services')->getAll();
        $this->send(200, 'Services retrieved successfully', $services);
    }

    public function show($params) {
        $id = $params['id'] ?? '';
        if ($id === '') {
            $this->send(400, 'Service ID is required');
            return;
        }

        $service = $this->model('Services')->getById($id);
        if (!$service) {
            $this->send(404, 'Service not found');
            return;
        }

        $this->send(200, 'Service retrieved successfully', $service);
    }

    public function store() {
        if (!$this->authorize(['admin', 'employee'])) {
            return;
        }

        $data = $this->request->all();
        if (!$this->validateRequired($data, ['TenDichVu', 'ChiPhiDichVu'])) {
            return;
        }

        $model = $this->model('Services');
        if (isset($data['MaDichVu']) && $data['MaDichVu'] !== '' && $model->exists($data['MaDichVu'])) {
            $this->send(409, 'Service ID already exists');
            return;
        }

        if ($model->nameExists($data['TenDichVu'])) {
            $this->send(409, 'Service name already exists');
            return;
        }

        $created = $model->create($data);
        if (!$created) {
            $this->send(500, 'Failed to create service');
            return;
        }

        $this->send(201, 'Service created successfully', $created);
    }

    public function update($params) {
        if (!$this->authorize(['admin', 'employee'])) {
            return;
        }

        $id = $params['id'] ?? '';
        if ($id === '') {
            $this->send(400, 'Service ID is required');
            return;
        }

        $model = $this->model('Services');
        $current = $model->getById($id);
        if (!$current) {
            $this->send(404, 'Service not found');
            return;
        }

        $data = $this->request->all();
        if (isset($data['TenDichVu']) && $model->nameExists($data['TenDichVu'], $id)) {
            $this->send(409, 'Service name already exists');
            return;
        }

        $updated = $model->updateById($id, $data);
        if (!$updated) {
            $this->send(500, 'Failed to update service');
            return;
        }

        $this->send(200, 'Service updated successfully', $updated);
    }

    public function destroy($params) {
        if (!$this->authorize(['admin'])) {
            return;
        }

        $id = $params['id'] ?? '';
        if ($id === '') {
            $this->send(400, 'Service ID is required');
            return;
        }

        $result = $this->model('Services')->deleteById($id);
        if (($result['success'] ?? false) === true) {
            $this->send(200, 'Service deleted successfully');
            return;
        }

        $reason = $result['reason'] ?? 'db_error';
        if ($reason === 'not_found') {
            $this->send(404, 'Service not found');
            return;
        }
        if ($reason === 'in_use') {
            $this->send(409, 'Cannot delete service that is in use');
            return;
        }

        $this->send(500, 'Failed to delete service');
    }
}
