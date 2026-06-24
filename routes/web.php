<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminPropertyController;

// Root redirect
Route::get('/', function () {
    return view('welcome');
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

// Buyer Auth Routes
Route::get('/buyer/login', [\App\Http\Controllers\BuyerAuthController::class, 'showLogin'])->name('buyer.login');
Route::post('/buyer/login', [\App\Http\Controllers\BuyerAuthController::class, 'processLogin'])->name('buyer.login.submit');
Route::get('/buyer/register', [\App\Http\Controllers\BuyerAuthController::class, 'showRegister'])->name('buyer.register');
Route::post('/buyer/register', [\App\Http\Controllers\BuyerAuthController::class, 'processRegister'])->name('buyer.register.submit');
Route::get('/buyer/logout', [\App\Http\Controllers\BuyerAuthController::class, 'logout'])->name('buyer.logout');

// Buyer Panel Routes (Protected by Session Middleware)
Route::middleware(['buyer'])->prefix('buyer')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\BuyerDashboardController::class, 'index'])->name('buyer.dashboard');
    Route::get('/properties/{id}', [\App\Http\Controllers\BuyerDashboardController::class, 'show'])->name('buyer.properties.show');
    
    // Wishlist Management
    Route::get('/wishlist', [\App\Http\Controllers\BuyerWishlistController::class, 'index'])->name('buyer.wishlist');
    Route::post('/wishlist/add/{id}', [\App\Http\Controllers\BuyerWishlistController::class, 'add'])->name('buyer.wishlist.add');
    Route::post('/wishlist/remove/{id}', [\App\Http\Controllers\BuyerWishlistController::class, 'remove'])->name('buyer.wishlist.remove');
    
    // Bookings & Transactions
    Route::get('/bookings', [\App\Http\Controllers\BuyerBookingController::class, 'index'])->name('buyer.bookings');
    Route::post('/bookings/store', [\App\Http\Controllers\BuyerBookingController::class, 'store'])->name('buyer.bookings.store');
    
    // Reviews
    Route::post('/reviews/property', [\App\Http\Controllers\BuyerReviewController::class, 'storeProperty'])->name('buyer.reviews.property');
    Route::post('/reviews/agent', [\App\Http\Controllers\BuyerReviewController::class, 'storeAgent'])->name('buyer.reviews.agent');
});
