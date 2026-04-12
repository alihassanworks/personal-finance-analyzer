<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\CategoryThresholdController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', function () {
        return view('welcome');
    })->name('dashboard');

    Route::resource('transactions', TransactionController::class)->except(['show']);

    Route::get('/categories/thresholds', [CategoryThresholdController::class, 'index'])->name('categories.thresholds');
    Route::put('/categories/thresholds', [CategoryThresholdController::class, 'update'])->name('categories.thresholds.update');
});