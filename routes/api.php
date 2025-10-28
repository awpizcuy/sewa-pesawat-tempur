<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\UnitController;
use App\Http\Controllers\Api\RentalController;
use App\Http\Controllers\Api\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\Admin\UnitController as AdminUnitController;
use App\Http\Controllers\Api\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Api\Admin\RentalController as AdminRentalController;

// === Rute Publik (Autentikasi) ===
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// === Rute untuk ANGGOTA (Wajib Login) ===
    Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [UserController::class, 'profile']);
    Route::put('/profile', [UserController::class, 'updateProfile']);
    Route::get('/units', [UnitController::class, 'index']);
    Route::get('/units/search', [UnitController::class, 'search']);
    Route::post('/rentals', [RentalController::class, 'store']);
    Route::get('/my-rentals', [RentalController::class, 'myRentals']);
    });

// === Rute untuk ADMIN (Wajib Login + Role Admin) ===

    Route::prefix('admin')->middleware(['auth:sanctum', 'admin.role'])
    ->group(function () {

    Route::apiResource('/users', AdminUserController::class);
    Route::apiResource('/units', AdminUnitController::class);
    Route::apiResource('/categories', AdminCategoryController::class);
    Route::get('/rentals', [AdminRentalController::class, 'index']);
    Route::post('/rentals/{rental}/return', [AdminRentalController::class, 'processReturn']);
    Route::get('/users/{userId}/history', [AdminRentalController::class, 'userRentalHistory']);
});
