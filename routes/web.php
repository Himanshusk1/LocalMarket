<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BrowseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ListingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - LocalMarket Classifieds
|--------------------------------------------------------------------------
*/

// Home page
Route::get('/', [HomeController::class, 'index'])->name('home');

// General Browse & Search
Route::get('/browse', [BrowseController::class, 'index'])->name('browse');

// Category-wise listing page (e.g. /category/electronics-appliances)
Route::get('/category/{category:slug}', [BrowseController::class, 'category'])->name('category.show');

// City + Category listing page (e.g. /city/gwalior/electronics-appliances)
Route::get('/city/{city}/{category:slug}', [BrowseController::class, 'cityCategory'])->name('city.category.show');

// City-wise listing page (e.g. /city/gwalior)
Route::get('/city/{city}', [BrowseController::class, 'city'])->name('city.show');

// Public listing details
Route::get('/listings/{listing}', [ListingController::class, 'show'])->name('listings.show');

// AJAX subcategories lookup
Route::get('/api/categories/{category}/subcategories', [ListingController::class, 'getSubcategories'])->name('api.subcategories');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Sell / Add Listing
    Route::get('/sell', [ListingController::class, 'create'])->name('listings.create');
    Route::post('/sell', [ListingController::class, 'store'])->name('listings.store');

    // Listing Edit & Delete
    Route::get('/listings/{listing}/edit', [ListingController::class, 'edit'])->name('listings.edit');
    Route::put('/listings/{listing}', [ListingController::class, 'update'])->name('listings.update');
    Route::delete('/listings/{listing}', [ListingController::class, 'destroy'])->name('listings.destroy');
    Route::delete('/listings/{listing}/images/{image}', [ListingController::class, 'deleteImage'])->name('listings.images.destroy');

    // Account & Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/listings/{listing}/toggle-status', [DashboardController::class, 'toggleStatus'])->name('dashboard.toggle-status');
});
