<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\RegisterController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BookController::class, 'index'])->name('home');

Route::middleware('guest')->group(function () {
    Route::view('/register', 'guest.register')->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
    Route::view('/login', 'guest.login')->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Route::middleware('auth')->group(function () {
    //     Route::resource('books', BookController::class)
    //         ->only(['index', 'create', 'store', 'edit', 'update']);

    //     Route::get('/books/{book}/edit', [BookController::class, 'edit'])
    //         ->middleware('can:update,book')
    //         ->name('books.edit');

    //     Route::patch('/books/{book}', [BookController::class, 'update'])
    //         ->middleware('can:update,book')
    //         ->name('books.update');
    // });

    Route::middleware('auth')->group(function () {
        Route::resource('books', BookController::class)
            ->only(['index', 'create', 'store', 'edit', 'update']);
    });
});