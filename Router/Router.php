<?php
// Health
$router->get('/api', function() {
    echo json_encode(['success' => true, 'message' => 'Hotel REST API is running', 'version' => '1.0']);
});
$router->get('/api/health', function() {
    echo json_encode(['success' => true, 'message' => 'OK', 'data' => ['time' => date('c')]]);
});

// Auth
$router->post('/api/auth/login', 'auth@login');
$router->post('/api/auth/register', 'auth@register');
$router->post('/api/auth/refresh', 'auth@refresh');
$router->get('/api/auth/me', 'auth@me');

// Services
$router->get('/api/services', 'services@index');
$router->get('/api/services/:id', 'services@show');
$router->post('/api/services', 'services@store');
$router->put('/api/services/:id', 'services@update');
$router->delete('/api/services/:id', 'services@destroy');

// Rooms
$router->get('/api/rooms', 'rooms@index');
$router->get('/api/rooms/available/:type', 'rooms@available');
$router->get('/api/rooms/:id', 'rooms@show');
$router->post('/api/rooms', 'rooms@store');
$router->put('/api/rooms/:id', 'rooms@update');
$router->delete('/api/rooms/:id', 'rooms@destroy');

// Room Types
$router->get('/api/room-types', 'roomtypes@index');
$router->get('/api/room-types/:id', 'roomtypes@show');
$router->post('/api/room-types', 'roomtypes@store');
$router->put('/api/room-types/:id', 'roomtypes@update');
$router->delete('/api/room-types/:id', 'roomtypes@destroy');

// Bookings
$router->get('/api/bookings', 'bookings@index');
$router->get('/api/bookings/:id', 'bookings@show');
$router->post('/api/bookings', 'bookings@store');
$router->put('/api/bookings/:id', 'bookings@update');
$router->delete('/api/bookings/:id', 'bookings@destroy');
$router->post('/api/bookings/:id/confirm', 'bookings@confirm');
$router->post('/api/bookings/:id/checkin', 'bookings@checkin');
$router->post('/api/bookings/:id/cancel', 'bookings@cancel');

// Guests
$router->get('/api/guests', 'guests@index');
$router->get('/api/guests/:id', 'guests@show');
$router->post('/api/guests', 'guests@store');
$router->put('/api/guests/:id', 'guests@update');
$router->delete('/api/guests/:id', 'guests@destroy');

// Payments
$router->get('/api/payments', 'payments@index');
$router->get('/api/payments/:id', 'payments@show');
$router->post('/api/payments', 'payments@store');
$router->get('/api/payments/booking/:booking_id', 'payments@byBooking');

// Service Orders
$router->get('/api/bookings/:booking_id/services', 'serviceorders@index');
$router->post('/api/bookings/:booking_id/services', 'serviceorders@store');
$router->delete('/api/bookings/:booking_id/services/:service_id', 'serviceorders@destroy');

// Reports
$router->get('/api/reports/dashboard', 'reports@dashboard');
$router->get('/api/reports/revenue', 'reports@revenue');
$router->get('/api/reports/occupancy', 'reports@occupancy');
