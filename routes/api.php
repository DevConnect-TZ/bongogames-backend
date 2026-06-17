<?php

use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DeveloperController;
use App\Http\Controllers\Api\GameController;
use App\Http\Controllers\Api\LibraryController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ReviewController;
use App\Http\Controllers\Api\WishlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $uptime = shell_exec('uptime 2>/dev/null') ?: (PHP_OS_FAMILY === 'Windows' ? trim(shell_exec('systeminfo | find "System Boot Time"') ?: 'Windows') : 'Unknown');

    return view('api-index', [
        'uptime' => trim($uptime),
        'timestamp' => now()->toIso8601String(),
    ]);
});

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('reset-password', [AuthController::class, 'resetPassword']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('me', [AuthController::class, 'me']);
    });
});

Route::get('categories', [CategoryController::class, 'index']);

Route::get('games', [GameController::class, 'index']);
Route::get('games/{game}', [GameController::class, 'show']);

Route::get('games/{game}/reviews', [ReviewController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('games/{game}/reviews', [ReviewController::class, 'store']);

    Route::get('library', [LibraryController::class, 'index']);

    Route::post('payments/create-order', [PaymentController::class, 'createOrder']);
    Route::post('payments/check-status', [PaymentController::class, 'checkStatus']);
    Route::post('payments/complete/{transaction}', [LibraryController::class, 'completePurchase']);

    Route::get('wishlist', [WishlistController::class, 'index']);
    Route::post('games/{game}/wishlist', [WishlistController::class, 'add']);
    Route::delete('games/{game}/wishlist', [WishlistController::class, 'remove']);

    Route::middleware('role:developer')->prefix('developer')->group(function () {
        Route::get('dashboard', [DeveloperController::class, 'dashboard']);
        Route::get('games', [DeveloperController::class, 'games']);
        Route::post('games', [DeveloperController::class, 'storeGame']);
        Route::put('games/{game}', [DeveloperController::class, 'updateGame']);
    });

    Route::middleware('role:admin')->prefix('admin')->group(function () {
        Route::get('dashboard', [AdminController::class, 'dashboard']);

        Route::get('games', [AdminController::class, 'games']);
        Route::post('games', [AdminController::class, 'storeGame']);
        Route::put('games/{game}', [AdminController::class, 'updateGame']);
        Route::put('games/{game}/status', [AdminController::class, 'updateGameStatus']);
        Route::delete('games/{game}', [AdminController::class, 'deleteGame']);

        Route::get('categories', [CategoryController::class, 'index']);
        Route::post('categories', [CategoryController::class, 'store']);
        Route::put('categories/{category}', [CategoryController::class, 'update']);
        Route::delete('categories/{category}', [CategoryController::class, 'destroy']);

        Route::get('users', [AdminController::class, 'users']);
        Route::post('users', [AdminController::class, 'storeUser']);
        Route::put('users/{user}', [AdminController::class, 'updateUser']);
        Route::delete('users/{user}', [AdminController::class, 'deleteUser']);

        Route::get('developers', [AdminController::class, 'developers']);
        Route::post('developers', [AdminController::class, 'storeDeveloper']);
        Route::put('developers/{developer}', [AdminController::class, 'updateDeveloper']);
        Route::delete('developers/{developer}', [AdminController::class, 'deleteDeveloper']);

        Route::get('sales', [AdminController::class, 'sales']);
    });
});
