<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\EstablishmentController;
use App\Http\Controllers\API\EstablishmentSpecialController;
use App\Http\Controllers\API\ListController;
use App\Http\Controllers\API\SearchHistoryController;

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

// Authentication Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->group(function () {
    // User profile routes
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/me', [AuthController::class, 'update']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Search history routes
    Route::get('/me/searches', [SearchHistoryController::class, 'index']);
    Route::post('/me/searches', [SearchHistoryController::class, 'store']);
    Route::delete('/me/searches/{search}', [SearchHistoryController::class, 'destroy']);
});

// Public Category Routes
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{category}', [CategoryController::class, 'show']);

// Public List Routes for Filters/Forms
Route::get('/lists', [ListController::class, 'index']);
Route::get('/domains', [ListController::class, 'domains']);
Route::get('/grades', [ListController::class, 'grades']);
Route::get('/mentions', [ListController::class, 'mentions']);
Route::get('/labels', [ListController::class, 'labels']);

// Public Establishment Routes
Route::get('/establishments/recent', [EstablishmentSpecialController::class, 'recent']);
Route::get('/establishments', [EstablishmentController::class, 'index']);
Route::get('/establishments/{establishment}', [EstablishmentController::class, 'show']);
Route::get('/map/markers', [EstablishmentSpecialController::class, 'mapMarkers']);
Route::get('/compare', [EstablishmentSpecialController::class, 'compare']);

// Protected Establishment Routes (Admin Only)
Route::middleware(['auth:sanctum', 'role:ROLE_ADMIN'])->group(function () {
    Route::post('/establishments', [EstablishmentController::class, 'store']);
    Route::delete('/establishments/{establishment}', [EstablishmentController::class, 'destroy']);

    // Category management routes (Admin only)
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::put('/categories/{category}', [CategoryController::class, 'update']);
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);
});

// Protected Establishment Routes (Admin or Owner)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::match(['put', 'patch'], '/establishments/{establishment}', [EstablishmentController::class, 'update']);
});
