<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::prefix('')->group(function() {
//    Route::ApiResource('posts', PostController::class);
    Route::get('logout', [AuthController::class, 'logout'])->middleware(['auth:sanctum']);
});

Route::prefix('')->group(function() {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
});

