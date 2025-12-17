<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProvinceController;
use App\Http\Controllers\Api\CityController;
use App\Http\Controllers\Api\DistrictController;
use App\Http\Controllers\Api\LogController;

/*
|--------------------------------------------------------------------------
| RUTE STANDAR LARAVEL (Opsional)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| RUTE PUBLIK (TIDAK MEMERLUKAN TOKEN JWT)
|--------------------------------------------------------------------------
| Rute Read-Only untuk data publik.
*/

// Rute Province Publik
Route::prefix('/province')->group(function () {
    Route::get('', [ProvinceController::class, 'index']);      // GET /api/province
    Route::get('/{id}', [ProvinceController::class, 'detail']); // GET /api/province/{id}
});

// Rute City Publik
Route::prefix('/city')->group(function () {
    Route::get('/', [CityController::class, 'index']);                  // GET /api/city
    Route::get('/province/{id}', [CityController::class, 'getByProvince']); // GET /api/city/province/{id}
    Route::get('/{id}', [CityController::class, 'detail']);             // GET /api/city/{id}
});

// Rute District Publik
Route::prefix('/district')->group(function () {
    Route::get('/', [DistrictController::class, 'index']);             // GET /api/district
    Route::get('/city/{id}', [DistrictController::class, 'getByCity']);  // GET /api/district/city/{id}
    Route::get('/{id}', [DistrictController::class, 'detail']);         // GET /api/district/{id}
});


/*
|--------------------------------------------------------------------------
| RUTE DILINDUNGI (MEMERLUKAN TOKEN JWT)
|--------------------------------------------------------------------------
| Rute untuk operasi CUD (Create, Update, Delete) dan Auth Management.
*/

Route::middleware(['jwt'])->group(function () {

    // --- JWT & Auth Management ---
    Route::get('me',        [AuthController::class, 'me']);
    Route::get('refresh',  [AuthController::class, 'refresh']);
    Route::get('logout',   [AuthController::class, 'logout']);

    // --- Province CUD Routes ---
    Route::prefix('/province')->group(function () {
        Route::post('',         [ProvinceController::class, 'create']);
        Route::put('/{id}',     [ProvinceController::class, 'update']);
        Route::patch('/{id}',   [ProvinceController::class, 'patch']);
        Route::delete('/{id}',  [ProvinceController::class, 'delete']);
    });

    // --- City CUD Routes ---
    Route::prefix('/city')->group(function () {
        Route::post('/',        [CityController::class, 'create']);
        Route::put('/{id}',     [CityController::class, 'update']);
        Route::delete('/{id}',  [CityController::class, 'delete']);
    });

    // --- District CUD Routes ---
    Route::prefix('/district')->group(function () {
        Route::post('/',        [DistrictController::class, 'create']);
        Route::put('/{id}',     [DistrictController::class, 'update']);
        Route::delete('/{id}',  [DistrictController::class, 'delete']);
    });
    
    // --- Log Routes ---
    Route::prefix('/log')->group(function () {
        Route::get('/', [LogController::class, 'index']); 
    });
});