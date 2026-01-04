<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin', function() {
    return view('admin.dashboard');
});

Route::get('/admin/login', [AuthController::class, 'showLogin'])
    ->middleware('guest')
    ->name('admin.login');

Route::post('/admin/login', [AuthController::class, 'loginAdmin'])
    ->middleware('guest')
    ->name('admin.login.post');

Route::post('/admin/logout', [AuthController::class, 'logoutAdmin'])
    ->middleware('auth')
    ->name('admin.logout');

//=============================

Route::get('admin/users', function(){
    $users = DB::table('users')->get();
    $apartments = DB::table('apartments')->get();
    return view('admin.users', compact('users', 'apartments'));
})->middleware('auth','admin')->name('admin.users');

//============================================

Route::get('/lang/{lang}', function ($lang) {
    if (!in_array($lang, ['en', 'ar'])) {
        abort(400);
    }

    session(['locale' => $lang]);
    return redirect()->back();
})->name('lang.switch');



// Route::delete('/user/delete/{user}', [AdminController::class, 'deleteUser'])->name('user.delete');

