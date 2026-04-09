<?php
/**
 * REST API Entry Point
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set JSON header immediately
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

try {
    require_once __DIR__ . '/MVC/Core/connectDB.php';
    require_once __DIR__ . '/MVC/Core/Request.php';
    require_once __DIR__ . '/MVC/Core/Response.php';
    require_once __DIR__ . '/MVC/Core/JWT.php';
    require_once __DIR__ . '/MVC/Core/ApiModel.php';
    require_once __DIR__ . '/MVC/Core/ApiController.php';
    require_once __DIR__ . '/MVC/Core/ApiRouter.php';

    $request = new Request();
    $response = new Response();

    $GLOBALS['api_request'] = $request;
    $GLOBALS['api_response'] = $response;

    $router = new ApiRouter($request, $response);
    require_once __DIR__ . '/MVC/Core/routes.php';
    $router->run();
    $response->send();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'API Error: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
    ], JSON_UNESCAPED_UNICODE);
}
