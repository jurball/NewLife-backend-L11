<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FileController;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\DeniedMiddleware;

Route::post('registration', [AuthController::class, 'registration']);
Route::post('authorization', [AuthController::class, 'authorization']);

// Защищенный маршрут
Route::middleware(['auth:sanctum', AuthMiddleware::class])->group(function() {
    Route::get('logout', [AuthController::class, 'logout']);

    Route::post('files', [FileController::class, 'uploadFile']);
    Route::get('files/disk', [FileController::class, 'getAllFiles']);
    Route::get('files/shared', [FileController::class, 'shared']);

    Route::middleware([DeniedMiddleware::class])->group(function() {
        Route::get('files/{fileId}', [FileController::class, 'downloadFile']);
        Route::delete('files/{fileId}', [FileController::class, 'deleteFile']);
        Route::patch('files/{fileId}', [FileController::class, 'updateNameFile']);
    });

    Route::post('files/{fileId}/access', [FileController::class, 'addAccessFile']);
    Route::delete('files/{fileId}/access', [FileController::class, 'deleteAccessFile']);
});


