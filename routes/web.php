<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\LoginController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/login',[LoginController::class,'index']);
Route::get('/admin',[AdminController::class,'index']);
