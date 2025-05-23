<?php

use App\Http\Controllers\AvatarController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\BankAccountController;
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

    Route::middleware(['role:superadmin|admin|teacher|student'])
    ->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/create', [AvatarController::class, 'create'])->name('avatar.update');
    Route::post('/store', [AvatarController::class, 'store'])->name('avatar.store');
    });
});
Route::middleware('auth')->group(function () {

    Route::middleware(['role:superadmin|admin|teacher'])
    ->group(function () {

        Route::get('/user', [BankAccountController::class, 'bankuser'])->name('bank.user');
    // Bank account routes
    Route::get('/bank/{user_id}', [BankAccountController::class, 'show'])
         ->name('bank.account');
    Route::post('/bank/deposit', [BankAccountController::class, 'deposit'])
         ->name('bank.deposit');
    Route::post('/bank/withdraw', [BankAccountController::class, 'withdraw'])
         ->name('bank.withdraw');
    Route::get('/bank/passbook/{user_id}', [BankAccountController::class, 'passbookPdf'])
         ->name('bank.passbook.pdf');



    });
});

// Start Routes for managing roles
Route::middleware('auth')->group(function () {

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
// End Routes for managing roles

// start Routes for managing permission
Route::middleware('auth')->group(function () {

    // Group for Role and Permission management, protected by 'superadmin' or 'admin' role
    // Adjust the outer middleware as per your needs (e.g., specific permission like 'access admin area')
    Route::middleware(['role:superadmin|admin']) // Or a more specific permission
        ->group(function () {

            // Routes for managing Permissions
            Route::get('permissions', [PermissionController::class, 'index'])->name('permissions.index')->middleware('permission:view permissions');
            Route::get('permissions/create', [PermissionController::class, 'create'])->name('permissions.create')->middleware('permission:create permissions');
            Route::post('permissions', [PermissionController::class, 'store'])->name('permissions.store')->middleware('permission:create permissions');
            Route::get('permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit')->middleware('permission:edit permissions');
            Route::put('permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update')->middleware('permission:edit permissions');
            Route::delete('permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy')->middleware('permission:delete permissions');
        });
});

// ...End for Permission

require __DIR__.'/auth.php';