<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FileController;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\DeniedMiddleware;

/*
Route::prefix('')->group(function() {
    Route::ApiResource('posts', PostController::class);
})->middleware(['auth:sanctum']);
*/

Route::prefix('')->group(function() {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::get('logout', [AuthController::class, 'logout'])->middleware([AuthMiddleware::class]);
});

Route::middleware([AuthMiddleware::class])->prefix('')->group(function() {
    Route::post('files', [FileController::class, 'uploadFile']);
    Route::get('files/disk', [FileController::class, 'index']);

    Route::get('files/{fileId}', [FileController::class, 'getFile'])->middleware([DeniedMiddleware::class]);
    Route::delete('files/{fileId}', [FileController::class, 'deleteFile'])->middleware([DeniedMiddleware::class]);
    Route::patch('files/{fileId}', [FileController::class, 'updateFile'])->middleware([DeniedMiddleware::class]);
});


