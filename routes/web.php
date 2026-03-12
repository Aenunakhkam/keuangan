<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\FeeTypeController;
use App\Http\Controllers\StudentBillController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\CashTransactionController;
use App\Http\Controllers\SalaryController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\ClassRoomController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Master Data
    Route::resource('positions', \App\Http\Controllers\PositionController::class);
    Route::resource('students', StudentController::class);
    Route::resource('teachers', TeacherController::class);
    Route::resource('fee-types', FeeTypeController::class);

    // Akademik
    Route::resource('academic-years', AcademicYearController::class);
    Route::resource('class-rooms', ClassRoomController::class);
    Route::resource('users', UserController::class);
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/transactions', [ReportController::class, 'transactionReport'])->name('reports.transactions');
    Route::get('reports/export-expense', [ReportController::class, 'exportExpense'])->name('reports.export-expense');
    Route::get('reports/export-income', [ReportController::class, 'exportIncome'])->name('reports.export-income');

    // Keuangan
    Route::post('student-bills/generate-bulk', [StudentBillController::class, 'generateBulk'])->name('student-bills.generate-bulk');
    Route::resource('student-bills', StudentBillController::class);
    Route::resource('payments', PaymentController::class);
    Route::resource('cash-transactions', CashTransactionController::class);

    // Payroll
    Route::post('salaries/generate-bulk', [SalaryController::class, 'generateBulk'])->name('salaries.generate-bulk');
    Route::get('salaries/{salary}/print', [SalaryController::class, 'print'])->name('salaries.print');
    Route::post('salaries/{salary}/process-payment', [SalaryController::class, 'processPayment'])->name('salaries.process-payment');
    Route::resource('salaries', SalaryController::class);

    // Pengaturan
    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingController::class, 'store'])->name('settings.store');
    Route::delete('settings/logo', [SettingController::class, 'destroyLogo'])->name('settings.logo.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
