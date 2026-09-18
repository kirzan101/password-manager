<?php

use Illuminate\Support\Facades\Route;

// Route::get('/user-groups', [UserGroupController::class, 'index']);

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/user-groups', [\App\Http\Controllers\API\System\UserGroupApiController::class, 'index']);
    Route::get('/roles', [\App\Http\Controllers\API\System\RoleApiController::class, 'index']);
    Route::get('/users', [\App\Http\Controllers\API\System\UserApiController::class, 'index']);
    Route::get('/activity-logs', [\App\Http\Controllers\API\System\ActivityLogApiController::class, 'index']);
    Route::get('/permissions', [\App\Http\Controllers\API\System\PermissionApiController::class, 'index']);

    // search
    Route::get('/user-groups/search', [\App\Http\Controllers\API\System\UserGroupApiController::class, 'searchIndex']);
    Route::get('/roles/search', [\App\Http\Controllers\API\System\RoleApiController::class, 'searchIndex']);
});
