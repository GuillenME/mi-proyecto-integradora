<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisterPassengerController;
use App\Http\Controllers\TaxiDriverController;
use App\Http\Controllers\VehicleController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('/user')->group(function(){
    Route::post('/login', 'LoginController@login');
});

// Rutas para el registro de pasajeros
Route::prefix('/passenger')->group(function(){
    Route::post('/register', [RegisterPassengerController::class, 'register']);
    Route::post('/login', [RegisterPassengerController::class, 'login']);
    Route::post('/logout', [RegisterPassengerController::class, 'logout']);
    Route::get('/users', [RegisterPassengerController::class, 'index']);
});

// Rutas para taxistas
Route::prefix('/taxi-driver')->group(function(){
    Route::post('/register', [TaxiDriverController::class, 'register']);
    Route::post('/login', [TaxiDriverController::class, 'login']);
    Route::post('/logout', [TaxiDriverController::class, 'logout']);
    Route::get('/', [TaxiDriverController::class, 'index']);
    Route::get('/{id}', [TaxiDriverController::class, 'show']);
    Route::put('/{id}', [TaxiDriverController::class, 'update']);
    Route::delete('/{id}', [TaxiDriverController::class, 'destroy']);

    // Gestión de vehículos para taxistas
    Route::post('/assign-vehicle', [TaxiDriverController::class, 'assignVehicle']);
    Route::post('/remove-vehicle', [TaxiDriverController::class, 'removeVehicle']);
});

// Rutas para vehículos
Route::prefix('/vehicle')->group(function(){
    Route::get('/', [VehicleController::class, 'index']);
    Route::get('/available', [VehicleController::class, 'getAvailableVehicles']);
    Route::get('/assigned', [VehicleController::class, 'getAssignedVehicles']);
    Route::get('/{id}', [VehicleController::class, 'show']);
    Route::post('/', [VehicleController::class, 'store']);
    Route::put('/{id}', [VehicleController::class, 'update']);
    Route::delete('/{id}', [VehicleController::class, 'destroy']);
});
