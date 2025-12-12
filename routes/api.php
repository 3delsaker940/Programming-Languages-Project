<?php

use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

Route::post('apartments/create',[ApartmentController::class, 'createApartments'])->middleware('auth:sanctum');
Route::delete('apartments/destroy/{apartment}',[ApartmentController::class, 'destroyApartments'])->middleware('auth:sanctum');
Route::post('apartments/update/{apartment}',[ApartmentController::class, 'updateApartments'])->middleware('auth:sanctum');

Route::get('apartments/allApartments',[ApartmentController::class, 'showAllApartments'])->middleware('auth:sanctum');
Route::get('apartments/idApartments/{apartment}',[ApartmentController::class, 'showIdApartment'])->middleware('auth:sanctum');
Route::get('apartments/user/{userId}', [ApartmentController::class, 'usersApartments'])->middleware('auth:sanctum');
Route::get('apartments/myApartments',[ApartmentController::class, 'myApartments'])->middleware('auth:sanctum');

//=====for test to delete user + his files =======================
// Route::delete('user/delete/{user}',[ApartmentController::class, 'deleteUser'])->middleware('auth:sanctum');
//=================================================




