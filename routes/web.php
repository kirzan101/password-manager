<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::middleware(['guest'])->group(function () {
    Route::get('/login', [\App\Http\Controllers\System\AuthController::class, 'index'])->name('login');
});

Route::post('/login', [\App\Http\Controllers\System\AuthController::class, 'login']);
Route::middleware(['auth'])->group(function () {
    Route::get('/first-login', [\App\Http\Controllers\System\AuthController::class, 'firstLogin'])->name('first-login');
    Route::post('/first-login-change-password', [\App\Http\Controllers\System\AuthController::class, 'firstLoginChangePassword'])->name('first-login-change-password');

    Route::post('/logout', [\App\Http\Controllers\System\AuthController::class, 'logout']);
    Route::put('/change-password', [\App\Http\Controllers\System\AuthController::class, 'changePassword'])->name('change-password');
    Route::put('/reset-password/{userId}', [\App\Http\Controllers\System\AuthController::class, 'resetPassword'])->name('reset-password');
    Route::put('/set-user-status/{userId}', [\App\Http\Controllers\System\AuthController::class, 'setUserStatus'])->name('set-user-status');
    Route::put('/change-avatar/{profileId?}', [\App\Http\Controllers\System\UserController::class, 'changeAvatar'])->name('change-avatar');
    Route::put('/remove-avatar/{profileId?}', [\App\Http\Controllers\System\UserController::class, 'removeAvatar'])->name('remove-avatar');

    // Protected routes that require the user to have completed the first login process
    Route::middleware(['first.login'])->group(function () {
        Route::get('/', [\App\Http\Controllers\HomeController::class, 'index'])->name('home');
        Route::get('/dashboard', [\App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
        Route::get('/profile', [\App\Http\Controllers\HomeController::class, 'profile'])->name('profile');
        Route::put('/update-basic-profile/{profileId?}', [\App\Http\Controllers\System\UserController::class, 'updateBasicProfile'])->name('update-basic-profile');
        Route::get('/settings', [\App\Http\Controllers\System\SettingsController::class, 'index'])->name('settings.index');

        Route::resource('users', \App\Http\Controllers\System\UserController::class)->only(['index', 'store', 'update']);
        Route::resource('user-groups', \App\Http\Controllers\System\UserGroupController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('roles', \App\Http\Controllers\System\RoleController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('permissions', \App\Http\Controllers\System\PermissionController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('activity-logs', [\App\Http\Controllers\System\ActivityLogController::class, 'index'])->name('activity-logs.index');
    });

    // Route to serve uploaded files from the private disk
    Route::get('/uploads/{path}', function (string $path) {
        $disk = \Illuminate\Support\Facades\Storage::disk('private');

        abort_unless($disk->exists($path), 404);

        return response()->file($disk->path($path));
    })
        ->where('path', '.*')
        ->name('uploads.show');
});

// Catch-all route for Inertia (must be defined last)
Route::get('{any}', function () {
    return Inertia::render('Error', [
        'code' => 404,
        'message' => 'The page you are looking for could not be found.',
    ]);
})->where('any', '.*');
