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
            Route::get('/payment/receipt/tin/view', [AssistantManagerController::class, 'payment_receipt_tin_view'])->name('payment.receipt.tin.view');
            Route::get('/payment/receipt/tin/add/{id}', [AssistantManagerController::class, 'payment_receipt_tin_add'])->name('payment.receipt.tin.add');
            Route::any('/payment/receipt/tin/pound/post', [AssistantManagerController::class, 'payment_receipt_tin_pound_post'])->name('payment.receipt.tin.pound.post');
            Route::any('/payment/receipt/tin/kg/post', [AssistantManagerController::class, 'payment_receipt_tin_kg_post'])->name('payment.receipt.tin.kg.post');
            // Columbite
            Route::get('/payment/receipt/columbite/view', [AssistantManagerController::class, 'payment_receipt_columbite_view'])->name('payment.receipt.columbite.view');
            Route::get('/payment/receipt/columbite/add/{id}', [AssistantManagerController::class, 'payment_receipt_columbite_add'])->name('payment.receipt.columbite.add');
            Route::any('/payment/receipt/columbite/pound/post', [AssistantManagerController::class, 'payment_receipt_columbite_pound_post'])->name('payment.receipt.columbite.pound.post');
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

    // Staff
    Route::get('/admin/staff', [AdminController::class, 'staff'])->name('admin.staff');
    Route::get('/admin/staff/add', [AdminController::class, 'staff_add'])->name('admin.add.staff');
    Route::post('/admin/staff/post', [AdminController::class, 'staff_post'])->name('admin.post.staff');
    Route::get('/admin/staff/edit/{id}', [AdminController::class, 'staff_edit'])->name('admin.edit.staff');
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
    Route::get('/admin/rates/list/berating', [AdminController::class, 'rates_berating'])->name('admin.rates.berating');
    Route::get('/admin/rates/list/berating/add', [AdminController::class, 'add_rate_berating'])->name('admin.add.rate.berating');
    Route::post('/admin/rates/list/berating/post', [AdminController::class, 'post_rate_berating'])->name('admin.post.rate.berating');
    Route::post('/admin/rates/list/berating/update/{id}', [AdminController::class, 'rate_berating_update'])->name('admin.update.rate.berating');
    Route::get('/admin/rates/list/berating/activate/{id}', [AdminController::class, 'rate_berating_activate'])->name('admin.activate.rate.berating');
    Route::get('/admin/rates/list/berating/deactivate/{id}', [AdminController::class, 'rate_berating_deactivate'])->name('admin.deactivate.rate.berating');
    Route::post('/admin/rates/list/berating/delete/{id}', [AdminController::class, 'rate_berating_delete'])->name('admin.delete.rate.berating');

    Route::get('/admin/rates/list/analysis', [AdminController::class, 'rates_analysis'])->name('admin.rates.analysis');
    Route::get('/admin/rates/list/analysis/add', [AdminController::class, 'add_rate_analysis'])->name('admin.add.rate.analysis');
    Route::post('/admin/rates/list/analysis/post', [AdminController::class, 'post_rate_analysis'])->name('admin.post.rate.analysis');
    Route::post('/admin/rates/list/analysis/update/{id}', [AdminController::class, 'rate_analysis_update'])->name('admin.update.rate.analysis');
    Route::get('/admin/rates/list/analysis/activate/{id}', [AdminController::class, 'rate_analysis_activate'])->name('admin.activate.rate.analysis');
    Route::get('/admin/rates/list/analysis/deactivate/{id}', [AdminController::class, 'rate_analysis_deactivate'])->name('admin.deactivate.rate.analysis');
    Route::post('/admin/rates/list/analysis/delete/{id}', [AdminController::class, 'rate_analysis_delete'])->name('admin.delete.rate.analysis');

    // Payment Receipts
    //Tin
    Route::get('/admin/payment/receipt/tin/view', [AdminController::class, 'payment_receipt_tin_view'])->name('admin.payment.receipt.tin.view');
    Route::get('/admin/payment/receipt/tin/add/{id}', [AdminController::class, 'payment_receipt_tin_add'])->name('admin.payment.receipt.tin.add');
    Route::any('/admin/payment/receipt/tin/pound/post', [AdminController::class, 'payment_receipt_tin_pound_post'])->name('admin.payment.receipt.tin.pound.post');
    Route::any('/admin/payment/receipt/tin/kg/post', [AdminController::class, 'payment_receipt_tin_kg_post'])->name('admin.payment.receipt.tin.kg.post');
    Route::get('/admin/payment/receipt/tin/edit/{id}', [AdminController::class, 'payment_receipt_tin_edit'])->name('admin.payment.receipt.tin.edit');
    Route::post('/admin/payment/receipt/tin/pound/update/{id}', [AdminController::class, 'payment_receipt_tin_pound_update'])->name('admin.payment.receipt.tin.pound.update');
    Route::post('/admin/payment/receipt/tin/kg/update/{id}', [AdminController::class, 'payment_receipt_tin_kg_update'])->name('admin.payment.receipt.tin.kg.update');
    Route::post('/admin/payment/receipt/tin/delete/{id}', [AdminController::class, 'payment_receipt_tin_delete'])->name('admin.payment.receipt.tin.delete');
    // Columbite
    Route::get('/admin/payment/receipt/columbite/view', [AdminController::class, 'payment_receipt_columbite_view'])->name('admin.payment.receipt.columbite.view');
    Route::get('/admin/payment/receipt/columbite/add', [AdminController::class, 'payment_receipt_columbite_add'])->name('admin.payment.receipt.columbite.add');
    Route::any('/admin/payment/receipt/columbite/post', [AdminController::class, 'payment_receipt_columbite_post'])->name('admin.payment.receipt.columbite.post');
    Route::get('/admin/payment/receipt/columbite/edit/{id}', [AdminController::class, 'payment_receipt_columbite_edit'])->name('admin.payment.receipt.columbite.edit');
    Route::post('/admin/payment/receipt/columbite/update/{id}', [AdminController::class, 'payment_receipt_columbite_update'])->name('admin.payment.receipt.columbite.update');
    Route::post('/admin/payment/receipt/columbite/delete/{id}', [AdminController::class, 'payment_receipt_columbite_delete'])->name('admin.payment.receipt.columbite.delete');

    // Weekly Analysis
    Route::any('/admin/weekly/analysis/tin/pound', [AdminController::class, 'weekly_analysis_tin_pound'])->name('admin.weekly.analysis.tin.pound');
    Route::post('/admin/weekly/analysis/tin/pounds', [AdminController::class, 'weekly_analysis_tin_pounds'])->name('admin.weekly.analysis.tin.pounds');

    Route::any('/admin/weekly/analysis/tin/kg', [AdminController::class, 'weekly_analysis_tin_kg'])->name('admin.weekly.analysis.tin.kg');
    Route::any('/admin/weekly/analysis/columbite/pound', [AdminController::class, 'weekly_analysis_columbite_pound'])->name('admin.weekly.analysis.columbite.pound');
});
