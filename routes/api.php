<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\DomainController;
use App\Http\Controllers\API\GradeController;
use App\Http\Controllers\API\MentionController;
use App\Http\Controllers\API\LabelController;
use App\Http\Controllers\API\EstablishmentController;
use App\Http\Controllers\API\EstablishmentSpecialController;
use App\Http\Controllers\API\ListController;
use App\Http\Controllers\API\SearchHistoryController;
use App\Http\Controllers\API\FaqController;
use App\Http\Controllers\API\OfficialDocumentController;
use App\Http\Controllers\API\ContactController;
use App\Http\Controllers\API\AdminStatsController;
use App\Http\Controllers\API\ExportController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\RoleDocumentationController;

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
Route::post('/login', [AuthController::class, 'login']);

// Role Documentation Route (Public)
Route::get('/roles/documentation', [RoleDocumentationController::class, 'documentation']);

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
// Note: Individual entity routes (domains, grades, mentions, labels) are handled by their respective controllers below

// Public FAQ Routes
Route::get('/faq', [FaqController::class, 'index']);
Route::get('/faq/{faqItem}', [FaqController::class, 'show']);

// Public Official Documents Routes
Route::get('/documents', [OfficialDocumentController::class, 'index']);
Route::get('/documents/{document}', [OfficialDocumentController::class, 'show']);

// Public Contact Form
Route::post('/contact', [ContactController::class, 'submit']);

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

    // FAQ management routes (Admin only)
    Route::post('/admin/faq', [FaqController::class, 'store']);
    Route::put('/admin/faq/{faqItem}', [FaqController::class, 'update']);
    Route::delete('/admin/faq/{faqItem}', [FaqController::class, 'destroy']);

    // Admin Statistics routes
    Route::prefix('admin/stats')->group(function () {
        Route::get('/overview', [AdminStatsController::class, 'overview']);
        Route::get('/establishments-by-category', [AdminStatsController::class, 'establishmentsByCategory']);
        Route::get('/geographical-distribution', [AdminStatsController::class, 'geographicalDistribution']);
        Route::get('/habilitations-by-year', [AdminStatsController::class, 'habilitationsByYear']);
        Route::get('/users-by-role', [AdminStatsController::class, 'usersByRole']);
        Route::get('/recent-activity', [AdminStatsController::class, 'recentActivity']);
    });

    // User management routes (Admin only)
    Route::apiResource('users', UserController::class);

    // Export routes (Admin only)
    Route::prefix('admin/export')->group(function () {
        Route::get('/establishments.csv', [ExportController::class, 'establishmentsCsv']);
        Route::get('/establishments.json', [ExportController::class, 'establishmentsJson']);
    });
});

// Protected Establishment Routes (Admin or Owner)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::match(['put', 'patch'], '/establishments/{establishment}', [EstablishmentController::class, 'update']);

    // CRUD routes for list entities (Admin only)
    Route::middleware('role:ROLE_ADMIN')->group(function () {
        // Categories CRUD
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::match(['put', 'patch'], '/categories/{category}', [CategoryController::class, 'update']);
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy']);

        // Domains CRUD
        Route::apiResource('domains', DomainController::class)->except(['index', 'show']);

        // Grades CRUD
        Route::apiResource('grades', GradeController::class)->except(['index', 'show']);

        // Mentions CRUD
        Route::apiResource('mentions', MentionController::class)->except(['index', 'show']);

        // Labels CRUD
        Route::apiResource('labels', LabelController::class)->except(['index', 'show']);
    });
});

// Public endpoints for list entities (index and show)
Route::get('/domains', [DomainController::class, 'index']);
Route::get('/domains/{domain}', [DomainController::class, 'show']);
Route::get('/grades', [GradeController::class, 'index']);
Route::get('/grades/{grade}', [GradeController::class, 'show']);
Route::get('/mentions', [MentionController::class, 'index']);
Route::get('/mentions/{mention}', [MentionController::class, 'show']);
Route::get('/labels', [LabelController::class, 'index']);
Route::get('/labels/{label}', [LabelController::class, 'show']);
