<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

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

// Health check endpoint (no authentication required)
Route::get('/health', function () {
    return response()->json([
        'success' => true,
        'message' => 'RISDA Odometer API is running',
        'version' => '1.0.0',
        'timestamp' => now()->toIso8601String(),
    ]);
});

// Public routes (require API token only - X-API-Key header)
Route::middleware(['api.token', 'api.cors'])->group(function () {
    
    // Authentication endpoints
    Route::prefix('auth')->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
    });
});

// Protected routes (require API token + user authentication via Sanctum)
Route::middleware(['api.token', 'api.cors', 'auth:sanctum'])->group(function () {
    
    // Authentication endpoints
    Route::prefix('auth')->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    });

    // User Profile Management
    Route::prefix('user')->group(function () {
        Route::post('/profile-picture', [\App\Http\Controllers\Api\UserProfileController::class, 'uploadProfilePicture']);
        Route::delete('/profile-picture', [\App\Http\Controllers\Api\UserProfileController::class, 'deleteProfilePicture']);
        Route::put('/change-password', [\App\Http\Controllers\Api\UserProfileController::class, 'changePassword']);
    });

    // Programs endpoints
    Route::prefix('programs')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\ProgramController::class, 'index']);
        Route::get('/{id}', [\App\Http\Controllers\Api\ProgramController::class, 'show']);
    });
    
    // Driver Logs endpoints (log-pemandu)
    Route::prefix('log-pemandu')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\LogPemanduController::class, 'index']); // Get all logs
        Route::get('/active', [\App\Http\Controllers\Api\LogPemanduController::class, 'getActiveJourney']); // Get active journey
        Route::post('/start', [\App\Http\Controllers\Api\LogPemanduController::class, 'startJourney']); // Start journey (check-out)
        Route::put('/{id}/end', [\App\Http\Controllers\Api\LogPemanduController::class, 'endJourney']); // End journey (check-in)
    });
    
    // Claims/Tuntutan endpoints
    Route::prefix('tuntutan')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\TuntutanController::class, 'index']); // Get all claims
        Route::get('/{id}', [\App\Http\Controllers\Api\TuntutanController::class, 'show']); // Get single claim
        Route::post('/', [\App\Http\Controllers\Api\TuntutanController::class, 'store']); // Create claim
        Route::put('/{id}', [\App\Http\Controllers\Api\TuntutanController::class, 'update']); // Update claim (if ditolak)
    });
    
    // Reports endpoints
    Route::prefix('reports')->group(function () {
        Route::get('/vehicle', [\App\Http\Controllers\Api\ReportController::class, 'vehicle']); // Get vehicle report
        Route::get('/cost', [\App\Http\Controllers\Api\ReportController::class, 'cost']); // Get cost report
        Route::get('/driver', [\App\Http\Controllers\Api\ReportController::class, 'driver']); // Get driver report
    });
    
    // Dashboard Statistics
    Route::get('/dashboard/statistics', [\App\Http\Controllers\Api\DashboardController::class, 'statistics']); // Get dashboard stats
});

