<?php

use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//==================================== Auth ====================================================

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

//======================================== API's needs middleware (active + auth:sanctum) ======

Route::middleware(['auth:sanctum', 'active'])->group(function () {

    Route::get('/me', function (Request $request) {
        return $request->user();
    });

    Route::prefix('apartments')->group(function () {
        Route::get('/filtering', [ApartmentController::class, 'filtering']);
        Route::post('/create', [ApartmentController::class, 'createApartments']);
        Route::delete('/destroy/{apartment}', [ApartmentController::class, 'destroyApartments']);
        Route::post('/update/{apartment}', [ApartmentController::class, 'updateApartments']);

        Route::get('/allApartments', [ApartmentController::class, 'showAllApartments']);
        Route::get('/idApartments/{apartment}', [ApartmentController::class, 'showIdApartment']);
        Route::get('/user/{userId}', [ApartmentController::class, 'usersApartments']);
        Route::get('/myApartments', [ApartmentController::class, 'myApartments']);
    });
});

//=============================================== Admin ======================================

Route::put('user/verfied/{id}', [UserController::class, 'ChangeUserStatusToActive']);

    //=====for test to delete user + his files =======================
    // Route::delete('user/delete/{user}',[ApartmentController::class, 'deleteUser'])->middleware('auth:sanctum');
    //=================================================
//===========================================================================
