<?php

use App\Http\Controllers\AvatarController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/user', [HomeController::class, 'user'])->name('user');
Route::get('/teacher', [HomeController::class, 'teacher'])->name('teachers');
Route::get('/student', [HomeController::class, 'student'])->name('students');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/create', [AvatarController::class, 'create'])->name('avatar.update');
    Route::post('/store', [AvatarController::class, 'store'])->name('avatar.store');

});

require __DIR__.'/auth.php';
