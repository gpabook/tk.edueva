<?php

use App\Http\Controllers\AvatarController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
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

// Routes for managing roles
Route::middleware('auth')->group(function () {
        // Ensure users are authenticated

        // Group for role management, protected by 'superadmin' or 'admin' role
        Route::middleware(['role:superadmin|admin'])
            ->group(function () {

        Route::get('roles', [RoleController::class, 'index'])->name('roles.index')->middleware('permission:read roles');
        Route::get('roles/create', [RoleController::class, 'create'])->name('roles.create')->middleware('permission:create roles');
        Route::post('roles', [RoleController::class, 'store'])->name('roles.store')->middleware('permission:create roles');
        Route::get('roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit')->middleware('permission:update roles');
        Route::put('roles/{role}', [RoleController::class, 'update'])->name('roles.update')->middleware('permission:update roles');
        Route::delete('roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('permission:delete roles');
        Route::get('roles/{role}', [RoleController::class, 'show'])->name('roles.show')->middleware('permission:read roles'); // If you have a show method

    });

});

require __DIR__.'/auth.php';
