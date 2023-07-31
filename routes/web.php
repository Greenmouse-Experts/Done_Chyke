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

Route::get('/run', function () {
    \Illuminate\Support\Facades\Artisan::call('schedule:run');
    return 'Successful!';
});

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

        Route::post('/add/berating/rate', [DashboardController::class, 'add_berating_rate'])->name('add.berating.rate');
        Route::post('/get/berating/rate', [DashboardController::class, 'get_berating_rate'])->name('get.berating.rate');
        Route::post('/update/berating/rate', [DashboardController::class, 'update_berating_rate'])->name('update.berating.rate');
    }
);

// Accountant
Route::middleware(['auth', 'isAccountant'])->group(function () {
    Route::prefix('/accountant')->group(
        function () {
            // Expenses
            Route::any('/expenses/view', [AccountantController::class, 'expenses_view'])->name('expenses.view');
            Route::get('/expenses/add', [AccountantController::class, 'expenses_add'])->name('expenses.add');
            Route::post('/expenses/post', [AccountantController::class, 'expenses_post'])->name('expenses.post');
            // Balance
            Route::any('/daily/balance', [AccountantController::class, 'daily_balance'])->name('daily.balance');
            Route::post('/daily/balance/add', [AccountantController::class, 'daily_balance_add'])->name('daily.balance.add');
        }
    );
});

