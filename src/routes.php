<?php
// Routes

// Endpoing POST http://localhost:8080/vehicles
// Endpoint GET  http://localhost:8080/vehicles/<MODEL YEAR>/<MANUFACTURER>/<MODEL>
// Endpoing GET  http://localhost:8080/vehicles/<MODEL YEAR>/<MANUFACTURER>/<MODEL>?withRating=true
// Endpoing GET  http://localhost:8080/vehicles/<MODEL YEAR>/<MANUFACTURER>/<MODEL>?withRating=true

$app->get('/vehicles/{modelYear}/{manufacturer}/{model}', App\Controllers\VehicleController::class . ':find');
$app->post('/vehicles', App\Controllers\VehicleController::class . ':find');

//$app->get('/vehicles/{modelYear}/{manufacturer}/{model}', App\Controllers\VehicleController::class . ':hello');
