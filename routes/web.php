<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\AdminController;
use App\Http\Controllers\admin\LoginController;
use App\Http\Controllers\admin\UpdateProfileController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\admin\TemplateController;
use App\Http\Controllers\admin\CampaignController;
use App\Http\Controllers\admin\CustomerController;

Route::get('/', function () {
    return view('welcome');
});
Route::group(['prefix' => 'admin'],function(){
    Route::group(['middleware'=>'admin.guest'],function(){
        Route::get('login',[LoginController::class,'index'])->name('admin.login');
        Route::post('authenticate', [LoginController::class, 'authenticate'])->name('admin.authenticate');
        Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
        Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
        Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
        Route::post('password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
    });
    Route::group(['middleware'=>'admin.auth'],function(){
        Route::get('dashboard',[AdminController::class,'index'])->name('admin.dashboard');
        Route::get('updatepassword',[UpdateProfileController::class,'index'])->name('admin.updatepassword');
        Route::post('update-password', [UpdateProfileController::class, 'updatePassword'])->name('update.password');
        Route::get('updateprofile',[UpdateProfileController::class,'updateprofile'])->name('admin.updateprofile');
        Route::post('update-profile', [UpdateProfileController::class, 'updateProfileAction'])->name('update.profile');
        Route::post('update-avatar', [UpdateProfileController::class, 'updateAvatar'])->name('update.avatar');
        Route::get('gettemplate/{type}', [TemplateController::class, 'getTemplates'])->name('template.get');
        Route::get('gettemplate/add/{type}', [TemplateController::class, 'createTemplates'])->name('template.createtemplate');
        Route::get('getcampaign', [CampaignController::class, 'index'])->name('campaign.get');
        Route::get('addcampaign', [CampaignController::class, 'addacmpaign'])->name('campaign.add');
        Route::post('add-password', [UpdateProfileController::class, 'updatePassword'])->name('update.passwordaction');
        Route::post('add-campaign', [CampaignController::class, 'create'])->name('campaign.create');
        Route::get('getcustomer', [CustomerController::class, 'index'])->name('customer.get');
        Route::post('getcustomer', [CustomerController::class, 'index'])->name('customer.search');
        Route::get('getcustomer/{id}', [CustomerController::class, 'campaignwise'])->name('customer.campaignwise');
        Route::post('getcustomer/{id}', [CustomerController::class, 'campaignwise'])->name('customer.searchcampaignwise');
        Route::delete('customer/delete/{id}', [CustomerController::class, 'customerdelete'])->name('customer.customerdelete');
        Route::delete('campaign/delete/{id}', [CampaignController::class, 'campaigndelete'])->name('campaign.campaigndelete');
        Route::get('/export-csv/{id}', [CustomerController::class, 'export'])->name('customer.export');
        Route::get('logout', [LoginController::class, 'logout'])->name('admin.logout');
    });
});