// Assistant Manager
Route::middleware(['auth', 'isAssistantManager'])->group(function () {
    Route::prefix('/general')->group(
        function () {
            //Tin
            Route::any('/payment/receipt/tin/view/{id}', [AssistantManagerController::class, 'payment_receipt_tin_view'])->name('payment.receipt.tin.view');
            Route::get('/payment/receipt/tin/add/{id}', [AssistantManagerController::class, 'payment_receipt_tin_add'])->name('payment.receipt.tin.add');
            Route::any('/payment/receipt/tin/pound/post', [AssistantManagerController::class, 'payment_receipt_tin_pound_post'])->name('payment.receipt.tin.pound.post');
            Route::any('/payment/receipt/tin/kg/post', [AssistantManagerController::class, 'payment_receipt_tin_kg_post'])->name('payment.receipt.tin.kg.post');
            // Columbite
            Route::any('/payment/receipt/columbite/view/{id}', [AssistantManagerController::class, 'payment_receipt_columbite_view'])->name('payment.receipt.columbite.view');
            Route::get('/payment/receipt/columbite/add/{id}', [AssistantManagerController::class, 'payment_receipt_columbite_add'])->name('payment.receipt.columbite.add');
            Route::any('/payment/receipt/columbite/pound/post', [AssistantManagerController::class, 'payment_receipt_columbite_pound_post'])->name('payment.receipt.columbite.pound.post');
            Route::any('/payment/receipt/columbite/kg/post', [AssistantManagerController::class, 'payment_receipt_columbite_kg_post'])->name('payment.receipt.columbite.kg.post');
            // LowerGrade
            Route::any('/payment/receipt/lower/grade/columbite/view/{id}', [AssistantManagerController::class, 'payment_receipt_lower_grade_columbite_view'])->name('payment.receipt.lower.grade.columbite.view');
            Route::get('/payment/receipt/lower/grade/columbite/add/{id}', [AssistantManagerController::class, 'payment_receipt_lower_grade_columbite_add'])->name('payment.receipt.lower.grade.columbite.add');
            Route::any('/payment/receipt/lower/grade/columbite/pound/post', [AssistantManagerController::class, 'payment_receipt_lower_grade_columbite_pound_post'])->name('payment.receipt.lower.grade.columbite.pound.post');
            Route::any('/payment/receipt/lower/grade/columbite/kg/post', [AssistantManagerController::class, 'payment_receipt_lower_grade_columbite_kg_post'])->name('payment.receipt.lower.grade.columbite.kg.post');

            // Weekly Material Summary
            Route::any('/weekly/material/summary/tin/pound', [AssistantManagerController::class, 'weekly_material_summary_tin_pound'])->name('weekly.material.summary.tin.pound');
            Route::any('/weekly/material/summary/tin/kg', [AssistantManagerController::class, 'weekly_material_summary_tin_kg'])->name('weekly.material.summary.tin.kg');
            Route::any('/weekly/material/summary/columbite/pound', [AssistantManagerController::class, 'weekly_material_summary_columbite_pound'])->name('weekly.material.summary.columbite.pound');
            Route::any('/weekly/material/summary/columbite/kg', [AssistantManagerController::class, 'weekly_material_summary_columbite_kg'])->name('weekly.material.summary.columbite.kg');
            Route::any('/weekly/material/summary/low/grade/pound', [AssistantManagerController::class, 'weekly_material_summary_low_grade_pound'])->name('weekly.material.summary.low.grade.pound');
            Route::any('/weekly/material/summary/low/grade/kg', [AssistantManagerController::class, 'weekly_material_summary_low_grade_kg'])->name('weekly.material.summary.low.grade.kg');

            // Rates
            Route::get('/rates/list/berating', [AssistantManagerController::class, 'rates_berating'])->name('rates.berating');
            Route::get('/rates/list/berating/add', [AssistantManagerController::class, 'add_rate_berating'])->name('add.rate.berating');
            Route::post('/rates/list/berating/post', [AssistantManagerController::class, 'post_rate_berating'])->name('post.rate.berating');
            Route::post('/rates/list/berating/update/{id}', [AssistantManagerController::class, 'rate_berating_update'])->name('update.rate.berating');
            Route::get('/rates/list/berating/activate/{id}', [AssistantManagerController::class, 'rate_berating_activate'])->name('activate.rate.berating');
            Route::get('/rates/list/berating/deactivate/{id}', [AssistantManagerController::class, 'rate_berating_deactivate'])->name('deactivate.rate.berating');
            Route::post('/rates/list/berating/delete/{id}', [AssistantManagerController::class, 'rate_berating_delete'])->name('delete.rate.berating');

            Route::get('/rates/list/analysis', [AssistantManagerController::class, 'rates_analysis'])->name('rates.analysis');
            Route::get('/rates/list/analysis/add', [AssistantManagerController::class, 'add_rate_analysis'])->name('add.rate.analysis');
            Route::post('/rates/list/analysis/post', [AssistantManagerController::class, 'post_rate_analysis'])->name('post.rate.analysis');
            Route::post('/rates/list/analysis/update/{id}', [AssistantManagerController::class, 'rate_analysis_update'])->name('update.rate.analysis');
            Route::get('/rates/list/analysis/activate/{id}', [AssistantManagerController::class, 'rate_analysis_activate'])->name('activate.rate.analysis');
            Route::get('/rates/list/analysis/deactivate/{id}', [AssistantManagerController::class, 'rate_analysis_deactivate'])->name('deactivate.rate.analysis');
            Route::post('/rates/list/analysis/delete/{id}', [AssistantManagerController::class, 'rate_analysis_delete'])->name('delete.rate.analysis');
        
            Route::get('/rates/list/benchmark', [AssistantManagerController::class, 'rates_beanchmark'])->name('rates.benchmark');
            Route::post('/rates/list/benchmark/post', [AssistantManagerController::class, 'post_rate_benchmark'])->name('post.rate.benchmark');
            Route::post('/rates/list/benchmark/update/{id}', [AssistantManagerController::class, 'rate_benchmark_update'])->name('update.rate.benchmark');
            Route::post('/rates/list/benchmark/delete/{id}', [AssistantManagerController::class, 'rate_benchmark_delete'])->name('delete.rate.benchmark');
        
        }
    );
});

