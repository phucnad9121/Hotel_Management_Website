<?php
/**
 * Standalone API Test Endpoint
 * Does NOT load any MVC files - purely for testing
 */

// Set JSON header IMMEDIATELY
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

// Simple health check
echo json_encode([
    'success' => true,
    'message' => 'API is working!',
    'timestamp' => date('c'),
    'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'request_uri' => $_SERVER['REQUEST_URI'] ?? 'Unknown',
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
