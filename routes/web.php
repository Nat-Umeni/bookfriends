<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\FriendsController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\FeedController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BookController::class, 'index'])->name('home');

Route::prefix('auth')->name('auth.oauth.')->group(function () {
    // Guests must be able to start and finish OAuth
    Route::get('{provider}/redirect', [OAuthController::class, 'redirect'])
        ->where('provider', 'github|google')
        ->middleware('guest')
        ->name('redirect');

    Route::get('{provider}/callback', [OAuthController::class, 'callback'])
        ->where('provider', 'github|google')
        ->middleware('guest')
        ->name('callback');
});

Route::middleware('guest')->group(function () {
    Route::view('/register', 'guest.register')->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
    Route::view('/login', 'guest.login')->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::resource('books', BookController::class)
        ->only(['index', 'create', 'store', 'edit', 'update']);

    Route::resource('friends', FriendsController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    Route::get('/feed', [FeedController::class, 'index'])->name('feed.index');
});