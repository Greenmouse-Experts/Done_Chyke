<?php

use App\Http\Controllers\AccountantController;
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
            // Expenses
            Route::get('/expenses/view', [AccountantController::class, 'expenses_view'])->name('expenses.view');
            Route::get('/expenses/add', [AccountantController::class, 'expenses_add'])->name('expenses.add');
            Route::post('/expenses/post', [AccountantController::class, 'expenses_post'])->name('expenses.post');
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
            Route::any('/payment/analysis/tin/pound/post', [AssistantManagerController::class, 'payment_analysis_tin_pound_post'])->name('payment.analysis.tin.pound.post');
            Route::any('/payment/analysis/tin/kg/post', [AssistantManagerController::class, 'payment_analysis_tin_kg_post'])->name('payment.analysis.tin.kg.post');
            // Columbite
            Route::get('/payment/analysis/columbite/view', [AssistantManagerController::class, 'payment_analysis_columbite_view'])->name('payment.analysis.columbite.view');
            Route::get('/payment/analysis/columbite/add', [AssistantManagerController::class, 'payment_analysis_columbite_add'])->name('payment.analysis.columbite.add');
            Route::any('/payment/analysis/columbite/pound/post', [AssistantManagerController::class, 'payment_analysis_columbite_pound_post'])->name('payment.analysis.columbite.pound.post');
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

    // Expenses
    Route::get('/admin/expenses', [AdminController::class, 'expenses'])->name('admin.expenses');
    Route::post('/admin/expenses/update/{id}', [AdminController::class, 'update_expense'])->name('admin.expense.update');
    Route::post('/admin/expenses/delete/{id}', [AdminController::class, 'delete_expense'])->name('admin.expense.delete');

    // Rates
    Route::get('/admin/rates/berating', [AdminController::class, 'rates_berating'])->name('admin.rates.berating');
    Route::get('/admin/rates/berating/add', [AdminController::class, 'add_rate_berating'])->name('admin.add.rate.berating');
    Route::post('/admin/rates/berating/post', [AdminController::class, 'post_rate_berating'])->name('admin.post.rate.berating');
    Route::post('/admin/rates/berating/update/{id}', [AdminController::class, 'rate_berating_update'])->name('admin.update.rate.berating');
    Route::get('/admin/rates/berating/activate/{id}', [AdminController::class, 'rate_berating_activate'])->name('admin.activate.rate.berating');
    Route::get('/admin/rates/berating/deactivate/{id}', [AdminController::class, 'rate_berating_deactivate'])->name('admin.deactivate.rate.berating');
    Route::post('/admin/rates/berating/delete/{id}', [AdminController::class, 'rate_berating_delete'])->name('admin.delete.rate.berating');

    Route::get('/admin/rates/analysis', [AdminController::class, 'rates_analysis'])->name('admin.rates.analysis');
    Route::get('/admin/rates/analysis/add', [AdminController::class, 'add_rate_analysis'])->name('admin.add.rate.analysis');
    Route::post('/admin/rates/analysis/post', [AdminController::class, 'post_rate_analysis'])->name('admin.post.rate.analysis');
    Route::post('/admin/rates/analysis/update/{id}', [AdminController::class, 'rate_analysis_update'])->name('admin.update.rate.analysis');
    Route::get('/admin/rates/analysis/activate/{id}', [AdminController::class, 'rate_analysis_activate'])->name('admin.activate.rate.analysis');
    Route::get('/admin/rates/analysis/deactivate/{id}', [AdminController::class, 'rate_analysis_deactivate'])->name('admin.deactivate.rate.analysis');
    Route::post('/admin/rates/analysis/delete/{id}', [AdminController::class, 'rate_analysis_delete'])->name('admin.delete.rate.analysis');

    // Payment Voucher
    //Tin
    Route::get('/admin/payment/voucher/tin/view', [AdminController::class, 'payment_voucher_tin_view'])->name('admin.payment.voucher.tin.view');
    // Columbite
    Route::get('/admin/payment/voucher/columbite/view', [AdminController::class, 'payment_voucher_columbite_view'])->name('admin.payment.voucher.columbite.view');
});
