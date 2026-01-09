<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReservationsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\Sanctum;

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
    Route::prefix('favorites')->group(function () {
        Route::post('/toggle', [UserController::class, 'ToggleFavorite']);
        Route::post('/add', [UserController::class, 'addToFavorites']);
        Route::delete('/remove', [UserController::class, 'removeFromFavorites']);
        Route::get('/get', [UserController::class, 'getFavorites']);
        Route::get('/check', [UserController::class, 'checkIfFavorite']);
    });



    Route::prefix('apartments')->group(function () {
        Route::post('/create', [ApartmentController::class, 'createApartments']);
        Route::delete('/destroy/{apartment}', [ApartmentController::class, 'destroyApartments']);
        Route::post('/update/{apartment}', [ApartmentController::class, 'updateApartments']);
        Route::get('/idApartments/{apartment}', [ApartmentController::class, 'showIdApartment']);
        Route::get('/user/{userId}', [ApartmentController::class, 'usersApartments']);
        Route::get('/myApartments', [ApartmentController::class, 'myApartments']);
        Route::post('/rate', [ApartmentController::class, 'rateAnApartment']);
    });
    //===========================Reservations============================================
    Route::middleware(['auth:sanctum', 'tenant'])->group(function () {
        Route::prefix('reservations')->group(function () {
            Route::post('/make', [ReservationsController::class, 'storeReservations']);
            Route::put('/update', [ReservationsController::class, 'updateReservation']);
            Route::post('/cancel', [ReservationsController::class, 'cancelReservation']);
            Route::get('/my-reservations', [ReservationsController::class, 'myReservations']);
        });
    });
    Route::middleware(['auth:sanctum', 'owner'])->group(function () {
        Route::post('reservations/{reservation}/approve', [ReservationsController::class, 'approveReservation']);
        Route::get('/owner/reservations',[ReservationsController::class, 'ownerReservations']);
    });
});

Route::middleware(['auth:sanctum'])->group(function () {

    Route::get('user/profile-photo', [UserController::class, 'profilePhoto']);

    Route::prefix('apartments')->group(function () {
        Route::get('/filtering', [ApartmentController::class, 'filtering']);
        Route::get('/allApartments', [ApartmentController::class, 'showAllApartments']);
    });
});


Route::middleware(['admin'])->group(function () {
    Route::put('users/{user}/status', [AdminController::class, 'updateStatus']);
    Route::get('users/{user}', [AdminController::class, 'show']);
    Route::get('user/id-photo/back/{id}', [UserController::class, 'idPhotoBack']);
    Route::get('user/id-photo/front/{id}', [UserController::class, 'idPhotoFront']);
    Route::delete('user/delete/{user}', [AdminController::class, 'deleteUser']);
    Route::delete('apartments/{apartment}', [AdminController::class, 'destroy']);
});
