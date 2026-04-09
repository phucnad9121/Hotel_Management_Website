<?php

// Health check
$router->get('/api/health', function ($request, $response) {
    $response->json([
        'success' => true,
        'message' => 'OK',
        'data' => [
            'time' => date('c'),
        ],
    ]);
});

$router->get('/api', function ($request, $response) {
    $response->json([
        'success' => true,
        'message' => 'Hotel Management REST API is running',
        'data' => [
            'version' => '1.0.0',
        ],
    ]);
});

// Auth
$router->post('/api/auth/login', 'Auth@login');
$router->post('/api/auth/register', 'Auth@register');
$router->post('/api/auth/refresh', 'Auth@refresh');
$router->get('/api/auth/me', 'Auth@me');

// Services
$router->get('/api/services', 'Services@index');
$router->get('/api/services/{id}', 'Services@show');
$router->post('/api/services', 'Services@store');
$router->put('/api/services/{id}', 'Services@update');
$router->delete('/api/services/{id}', 'Services@destroy');

// Rooms
$router->get('/api/rooms', 'Rooms@index');
$router->get('/api/rooms/available', 'Rooms@available');
$router->get('/api/rooms/{id}', 'Rooms@show');
$router->post('/api/rooms', 'Rooms@store');
$router->put('/api/rooms/{id}', 'Rooms@update');
$router->delete('/api/rooms/{id}', 'Rooms@destroy');

// Bookings
$router->get('/api/bookings', 'Bookings@index');
$router->get('/api/bookings/{id}', 'Bookings@show');
$router->post('/api/bookings', 'Bookings@store');
$router->put('/api/bookings/{id}', 'Bookings@update');
$router->delete('/api/bookings/{id}', 'Bookings@destroy');

$router->post('/api/bookings/{id}/confirm', 'Bookings@confirm');
$router->post('/api/bookings/{id}/checkin', 'Bookings@checkin');
$router->post('/api/bookings/{id}/cancel', 'Bookings@cancel');
