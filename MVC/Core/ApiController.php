<?php

class ApiController {
    protected $request;
    protected $response;

    public function __construct($request = null, $response = null) {
        $this->request = $request ?: ($GLOBALS['api_request'] ?? null);
        $this->response = $response ?: ($GLOBALS['api_response'] ?? null);
    }

    protected function model($name) {
        $file = __DIR__ . '/../../Application/Models/' . $name . '.php';
        if (!file_exists($file)) {
            throw new Exception('Model not found: ' . $name);
        }

        require_once $file;
        $class = 'Models' . $name;
        if (!class_exists($class)) {
            throw new Exception('Model class not found: ' . $class);
        }

        return new $class();
    }

    protected function send($statusCode, $message, $data = null, $errors = null) {
        $payload = [
            'success' => ((int) $statusCode) < 400,
            'message' => $message,
        ];

        if ($data !== null) {
            $payload['data'] = $data;
        }

        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        $this->response->json($payload, (int) $statusCode);
    }

    protected function validateRequired(array $data, array $fields) {
        $missing = [];

        foreach ($fields as $field) {
            if (!array_key_exists($field, $data) || trim((string) $data[$field]) === '') {
                $missing[] = $field;
            }
        }

        if (!empty($missing)) {
            $this->send(422, 'Missing required fields', null, ['missing_fields' => $missing]);
            return false;
        }

        return true;
    }

    protected function user() {
        $token = $this->request->bearerToken();
        if (!$token) {
            return null;
        }

        $decoded = JWT::decode($token);
        if (!$decoded) {
            return null;
        }

        return (array) $decoded;
    }

    protected function authorize(array $roles = []) {
        $user = $this->user();
        if (!$user) {
            $this->send(401, 'Unauthorized. Token is missing or invalid.');
            return null;
        }

        if (!empty($roles) && !in_array(($user['role'] ?? ''), $roles, true)) {
            $this->send(403, 'Forbidden. You do not have permission to access this resource.');
            return null;
        }

        return $user;
    }
}
