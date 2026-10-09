<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Public Lang Switch Route
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'id'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('lang.switch');

// Guest Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    
    // Google OAuth Routes
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);
    Route::get('/auth/google/mock', [AuthController::class, 'mockGoogleLogin'])->name('auth.google.mock');
});

// Authenticated Action Routes (Defined before wildcards)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Items Creation & Interactions
    Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
    Route::post('/items/{item}/like', [ItemController::class, 'toggleLike'])->name('items.like');
    Route::post('/items/{item}/repost', [ItemController::class, 'toggleRepost'])->name('items.repost');
    Route::post('/items/{item}/comment', [ItemController::class, 'storeComment'])->name('items.comment');

    // Own Profile page & Editing
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.me');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});

// Public Viewer Routes (Non-mandatory Login)
Route::get('/', [ItemController::class, 'index'])->name('items.index');
Route::get('/search', [\App\Http\Controllers\SearchController::class, 'index'])->name('search');
Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');
Route::get('/map', [MapController::class, 'index'])->name('map');
Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard');
Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');
