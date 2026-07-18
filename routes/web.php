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
    Route::post('/users/store', [AdminUserController::class, 'store'])->name('admin.users.store');
    Route::get('/users/edit/{id}', [AdminUserController::class, 'edit'])->name('admin.users.edit');
    Route::post('/users/update/{id}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::post('/users/suspend/{id}', [AdminUserController::class, 'suspend'])->name('admin.users.suspend');
    Route::post('/users/activate/{id}', [AdminUserController::class, 'activate'])->name('admin.users.activate');
    Route::delete('/users/delete/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.delete');

    // Agent Management
    Route::get('/agents', [AdminUserController::class, 'agentsIndex'])->name('admin.agents');
    Route::post('/agents/{id}/update', [AdminUserController::class, 'agentUpdate'])->name('admin.agents.update');

    // Owner Management
    Route::get('/owners', [AdminUserController::class, 'ownersIndex'])->name('admin.owners');
    
    // Property Listings
    Route::get('/properties', [AdminPropertyController::class, 'index'])->name('admin.properties');
    Route::get('/properties/create', [AdminPropertyController::class, 'create'])->name('admin.properties.create');
    Route::post('/properties/store', [AdminPropertyController::class, 'store'])->name('admin.properties.store');
    Route::get('/properties/edit/{id}', [AdminPropertyController::class, 'edit'])->name('admin.properties.edit');
    Route::post('/properties/update/{id}', [AdminPropertyController::class, 'update'])->name('admin.properties.update');
    Route::post('/properties/{id}/status', [AdminPropertyController::class, 'updateStatus'])->name('admin.properties.status');
    Route::delete('/properties/delete/{id}', [AdminPropertyController::class, 'destroy'])->name('admin.properties.delete');

    // Bookings & Visits log
    Route::get('/bookings', [AdminDashboardController::class, 'bookings'])->name('admin.bookings');
    Route::post('/bookings/{id}/action', [AdminDashboardController::class, 'bookingAction'])->name('admin.bookings.action');
    Route::delete('/bookings/{id}/delete', [AdminDashboardController::class, 'bookingDelete'])->name('admin.bookings.delete');

    // Transaction Ledger
    Route::get('/transactions', [AdminDashboardController::class, 'transactions'])->name('admin.transactions');

    // System Audit Logs
    Route::get('/audit-logs', [AdminDashboardController::class, 'auditLogs'])->name('admin.audit-logs');
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

    // Comparisons
    Route::get('/comparisons', [\App\Http\Controllers\BuyerDashboardController::class, 'comparisons'])->name('buyer.comparisons');
    Route::post('/comparisons/add', [\App\Http\Controllers\BuyerDashboardController::class, 'addComparison'])->name('buyer.comparisons.add');
    Route::delete('/comparisons/remove/{id}', [\App\Http\Controllers\BuyerDashboardController::class, 'removeComparison'])->name('buyer.comparisons.remove');

    // Profile Management
    Route::get('/profile', [\App\Http\Controllers\BuyerProfileController::class, 'index'])->name('buyer.profile');
    Route::post('/profile/update', [\App\Http\Controllers\BuyerProfileController::class, 'update'])->name('buyer.profile.update');
});

// Owner Auth Routes
Route::get('/owner/login', [\App\Http\Controllers\OwnerAuthController::class, 'showLogin'])->name('owner.login');
Route::post('/owner/login', [\App\Http\Controllers\OwnerAuthController::class, 'processLogin'])->name('owner.login.submit');
Route::get('/owner/register', [\App\Http\Controllers\OwnerAuthController::class, 'showRegister'])->name('owner.register');
Route::post('/owner/register', [\App\Http\Controllers\OwnerAuthController::class, 'processRegister'])->name('owner.register.submit');
Route::get('/owner/logout', [\App\Http\Controllers\OwnerAuthController::class, 'logout'])->name('owner.logout');

