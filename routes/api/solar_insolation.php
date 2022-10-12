<?php

use App\Http\Controllers\Api\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'auth',
    'namespace' => 'Auth',
],
    function () {
        Route::post('login', [AuthController::class, 'login'])->name('auth.login');
        Route::post('refresh', [AuthController::class, 'refresh'])->name('auth.refresh');

        Route::group(['middleware' => 'auth:api'], function () {
            Route::post('logout', [AuthController::class, 'logout'])->name('auth.logout');
            Route::get('check', [AuthController::class, 'check'])->name('auth.check');
        });
    }
);
