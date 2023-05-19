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

    //app
    Route::get('app', [App\Http\Controllers\Api\Apps\AppsController::class, 'index']);
    Route::get('app/get/{app_id}', [App\Http\Controllers\Api\Apps\AppsController::class, 'show']);
    
    //user
    Route::get('user', [App\Http\Controllers\Api\Auth\UserController::class, 'index']);
    Route::get('user/login', [App\Http\Controllers\Api\Auth\UserController::class, 'show']);
    Route::get('user/get/{id}', [App\Http\Controllers\Api\Auth\UserController::class, 'get']);

    //akses
    Route::get('akses/app/get/{app_id}', [App\Http\Controllers\Api\Akses\AksesController::class, 'showApp']);
    Route::get('akses/user/get/{user_id}', [App\Http\Controllers\Api\Akses\AksesController::class, 'showUser']);
});

Route::middleware(['middleware' => 'adminit.checker'])->group(function () {
    //app
    Route::post('app/add', [App\Http\Controllers\Api\Apps\AppsController::class, 'store']);
    Route::post('app/update/{app_id}', [App\Http\Controllers\Api\Apps\AppsController::class, 'update']);
    Route::get('app/post/{app_id}', [App\Http\Controllers\Api\Apps\AppsController::class, 'togglePost']);

    //akses
    Route::get('akses', [App\Http\Controllers\Api\Akses\AksesController::class, 'index']);
    Route::get('akses/get/{akses_id}', [App\Http\Controllers\Api\Akses\AksesController::class, 'show']);
    Route::post('akses/add', [App\Http\Controllers\Api\Akses\AksesController::class, 'store']);
    Route::post('akses/update/{akses_id}', [App\Http\Controllers\Api\Akses\AksesController::class, 'update']); 
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