// Owner Panel Routes (Protected by Session Middleware)
Route::middleware(['owner'])->prefix('owner')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\OwnerDashboardController::class, 'index'])->name('owner.dashboard');

    // Property Management (CRUD)
    Route::get('/properties/create', [\App\Http\Controllers\OwnerPropertyController::class, 'create'])->name('owner.properties.create');
    Route::post('/properties/store', [\App\Http\Controllers\OwnerPropertyController::class, 'store'])->name('owner.properties.store');
    Route::get('/properties/{id}/edit', [\App\Http\Controllers\OwnerPropertyController::class, 'edit'])->name('owner.properties.edit');
    Route::post('/properties/{id}/update', [\App\Http\Controllers\OwnerPropertyController::class, 'update'])->name('owner.properties.update');
    Route::delete('/properties/{id}/delete', [\App\Http\Controllers\OwnerPropertyController::class, 'destroy'])->name('owner.properties.delete');

    // Bookings / Visit Management
    Route::get('/bookings', [\App\Http\Controllers\OwnerBookingController::class, 'index'])->name('owner.bookings');
    Route::post('/bookings/{id}/approve', [\App\Http\Controllers\OwnerBookingController::class, 'approve'])->name('owner.bookings.approve');
    Route::post('/bookings/{id}/reject', [\App\Http\Controllers\OwnerBookingController::class, 'reject'])->name('owner.bookings.reject');
    Route::post('/bookings/{id}/complete', [\App\Http\Controllers\OwnerBookingController::class, 'complete'])->name('owner.bookings.complete');

    // Agent Management
    Route::get('/agents', [\App\Http\Controllers\OwnerAgentController::class, 'index'])->name('owner.agents');
    Route::post('/properties/{propertyId}/assign-agent', [\App\Http\Controllers\OwnerAgentController::class, 'assign'])->name('owner.properties.assign-agent');

    // Profile Management
    Route::get('/profile', [\App\Http\Controllers\OwnerProfileController::class, 'index'])->name('owner.profile');
    Route::post('/profile/update', [\App\Http\Controllers\OwnerProfileController::class, 'update'])->name('owner.profile.update');
});

// Agent Auth Routes
Route::get('/agent/login', [\App\Http\Controllers\AgentAuthController::class, 'showLogin'])->name('agent.login');
Route::post('/agent/login', [\App\Http\Controllers\AgentAuthController::class, 'processLogin'])->name('agent.login.submit');
Route::get('/agent/register', [\App\Http\Controllers\AgentAuthController::class, 'showRegister'])->name('agent.register');
Route::post('/agent/register', [\App\Http\Controllers\AgentAuthController::class, 'processRegister'])->name('agent.register.submit');
Route::get('/agent/logout', [\App\Http\Controllers\AgentAuthController::class, 'logout'])->name('agent.logout');

// Agent Panel Routes (Protected by Session Middleware)
Route::middleware(['agent'])->prefix('agent')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\AgentDashboardController::class, 'index'])->name('agent.dashboard');
    Route::get('/properties', [\App\Http\Controllers\AgentPropertyController::class, 'index'])->name('agent.properties');
    
    // Bookings / Visit Management
    Route::get('/bookings', [\App\Http\Controllers\AgentBookingController::class, 'index'])->name('agent.bookings');
    Route::post('/bookings/{id}/approve', [\App\Http\Controllers\AgentBookingController::class, 'approve'])->name('agent.bookings.approve');
    Route::post('/bookings/{id}/reject', [\App\Http\Controllers\AgentBookingController::class, 'reject'])->name('agent.bookings.reject');
    Route::post('/bookings/{id}/complete', [\App\Http\Controllers\AgentBookingController::class, 'complete'])->name('agent.bookings.complete');

    // Reviews
    Route::get('/reviews', [\App\Http\Controllers\AgentDashboardController::class, 'reviews'])->name('agent.reviews');

    // Profile Management
    Route::get('/profile', [\App\Http\Controllers\AgentProfileController::class, 'index'])->name('agent.profile');
    Route::post('/profile/update', [\App\Http\Controllers\AgentProfileController::class, 'update'])->name('agent.profile.update');

    // Sales & Commission Analytics
    Route::get('/analytics', [\App\Http\Controllers\AgentDashboardController::class, 'analytics'])->name('agent.analytics');

    // Client Tracking CRM
    Route::get('/clients', [\App\Http\Controllers\AgentDashboardController::class, 'clients'])->name('agent.clients');

    // Availability Calendar
    Route::get('/calendar', [\App\Http\Controllers\AgentDashboardController::class, 'calendar'])->name('agent.calendar');
    Route::post('/calendar/store', [\App\Http\Controllers\AgentDashboardController::class, 'storeCalendar'])->name('agent.calendar.store');
    Route::delete('/calendar/{id}/delete', [\App\Http\Controllers\AgentDashboardController::class, 'deleteCalendar'])->name('agent.calendar.delete');
});

