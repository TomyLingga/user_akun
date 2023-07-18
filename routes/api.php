<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::get('/auth-checker', [\App\Http\Controllers\Api\Auth\AuthController::class, 'auth_checker']);

//Misc
    //lokasi
Route::get('/prov', [\App\Http\Controllers\Api\Miscellaneous\AlamatController::class, 'getProv']);
Route::get('/prov/get/{id_prov}', [\App\Http\Controllers\Api\Miscellaneous\AlamatController::class, 'detailProv']);

Route::get('/kabkot/{id_prov}', [\App\Http\Controllers\Api\Miscellaneous\AlamatController::class, 'getKabKot']);
Route::get('/kabkot/get/{id_kabkot}', [\App\Http\Controllers\Api\Miscellaneous\AlamatController::class, 'detailKabKot']);

Route::get('/kec/{id_kabkot}', [\App\Http\Controllers\Api\Miscellaneous\AlamatController::class, 'getKec']);
Route::get('/kec/get/{id_kec}', [\App\Http\Controllers\Api\Miscellaneous\AlamatController::class, 'detailKec']);

Route::get('/kel/{id_kec}', [\App\Http\Controllers\Api\Miscellaneous\AlamatController::class, 'getKel']);
Route::get('/kel/get/{id_kel}', [\App\Http\Controllers\Api\Miscellaneous\AlamatController::class, 'detailKel']);

    //kampus
Route::get('/load/pt', [\App\Http\Controllers\Api\Miscellaneous\KampusController::class, 'getKampus']);
Route::get('/load/prodi/{id_sp}', [\App\Http\Controllers\Api\Miscellaneous\KampusController::class, 'getProdi']);

//https://api-frontend.kemdikbud.go.id/v2/detail_pt/AD4F980F-4024-40AE-97CE-6F4E025B1B1D

    //bank
Route::get('/load/kurs', [\App\Http\Controllers\Api\Miscellaneous\CurrencyController::class, 'kurs']);

// Lokasi
Route::get('/load/prov', [\App\Http\Controllers\Api\Auth\CrudUserController::class, 'getProv']);
Route::get('/detail/prov/{id_prov}', [\App\Http\Controllers\Api\Auth\CrudUserController::class, 'detailProv']);

Route::get('/load/kabkot/{id_prov}', [\App\Http\Controllers\Api\Auth\CrudUserController::class, 'getKabKot']);
Route::get('/detail/kabkot/{id_kabkot}', [\App\Http\Controllers\Api\Auth\CrudUserController::class, 'detailKabKot']);

Route::get('/load/kec/{id_kabkot}', [\App\Http\Controllers\Api\Auth\CrudUserController::class, 'getKec']);
Route::get('/detail/kec/{id_kec}', [\App\Http\Controllers\Api\Auth\CrudUserController::class, 'detailKec']);

Route::get('/load/kel/{id_kec}', [\App\Http\Controllers\Api\Auth\CrudUserController::class, 'getKel']);
Route::get('/detail/kel/{id_kel}', [\App\Http\Controllers\Api\Auth\CrudUserController::class, 'detailKel']);

Route::post('password/reset', [App\Http\Controllers\Api\Auth\CrudUserController::class, 'reset_password']);
Route::post('password/update', [App\Http\Controllers\Api\Auth\CrudUserController::class, 'update_password']);

Route::post('/login', [\App\Http\Controllers\Api\Auth\AuthController::class, 'login']);

Route::group(['middleware' => 'token.checker'], function () {
    Route::get('/redirect', [\App\Http\Controllers\Api\PortalController::class, 'show']);
    Route::post('/logout', [\App\Http\Controllers\Api\Auth\AuthController::class, 'logout']);

    //app
    Route::get('app', [App\Http\Controllers\Api\Apps\AppsController::class, 'index']);
    Route::get('all/app', [App\Http\Controllers\Api\Apps\AppsController::class, 'index_all']);
    Route::get('app/get/{app_id}', [App\Http\Controllers\Api\Apps\AppsController::class, 'show']);

    //user
    Route::get('user', [App\Http\Controllers\Api\Auth\UserController::class, 'index']);
    Route::get('user/login', [App\Http\Controllers\Api\Auth\UserController::class, 'show']);
    Route::get('user/get/{id}', [App\Http\Controllers\Api\Auth\UserController::class, 'get']);
    Route::post('user/update/{id}', [App\Http\Controllers\Api\Auth\CrudUserController::class, 'user_update']);

    //akses
    Route::get('akses/app/get/{app_id}', [App\Http\Controllers\Api\Akses\AksesController::class, 'showApp']);
    Route::get('akses/user/get/{user_id}', [App\Http\Controllers\Api\Akses\AksesController::class, 'showUser']);
    Route::get('akses/mine/{app_id}/{user_id}', [App\Http\Controllers\Api\Akses\AksesController::class, 'showMine']);
});

Route::group(['middleware' => 'adminit.checker'], function () {
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

Route::group(['middleware' => 'adminsdm.checker'], function () {
    //user
    Route::post('user/add', [App\Http\Controllers\Api\Auth\CrudUserController::class, 'user_store']);

    //divisi
    Route::get('division/bom', [App\Http\Controllers\Api\Position\DivisionController::class, 'bom']);
    Route::get('division', [App\Http\Controllers\Api\Position\DivisionController::class, 'index']);
    Route::get('division/get/{id}', [App\Http\Controllers\Api\Position\DivisionController::class, 'show']);
    Route::post('division/add', [App\Http\Controllers\Api\Position\DivisionController::class, 'store']);
    Route::post('division/update/{id}', [App\Http\Controllers\Api\Position\DivisionController::class, 'update']);
    Route::post('division/active/{id}', [App\Http\Controllers\Api\Position\DivisionController::class, 'toggleActive']);

    //departement
    Route::get('department', [App\Http\Controllers\Api\Position\DepartmentController::class, 'index']);
    Route::get('department/get/{id}', [App\Http\Controllers\Api\Position\DepartmentController::class, 'show']);
    Route::get('department/get-division/{id}', [App\Http\Controllers\Api\Position\DepartmentController::class, 'showByDivisi']);
    Route::post('department/add', [App\Http\Controllers\Api\Position\DepartmentController::class, 'store']);
    Route::post('department/update/{id}', [App\Http\Controllers\Api\Position\DepartmentController::class, 'update']);
    Route::post('department/active/{id}', [App\Http\Controllers\Api\Position\DepartmentController::class, 'toggleActive']);

});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
