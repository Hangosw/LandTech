<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PropertyController;

use App\Http\Controllers\WishlistController;

// Pages
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/thue', [PageController::class, 'rentList'])->name('rent.list');
Route::get('/thue/{slug}', [PageController::class, 'rentDetail'])->name('rent.detail');
Route::get('/chu-nha', [PageController::class, 'owner'])->name('owner');
Route::get('/dang-tin', [PageController::class, 'postProperty'])->name('property.post');
Route::post('/dang-tin', [PropertyController::class, 'store'])->name('property.post.submit');
Route::post('/dang-tin/draft', [PropertyController::class, 'saveDraft'])->name('property.draft.save');
Route::delete('/dang-tin/draft/{id}', [PropertyController::class, 'deleteDraft'])->name('property.draft.delete');
Route::get('/quan-ly-tin-dang/{id}/edit', [PropertyController::class, 'edit'])->name('property.edit');
Route::post('/quan-ly-tin-dang/{id}', [PropertyController::class, 'update'])->name('property.update');
Route::get('/yeu-thich', [PageController::class, 'wishlist'])->name('wishlist');
Route::get('/quan-ly-tin-dang', [PageController::class, 'myProperties'])->name('my-properties');
Route::delete('/quan-ly-tin-dang/{id}', [PropertyController::class, 'destroy'])->name('property.destroy');
Route::post('/wishlist/toggle', [WishlistController::class, 'toggle'])->name('wishlist.toggle');
Route::post('/booking', [\App\Http\Controllers\BookingController::class, 'store'])->name('booking.store');
Route::get('/moi-gioi', [PageController::class, 'agents'])->name('agents');
Route::get('/du-an', [PageController::class, 'projects'])->name('projects');
Route::get('/lich-hen-cua-toi', [\App\Http\Controllers\BookingController::class, 'myBookings'])->name('my-bookings');

// Profile
use App\Http\Controllers\ProfileController;
Route::get('/thong-tin-ca-nhan', [ProfileController::class, 'edit'])->name('profile.edit');
Route::post('/thong-tin-ca-nhan', [ProfileController::class, 'update'])->name('profile.update');

// Auth Endpoints
Route::get('/dang-nhap', [AuthController::class, 'login'])->name('login');
Route::post('/dang-nhap', [AuthController::class, 'handleLogin']);
Route::get('/dang-xuat', [AuthController::class, 'logout'])->name('logout');

// Google OAuth (Real — Laravel Socialite)
Route::get('/auth/google', [AuthController::class, 'googleRedirect'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'googleCallback'])->name('auth.google.callback');

// OAuth Mock (Zalo & other providers without real OAuth)
Route::get('/oauth/{provider}', [AuthController::class, 'oauthRedirect'])->name('oauth.redirect');
Route::get('/oauth/{provider}/mock', [AuthController::class, 'oauthMock'])->name('oauth.mock');
Route::post('/oauth/{provider}/callback', [AuthController::class, 'oauthCallback'])->name('oauth.callback');

Route::get('/auth/link-provider-offer', [AuthController::class, 'linkProviderOffer'])->name('auth.link-provider-offer');
Route::post('/auth/link-provider', [AuthController::class, 'linkProvider'])->name('auth.link-provider');
Route::post('/auth/unlink-provider/{provider}', [AuthController::class, 'unlinkProvider'])->name('auth.unlink-provider');

// Admin Routes (Simple implementation for user management)
use App\Http\Controllers\Admin\UserController as AdminUserController;
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
    Route::post('/users/{id}/status', [AdminUserController::class, 'updateStatus'])->name('users.status');
    Route::get('/users/{id}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [AdminUserController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [AdminUserController::class, 'destroy'])->name('users.destroy');
    
    // Properties
    Route::get('/properties', [\App\Http\Controllers\PropertiesController::class, 'index'])->name('properties.index');
    Route::post('/properties/{id}/status', [\App\Http\Controllers\PropertiesController::class, 'updateStatus'])->name('properties.status');
    Route::get('/properties/{id}/edit', [\App\Http\Controllers\PropertiesController::class, 'edit'])->name('properties.edit');
    Route::put('/properties/{id}', [\App\Http\Controllers\PropertiesController::class, 'update'])->name('properties.update');
    Route::delete('/properties/{id}', [\App\Http\Controllers\PropertiesController::class, 'destroy'])->name('properties.destroy');

    // Projects (/du-an catalog)
    Route::get('/projects', [\App\Http\Controllers\ProjectsController::class, 'index'])->name('projects.index');
    Route::get('/projects/create', [\App\Http\Controllers\ProjectsController::class, 'create'])->name('projects.create');
    Route::post('/projects', [\App\Http\Controllers\ProjectsController::class, 'store'])->name('projects.store');
    Route::get('/projects/{id}/edit', [\App\Http\Controllers\ProjectsController::class, 'edit'])->name('projects.edit');
    Route::put('/projects/{id}', [\App\Http\Controllers\ProjectsController::class, 'update'])->name('projects.update');
    Route::post('/projects/{id}/toggle', [\App\Http\Controllers\ProjectsController::class, 'toggle'])->name('projects.toggle');
    Route::delete('/projects/{id}', [\App\Http\Controllers\ProjectsController::class, 'destroy'])->name('projects.destroy');

    // Roles & Permissions
    Route::get('/roles', [\App\Http\Controllers\Admin\RoleController::class, 'index'])->name('roles.index');
    Route::post('/roles', [\App\Http\Controllers\Admin\RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{id}/edit', [\App\Http\Controllers\Admin\RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{id}', [\App\Http\Controllers\Admin\RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{id}', [\App\Http\Controllers\Admin\RoleController::class, 'destroy'])->name('roles.destroy');
    Route::post('/permissions', [\App\Http\Controllers\Admin\RoleController::class, 'storePermission'])->name('permissions.store');
    Route::delete('/permissions/{id}', [\App\Http\Controllers\Admin\RoleController::class, 'destroyPermission'])->name('permissions.destroy');
});

