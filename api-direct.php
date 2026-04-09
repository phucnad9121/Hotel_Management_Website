<?php
/**
 * Direct API Test - No .htaccess needed
 * Access: http://localhost/Hotel_Management_Website/api-direct.php/health
 */

// Set JSON header FIRST
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// Parse URL manually from PATH_INFO
$pathInfo = $_SERVER['PATH_INFO'] ?? $_SERVER['ORIG_PATH_INFO'] ?? '';

// Remove leading slash
$path = ltrim($pathInfo, '/');

// Simple routing
switch ($path) {
    case 'health':
    case '':
        echo json_encode([
            'success' => true,
            'message' => 'API is working!',
            'timestamp' => date('c'),
            'path_info' => $path,
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        break;

    case 'auth/login':
        // Simple login test
        $input = json_decode(file_get_contents('php://input'), true);
        if ($input && isset($input['username']) && isset($input['password'])) {
            echo json_encode([
                'success' => true,
                'message' => 'Login test successful',
                'data' => [
                    'token' => 'test-token-12345',
                    'username' => $input['username'],
                ],
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } else {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Please send username and password in JSON body',
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Route not found: ' . $path,
            'available_routes' => ['health', 'auth/login'],
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        break;
}
