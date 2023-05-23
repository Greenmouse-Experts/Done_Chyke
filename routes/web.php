<?php

use App\Http\Controllers\AccountantDashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AssistantManagerController;
use App\Http\Controllers\AssistantManagerDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
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
Route::get('/forgot', [AuthController::class, 'forgot'])->name('forgot');
Route::post('/password/forget', [AuthController::class, 'forget_password'])->name('forget.password');
Route::get('/reset/password/email/{email}', [AuthController::class, 'password_reset_email'])->name('reset.password');
Route::post('update/password/reset/{id}', [AuthController::class, 'reset_password'])->name('update.new.password');
Route::post('/user/login', [AuthController::class, 'user_login'])->name('user.login');
Route::get('/admin/login', [AuthController::class, 'admin_login'])->name('admin.login');
Route::post('/post/admin/login', [AuthController::class, 'post_admin_login'])->name('post.admin.login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// General of both Accountant and Assistant Manager
Route::prefix('/dashboard')->group(
    function () {
        Route::get('/', [DashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/read/notification/{id}', [DashboardController::class, 'read_notification'])->name('read.notification');
        Route::post('/update/profile/password', [DashboardController::class, 'update_password'])->name('update.password');
        Route::post('/update/profile', [DashboardController::class, 'update_profile'])->name('update.profile');
        Route::post('/upload/profile/picture', [DashboardController::class, 'upload_profile_picture'])->name('upload.profile.picture');
        Route::get('/notifications', [DashboardController::class, 'notifications'])->name('notifications');
        Route::get('/profile', [DashboardController::class, 'profile'])->name('profile');
    }
);

// Accountant
Route::middleware(['auth', 'isAccountant'])->group(function () {
    Route::prefix('/accountant')->group(
        function () {
        }
    );
});

// Assistant Manager
Route::middleware(['auth', 'isAssistantManager'])->group(function () {
    Route::prefix('/assistant-manager')->group(
        function () {
            //Tin
            Route::get('/payment/analysis/tin/view', [AssistantManagerController::class, 'payment_analysis_tin_view'])->name('payment.analysis.tin.view');
            Route::get('/payment/analysis/tin/add', [AssistantManagerController::class, 'payment_analysis_tin_add'])->name('payment.analysis.tin.add');
            Route::get('/payment/analysis/tin/post', [AssistantManagerController::class, 'payment_analysis_tin_post'])->name('payment.analysis.tin.post');
            // Columbite
            Route::get('/payment/analysis/columbite/view', [AssistantManagerController::class, 'payment_analysis_columbite_view'])->name('payment.analysis.columbite.view');
            Route::get('/payment/analysis/columbite/add', [AssistantManagerController::class, 'payment_analysis_columbite_add'])->name('payment.analysis.columbite.add');
        }
    );
});

Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/profile', [AdminController::class, 'profile'])->name('admin.profile');
    Route::post('/admin/update/profile/password', [AdminController::class, 'update_admin_password'])->name('admin.update.password');
    Route::post('/admin/update/profile', [AdminController::class, 'update_admin_profile'])->name('admin.update.profile');
    Route::post('/admin/upload/profile/picture', [AdminController::class, 'upload_admin_profile_picture'])->name('admin.upload.profile.picture');
    
    // Fund Account
    Route::get('/admin/account/funding/confirm/{response}/{amount}', [AdminController::class, 'account_funding_confirm'])->name('admin.account.funding.confirm');

    // Manager
    Route::get('/admin/staff/managers', [AdminController::class, 'managers'])->name('admin.managers');
    Route::get('/admin/staff/manager/add', [AdminController::class, 'manager_add'])->name('admin.add.manager');
    Route::post('/admin/staff/manager/post', [AdminController::class, 'manager_post'])->name('admin.post.manager');
    Route::get('/admin/staff/manager/edit/{id}', [AdminController::class, 'manager_edit'])->name('admin.edit.manager');
    Route::post('/admin/staff/manager/update/{id}', [AdminController::class, 'manager_update'])->name('admin.update.manager');
    Route::get('/admin/staff/manager/activate/{id}', [AdminController::class, 'manager_activate'])->name('admin.activate.manager');
    Route::get('/admin/staff/manager/deactivate/{id}', [AdminController::class, 'manager_deactivate'])->name('admin.deactivate.manager');
    Route::post('/admin/staff/manager/delete/{id}', [AdminController::class, 'manager_delete'])->name('admin.delete.manager');

    // Accountant
    Route::get('/admin/staff/accountants', [AdminController::class, 'accountants'])->name('admin.accountants');
    Route::get('/admin/staff/accountant/add', [AdminController::class, 'accountant_add'])->name('admin.add.accountant');
    Route::post('/admin/staff/accountant/post', [AdminController::class, 'accountant_post'])->name('admin.post.accountant');
    Route::get('/admin/staff/accountant/edit/{id}', [AdminController::class, 'accountant_edit'])->name('admin.edit.accountant');

    // Assistant Manager
    Route::get('/admin/staff/assistance/manager', [AdminController::class, 'manager_assistances'])->name('admin.manager.assistances');
    Route::get('/admin/staff/assistance/manager/add', [AdminController::class, 'manager_assistance_add'])->name('admin.add.manager.assistance');
    Route::post('/admin/staff/assistance/manager/post', [AdminController::class, 'manager_assistance_post'])->name('admin.post.manager.assistance');
    Route::get('/admin/staff/assistance/manager/edit/{id}', [AdminController::class, 'assistance_manager_edit'])->name('admin.edit.manager.assistance');
    
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

    // Calculations
    Route::get('/admin/calculations/berating', [AdminController::class, 'calculations_berating'])->name('admin.calculations.berating');
    Route::get('/admin/calculations/berating/add', [AdminController::class, 'add_calculation_berating'])->name('admin.add.calculation.berating');
    Route::post('/admin/calculations/berating/post', [AdminController::class, 'post_calculation_berating'])->name('admin.post.calculation.berating');
    Route::post('/admin/calculations/berating/update/{id}', [AdminController::class, 'calculation_berating_update'])->name('admin.update.calculation.berating');
    Route::get('/admin/calculations/berating/activate/{id}', [AdminController::class, 'calculation_berating_activate'])->name('admin.activate.calculation.berating');
    Route::get('/admin/calculations/berating/deactivate/{id}', [AdminController::class, 'calculation_berating_deactivate'])->name('admin.deactivate.calculation.berating');
    Route::post('/admin/calculations/berating/delete/{id}', [AdminController::class, 'calculation_berating_delete'])->name('admin.delete.calculation.berating');

    Route::get('/admin/calculations/analysis', [AdminController::class, 'calculations_analysis'])->name('admin.calculations.analysis');
    Route::get('/admin/calculations/analysis/add', [AdminController::class, 'add_calculation_analysis'])->name('admin.add.calculation.analysis');
    Route::post('/admin/calculations/analysis/post', [AdminController::class, 'post_calculation_analysis'])->name('admin.post.calculation.analysis');
    Route::post('/admin/calculations/analysis/update/{id}', [AdminController::class, 'calculation_analysis_update'])->name('admin.update.calculation.analysis');
    Route::get('/admin/calculations/analysis/activate/{id}', [AdminController::class, 'calculation_analysis_activate'])->name('admin.activate.calculation.analysis');
    Route::get('/admin/calculations/analysis/deactivate/{id}', [AdminController::class, 'calculation_analysis_deactivate'])->name('admin.deactivate.calculation.analysis');
    Route::post('/admin/calculations/analysis/delete/{id}', [AdminController::class, 'calculation_analysis_delete'])->name('admin.delete.calculation.analysis');
});
