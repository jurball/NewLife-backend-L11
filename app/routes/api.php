<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FileController;

Route::prefix('')->group(function() {
//    Route::ApiResource('posts', PostController::class);
})->middleware(['auth:sanctum']);

Route::prefix('')->group(function() {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::get('logout', [AuthController::class, 'logout']);
});

/*Route::middleware('auth:sanctum')->post('upload', [FileController::class, 'uploadFile']);
Route::middleware('auth:sanctum')->get('files/{fileId}', [FileController::class, 'getFile']);*/

Route::post('upload', [FileController::class, 'uploadFile']);
Route::get('files/{fileId}', [FileController::class, 'getFile']);
