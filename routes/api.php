<?php

use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//==================================== Auth ====================================================

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

//======================================== API's needs middleware (active + auth:sanctum) ======

Route::middleware(['auth:sanctum', 'active'])->group(function() {

    Route::get('/me', function (Request $request) {
        return $request->user();
    });

    Route::get('filtering', [ApartmentController::class, 'filtering']);
    Route::post('apartments/create',[ApartmentController::class, 'createApartments']);
    Route::delete('apartments/destroy/{apartment}',[ApartmentController::class, 'destroyApartments']);
    Route::post('apartments/update/{apartment}',[ApartmentController::class, 'updateApartments']);

    Route::get('apartments/allApartments',[ApartmentController::class, 'showAllApartments']);
    Route::get('apartments/idApartments/{apartment}',[ApartmentController::class, 'showIdApartment']);
    Route::get('apartments/user/{userId}', [ApartmentController::class, 'usersApartments']);
    Route::get('apartments/myApartments',[ApartmentController::class, 'myApartments']);

});

//=============================================== Admin ======================================



    //=====for test to delete user + his files =======================
    // Route::delete('user/delete/{user}',[ApartmentController::class, 'deleteUser'])->middleware('auth:sanctum');
    //=================================================
//===========================================================================


