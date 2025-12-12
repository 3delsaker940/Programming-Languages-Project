<?php

use App\Http\Controllers\ApartmentController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});
Route::get('filtering', [ApartmentController::class, 'filtering']);
Route::post('apartments/create',[ApartmentController::class, 'createApartments'])->middleware('auth:sanctum');
Route::delete('apartments/destroy/{apartment}',[ApartmentController::class, 'destroyApartments'])->middleware('auth:sanctum');
Route::post('apartments/update/{apartment}',[ApartmentController::class, 'updateApartments'])->middleware('auth:sanctum');

// Route::get('apartments/allApartments',[ApartmentController::class, 'showAllApartments'])->middleware('auth:sanctum');
Route::get('apartments/idApartments/{apartment}',[ApartmentController::class, 'showIdApartment'])->middleware('auth:sanctum');
Route::get('apartments/userApartments',[ApartmentController::class, 'userApartments'])->middleware('auth:sanctum');



