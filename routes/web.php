<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin', function() {
    return view('admin.dashboard');
});

Route::get('/admin/test', function() {
    // جلب أول 5 مستخدمين من جدول users
    $users = DB::table('users')->limit(5)->get();
    return view('admin.test', ['users' => $users]);
});

//=============================
Route::get('admin/users', function(){
    $user = DB::table('users')->get();
    return view('admin.users', ['users' => $user]);
});