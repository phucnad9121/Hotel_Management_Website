<?php

class ControllersAuth extends ApiController {
    public function index() {
        $this->send(200, 'Auth service is ready');
    }

    public function login() {
        $data = $this->request->all();
        if (!$this->validateRequired($data, ['username', 'password'])) {
            return;
        }

        $user = $this->model('Auth')->authenticate($data['username'], $data['password']);
        if (!$user) {
            $this->send(401, 'Invalid username or password');
            return;
        }

        $token = JWT::encode([
            'user_id' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role'],
            'display_name' => $user['display_name'],
            'email' => $user['email'],
        ]);

        $this->send(200, 'Login successful', [
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
            'user' => $user,
        ]);
    }

    public function register() {
        $data = $this->request->all();
        if (!$this->validateRequired($data, ['username', 'password'])) {
            return;
        }

        $model = $this->model('Auth');
        if ($model->usernameExists($data['username'])) {
            $this->send(409, 'Username already exists');
            return;
        }

        $created = $model->createAccount($data['username'], $data['password']);
        if (!$created) {
            $this->send(500, 'Failed to create account');
            return;
        }

        $token = JWT::encode([
            'user_id' => $created['id'],
            'username' => $created['username'],
            'role' => $created['role'],
            'display_name' => $created['display_name'],
            'email' => $created['email'],
        ]);

        $this->send(201, 'Account created successfully', [
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
            'user' => $created,
        ]);
    }

    public function refresh() {
        $token = $this->request->bearerToken();
        if (!$token) {
            $this->send(401, 'Token is required');
            return;
        }

        $newToken = JWT::refresh($token);
        if (!$newToken) {
            $this->send(401, 'Invalid or expired token');
            return;
        }

        $this->send(200, 'Token refreshed successfully', [
            'token' => $newToken,
            'token_type' => 'Bearer',
            'expires_in' => 3600,
        ]);
    }

    public function me() {
        $user = $this->authorize([]);
        if (!$user) {
            return;
        }

        $this->send(200, 'Current user retrieved successfully', $user);
    }
}
