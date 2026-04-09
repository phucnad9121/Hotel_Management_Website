<?php
/**
 * Quick API Test - Bypass .htaccess
 * Access: http://localhost/Hotel_Management_Website/test-api-direct.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set content type to JSON
header('Content-Type: application/json; charset=utf-8');

// Test 1: Check if files exist
$files = [
    'api.php' => './api.php',
    'Request.php' => './MVC/Core/Request.php',
    'Response.php' => './MVC/Core/Response.php',
    'ApiRouter.php' => './MVC/Core/ApiRouter.php',
    'routes.php' => './MVC/Core/routes.php',
    'Auth.php' => './Application/Controllers/Auth.php',
    'Auth Model.php' => './Application/Models/Auth.php',
];

$filesExist = [];
foreach ($files as $name => $path) {
    $filesExist[$name] = file_exists($path);
}

// Test 2: Try to call login directly
$loginTest = null;
try {
    require_once './MVC/Core/connectDB.php';
    $db = new connectDB();
    $dbOk = true;
    $dbError = null;
} catch (Exception $e) {
    $dbOk = false;
    $dbError = $e->getMessage();
}

// Test 3: Check if admin exists
$adminExists = null;
if ($dbOk) {
    try {
        $result = $db->select("SELECT MaDangNhap, TenDangNhap FROM authentication_admin LIMIT 1");
        $adminExists = !empty($result);
    } catch (Exception $e) {
        $adminExists = false;
    }
}

// Output results
echo json_encode([
    'test' => 'API System Check',
    'timestamp' => date('c'),
    'files' => $filesExist,
    'database' => [
        'connected' => $dbOk,
        'error' => $dbError,
        'admin_exists' => $adminExists,
    ],
    'instructions' => [
        'If all files exist and DB connected -> API should work',
        'Test URL: http://localhost/Hotel_Management_Website/api.php/api/health',
        'If that works -> .htaccess is the issue',
        'If that fails -> Check error logs',
    ],
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