Route::middleware(['auth', 'isAdmin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/profile', [AdminController::class, 'profile'])->name('admin.profile');
    Route::post('/admin/update/profile/password', [AdminController::class, 'update_admin_password'])->name('admin.update.password');
    Route::post('/admin/update/profile', [AdminController::class, 'update_admin_profile'])->name('admin.update.profile');
    Route::post('/admin/upload/profile/picture', [AdminController::class, 'upload_admin_profile_picture'])->name('admin.upload.profile.picture');
    
    // Sub Admins
    Route::get('/admin/sub/admins', [AdminController::class, 'sub_admins'])->name('admin.sub.admins');
    Route::post('/admin/sub/admin/add', [AdminController::class, 'add_sub_admin'])->name('admin.add.sub.admin');
    Route::post('/admin/sub/admin/update/{id}', [AdminController::class, 'sub_admin_update'])->name('admin.update.sub.admin');
    Route::get('/admin/sub/admin/activate/{id}', [AdminController::class, 'sub_admin_activate'])->name('admin.activate.sub.admin');
    Route::get('/admin/sub/admin/deactivate/{id}', [AdminController::class, 'sub_admin_deactivate'])->name('admin.deactivate.sub.admin');
    Route::post('/admin/sub/admin/delete/{id}', [AdminController::class, 'sub_admin_delete'])->name('admin.delete.sub.admin');

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
    Route::any('/admin/expenses', [AdminController::class, 'expenses'])->name('admin.expenses');
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

    Route::get('/admin/rates/list/benchmark', [AdminController::class, 'rates_beanchmark'])->name('admin.rates.benchmark');
    Route::post('/admin/rates/list/benchmark/post', [AdminController::class, 'post_rate_benchmark'])->name('admin.post.rate.benchmark');
    Route::post('/admin/rates/list/benchmark/update/{id}', [AdminController::class, 'rate_benchmark_update'])->name('admin.update.rate.benchmark');
    Route::post('/admin/rates/list/benchmark/delete/{id}', [AdminController::class, 'rate_benchmark_delete'])->name('admin.delete.rate.benchmark');

    // Payment Receipts
    //Tin
    Route::any('/admin/payment/receipt/tin/view/{id}', [AdminController::class, 'payment_receipt_tin_view'])->name('admin.payment.receipt.tin.view');
    Route::get('/admin/payment/receipt/tin/add/{id}', [AdminController::class, 'payment_receipt_tin_add'])->name('admin.payment.receipt.tin.add');
    Route::any('/admin/payment/receipt/tin/pound/post', [AdminController::class, 'payment_receipt_tin_pound_post'])->name('admin.payment.receipt.tin.pound.post');
    Route::any('/admin/payment/receipt/tin/kg/post', [AdminController::class, 'payment_receipt_tin_kg_post'])->name('admin.payment.receipt.tin.kg.post');
    Route::get('/admin/payment/receipt/tin/edit/{id}', [AdminController::class, 'payment_receipt_tin_edit'])->name('admin.payment.receipt.tin.edit');
    Route::post('/admin/payment/receipt/tin/pound/update/{id}', [AdminController::class, 'payment_receipt_tin_pound_update'])->name('admin.payment.receipt.tin.pound.update');
    Route::post('/admin/payment/receipt/tin/kg/update/{id}', [AdminController::class, 'payment_receipt_tin_kg_update'])->name('admin.payment.receipt.tin.kg.update');
    Route::post('/admin/payment/receipt/tin/delete/{id}/{type}', [AdminController::class, 'payment_receipt_tin_delete'])->name('admin.payment.receipt.tin.delete');
    // Columbite
    Route::any('/admin/payment/receipt/columbite/view/{id}', [AdminController::class, 'payment_receipt_columbite_view'])->name('admin.payment.receipt.columbite.view');
    Route::get('/admin/payment/receipt/columbite/add/{id}', [AdminController::class, 'payment_receipt_columbite_add'])->name('admin.payment.receipt.columbite.add');
    Route::any('/admin/payment/receipt/columbite/pound/post', [AdminController::class, 'payment_receipt_columbite_pound_post'])->name('admin.payment.receipt.columbite.pound.post');
    Route::any('/admin/payment/receipt/columbite/kg/post', [AdminController::class, 'payment_receipt_columbite_kg_post'])->name('admin.payment.receipt.columbite.kg.post');
    Route::get('/admin/payment/receipt/columbite/edit/{id}', [AdminController::class, 'payment_receipt_columbite_edit'])->name('admin.payment.receipt.columbite.edit');
    Route::post('/admin/payment/receipt/columbite/pound/update/{id}', [AdminController::class, 'payment_receipt_columbite_pound_update'])->name('admin.payment.receipt.columbite.pound.update');
    Route::post('/admin/payment/receipt/columbite/kg/update/{id}', [AdminController::class, 'payment_receipt_columbite_kg_update'])->name('admin.payment.receipt.columbite.kg.update');
    Route::post('/admin/payment/receipt/columbite/delete/{id}/{type}', [AdminController::class, 'payment_receipt_columbite_delete'])->name('admin.payment.receipt.columbite.delete');
    // Low Grade 
    Route::any('/admin/payment/receipt/lower/grade/columbite/view/{id}', [AdminController::class, 'payment_receipt_lower_grade_columbite_view'])->name('admin.payment.receipt.lower.grade.columbite.view');
    Route::get('/admin/payment/receipt/lower/grade/columbite/add/{id}', [AdminController::class, 'payment_receipt_lower_grade_columbite_add'])->name('admin.payment.receipt.lower.grade.columbite.add');
    Route::any('/admin/payment/receipt/lower/grade/columbite/pound/post', [AdminController::class, 'payment_receipt_lower_grade_columbite_pound_post'])->name('admin.payment.receipt.lower.grade.columbite.pound.post');
    Route::any('/admin/payment/receipt/lower/grade/columbite/kg/post', [AdminController::class, 'payment_receipt_lower_grade_columbite_kg_post'])->name('admin.payment.receipt.lower.grade.columbite.kg.post');
    Route::get('/admin/payment/receipt/lower/grade/columbite/edit/{id}', [AdminController::class, 'payment_receipt_lower_grade_columbite_edit'])->name('admin.payment.receipt.lower.grade.columbite.edit');
    Route::post('/admin/payment/receipt/lower/grade/columbite/pound/update/{id}', [AdminController::class, 'payment_receipt_lower_grade_columbite_pound_update'])->name('admin.payment.receipt.lower.grade.columbite.pound.update');
    Route::post('/admin/payment/receipt/lower/grade/columbite/kg/update/{id}', [AdminController::class, 'payment_receipt_lower_grade_columbite_kg_update'])->name('admin.payment.receipt.lower.grade.columbite.kg.update');
    Route::post('/admin/payment/receipt/lower/grade/columbite/delete/{id}/{type}', [AdminController::class, 'payment_receipt_lower_grade_columbite_delete'])->name('admin.payment.receipt.lower.grade.columbite.delete');

    // Weekly Material Summary
    Route::any('/admin/weekly/material/summary/tin/pound', [AdminController::class, 'weekly_material_summary_tin_pound'])->name('admin.weekly.material.summary.tin.pound');
    Route::any('/admin/weekly/material/summary/tin/kg', [AdminController::class, 'weekly_material_summary_tin_kg'])->name('admin.weekly.material.summary.tin.kg');
    Route::any('/admin/weekly/material/summary/columbite/pound', [AdminController::class, 'weekly_material_summary_columbite_pound'])->name('admin.weekly.material.summary.columbite.pound');
    Route::any('/admin/weekly/material/summary/columbite/kg', [AdminController::class, 'weekly_material_summary_columbite_kg'])->name('admin.weekly.material.summary.columbite.kg');
    Route::any('/admin/weekly/material/summary/low/grade/pound', [AdminController::class, 'weekly_material_summary_low_grade_pound'])->name('admin.weekly.material.summary.low.grade.pound');
    Route::any('/admin/weekly/material/summary/low/grade/kg', [AdminController::class, 'weekly_material_summary_low_grade_kg'])->name('admin.weekly.material.summary.low.grade.kg'); 

    Route::any('/admin/daily/balance', [AdminController::class, 'daily_balance'])->name('admin.daily.balance');
    Route::post('/admin/daily/balance/add', [AccountantController::class, 'add_daily_balance'])->name('admin.daily.balance.add');
    Route::post('/admin/daily/balance/update/{id}', [AdminController::class, 'update_daily_balance'])->name('admin.daily.balance.update');
    Route::post('/admin/daily/balance/delete/{id}', [AdminController::class, 'delete_daily_balance'])->name('admin.daily.balance.delete');
});
