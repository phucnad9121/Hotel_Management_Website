<?php
/**
 * Main Entry Point - Handles both REST API and MVC web app
 */

// Check if this is an API request
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$isApiRequest = (stripos($requestUri, '/api') !== false);

if ($isApiRequest) {
    // === REST API MODE ===
    require_once __DIR__ . '/config.php';
    require_once SYSTEM . 'Startup.php';
    
    $request = new Http\Request();
    $response = new Http\Response();
    $response->setHeader('Access-Control-Allow-Origin: *');
    $response->setHeader("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    $response->setHeader('Content-Type: application/json; charset=UTF-8');
    $router = new Router\Router($request->getUrl(), $request->getMethod());
    require_once __DIR__ . '/Router/Router.php';
    $router->run();
    $response->render();
    exit;
}

// === MVC WEB APP MODE (old system) ===
require_once "bridge.php";
$app = new App();
