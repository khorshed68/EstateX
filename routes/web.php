<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminPropertyController;

// Root redirect
Route::get('/', function () {
    return redirect()->route('admin.login');
});

// Admin Auth Routes
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login', [AuthController::class, 'processLogin'])->name('admin.login.submit');
Route::get('/admin/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Admin Panel Routes (Protected by Session Middleware)
Route::middleware(['admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    
    // User Management
    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users');
    Route::post('/users/suspend/{id}', [AdminUserController::class, 'suspend'])->name('admin.users.suspend');
    Route::post('/users/activate/{id}', [AdminUserController::class, 'activate'])->name('admin.users.activate');
    
    // Property Listings
    Route::get('/properties', [AdminPropertyController::class, 'index'])->name('admin.properties');
    Route::delete('/properties/delete/{id}', [AdminPropertyController::class, 'destroy'])->name('admin.properties.delete');
});
