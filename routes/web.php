<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\LoginController;

Route::get('/', function () {
    return view('welcome');
});
Route::group(['prefix' => 'admin'],function(){
    Route::group(['middleware'=>'admin.guest'],function(){
        Route::get('login',[LoginController::class,'index'])->name('admin.login');
        Route::post('authenticate', [LoginController::class, 'authenticate'])->name('admin.authenticate');
    });
    Route::group(['middleware'=>'admin.auth'],function(){
        Route::get('dashboard',[AdminController::class,'index'])->name('admin.dashboard');
        Route::get('logout', [LoginController::class, 'logout'])->name('admin.logout');
    });
});