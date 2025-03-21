<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FileController;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\DeniedMiddleware;
use App\Http\Middleware\CheckAccessFileUserMiddleware;

Route::post('registration', [AuthController::class, 'registration']);
Route::post('authorization', [AuthController::class, 'authorization']);

// Защищенный маршрут
Route::middleware([AuthMiddleware::class, 'auth:sanctum'])->group(function () {
    Route::get('logout', [AuthController::class, 'logout']);

    Route::post('files', [FileController::class, 'uploadFile']);
    Route::get('files/disk', [FileController::class, 'getAllFiles']);
    Route::get('files/shared', [FileController::class, 'shared']);

    Route::get('token', function () {
        return response()->json([
            "message" => "is valid"
        ], 200);
    });

    Route::middleware([DeniedMiddleware::class])->group(function () {
        Route::delete('files/{fileId}', [FileController::class, 'deleteFile']);
        Route::patch('files/{fileId}', [FileController::class, 'updateNameFile']);

        Route::post('files/{fileId}/access', [FileController::class, 'addAccessFile']);
        Route::delete('files/{fileId}/access', [FileController::class, 'deleteAccessFile']);
    });

    Route::get('files/{fileId}', [FileController::class, 'downloadFile'])->middleware(CheckAccessFileUserMiddleware::class);
});
