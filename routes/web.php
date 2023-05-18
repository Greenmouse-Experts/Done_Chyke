<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', [AuthController::class, 'index'])->name('index');
Route::post('/post/login', [AuthController::class, 'post_login'])->name('post.login');
Route::get('/admin/login', [AuthController::class, 'admin_login'])->name('admin.login');
Route::post('/post/admin/login', [AuthController::class, 'post_admin_login'])->name('post.admin.login');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/profile', [AdminController::class, 'profile'])->name('admin.profile');
    Route::post('/admin/update/profile/password', [AdminController::class, 'update_admin_password'])->name('admin.update.password');
    Route::post('/admin/update/profile', [AdminController::class, 'update_admin_profile'])->name('admin.update.profile');
    Route::post('/admin/upload/profile/picture', [AdminController::class, 'upload_admin_profile_picture'])->name('admin.upload.profile.picture');
    
    // Fund Account
    Route::get('/admin/account/funding/confirm/{response}/{amount}', [AdminController::class, 'account_funding_confirm'])->name('admin.account.funding.confirm');

    // Manager
    Route::get('/admin/managers', [AdminController::class, 'managers'])->name('admin.managers');
    Route::get('/admin/manager/add', [AdminController::class, 'manager_add'])->name('admin.add.manager');
    Route::post('/admin/manager/post', [AdminController::class, 'manager_post'])->name('admin.post.manager');
    Route::get('/admin/manager/edit/{id}', [AdminController::class, 'manager_edit'])->name('admin.edit.manager');
    Route::post('/admin/manager/update/{id}', [AdminController::class, 'manager_update'])->name('admin.update.manager');
    Route::get('/admin/manager/activate/{id}', [AdminController::class, 'manager_activate'])->name('admin.activate.manager');
    Route::get('/admin/manager/deactivate/{id}', [AdminController::class, 'manager_deactivate'])->name('admin.deactivate.manager');
    Route::post('/admin/manager/delete/{id}', [AdminController::class, 'manager_delete'])->name('admin.delete.manager');

    // Accountant
    Route::get('/admin/accountants', [AdminController::class, 'accountants'])->name('admin.accountants');
    Route::get('/admin/accountant/add', [AdminController::class, 'accountant_add'])->name('admin.add.accountant');
    Route::post('/admin/accountant/post', [AdminController::class, 'accountant_post'])->name('admin.post.accountant');
    Route::get('/admin/accountant/edit/{id}', [AdminController::class, 'accountant_edit'])->name('admin.edit.accountant');

    // Assistant Manager
    Route::get('/admin/assistance/manager', [AdminController::class, 'manager_assistances'])->name('admin.manager.assistances');
    Route::get('/admin/assistance/manager/add', [AdminController::class, 'manager_assistance_add'])->name('admin.add.manager.assistance');
    Route::post('/admin/assistance/manager/post', [AdminController::class, 'manager_assistance_post'])->name('admin.post.manager.assistance');
    Route::get('/admin/assistance/manager/edit/{id}', [AdminController::class, 'assistance_manager_edit'])->name('admin.edit.manager.assistance');
    
    // General Settings
    Route::post('/admin/staff/update/profile{id}', [AdminController::class, 'staff_update_profile'])->name('admin.update.staff.profile');
    Route::post('/admin/staff/update/password/{id}', [AdminController::class, 'staff_update_password'])->name('admin.update.staff.password');
    Route::post('/admin/staff/update/profile-picture/{id}', [AdminController::class, 'staff_update_profile_picture'])->name('admin.update.staff.profile.picture');
    Route::get('/admin/staff/activate/{id}', [AdminController::class, 'staff_activate'])->name('admin.activate.staff');
    Route::get('/admin/staff/deactivate/{id}', [AdminController::class, 'staff_deactivate'])->name('admin.deactivate.staff');
    Route::post('/admin/staff/delete/{id}', [AdminController::class, 'staff_delete'])->name('admin.delete.staff');

    // Notifications
    Route::get('/admin/notifications', [AdminController::class, 'notifications'])->name('admin.notifications');
    Route::get('/admin/read/notification/{id}', [AdminController::class, 'read_notification'])->name('admin.read.notification');

    // Transactions
    Route::get('/admin/transactions', [AdminController::class, 'transactions'])->name('admin.transactions');
});
