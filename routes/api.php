<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::get('/auth-checker', [\App\Http\Controllers\Api\Auth\AuthController::class, 'auth_checker']);

// Route::group(['middleware' => 'auth'], function () {
    //     Route::post('/logout', [\App\Http\Controllers\Api\Auth\AuthController::class, 'logout']);
    // });
Route::post('/login', [\App\Http\Controllers\Api\Auth\AuthController::class, 'login']);
Route::group(['middleware' => 'token.checker'], function () {
    Route::get('/redirect', [\App\Http\Controllers\Api\PortalController::class, 'show']);
    Route::post('/logout', [\App\Http\Controllers\Api\Auth\AuthController::class, 'logout']);
});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
