<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('admin.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__.'/auth.php';

Route::get('/admin/logout', [AdminController::class, 'destroy'])->name('admin.logout');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login');
Route::get('/verification', [AdminController::class, 'showVerification'])->name('custom.verification.form');
Route::post('/verification', [AdminController::class, 'submitVerification'])->name('custom.verification.submit');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [AdminController::class, 'showProfile'])->name('admin.profile.show');
    Route::post('/profile', [AdminController::class, 'updateProfile'])->name('admin.profile.store');
});

