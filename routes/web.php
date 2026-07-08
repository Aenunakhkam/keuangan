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

use App\Http\Controllers\SalaryScaleController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Master Data
    Route::resource('positions', \App\Http\Controllers\PositionController::class);
    Route::get('teachers/template', [TeacherController::class, 'downloadTemplate'])->name('teachers.template');
    Route::post('/teachers/import', [TeacherController::class, 'import'])->name('teachers.import');
    Route::post('/teachers/{teacher}/reset-password', [TeacherController::class, 'resetPassword'])->name('teachers.reset-password');
    Route::resource('teachers', TeacherController::class);
    
    // Potongan Gaji Pegawai
    Route::get('salary-deductions', [\App\Http\Controllers\SalaryDeductionController::class, 'index'])->name('salary-deductions.index');
    Route::get('salary-deductions/export-pdf', [\App\Http\Controllers\SalaryDeductionController::class, 'exportPdf'])->name('salary-deductions.export-pdf');
    Route::get('salary-deductions/export-excel', [\App\Http\Controllers\SalaryDeductionController::class, 'exportExcel'])->name('salary-deductions.export-excel');
    Route::post('salary-deductions/bulk', [\App\Http\Controllers\SalaryDeductionController::class, 'storeBulk'])->name('salary-deductions.storeBulk');

    Route::resource('salary-scales', SalaryScaleController::class);
    Route::get('salary-scales/calculate', [SalaryScaleController::class, 'getSalary'])->name('salary-scales.calculate');
    Route::get('salary-calculations', [\App\Http\Controllers\Master\SalaryCalculationController::class, 'index'])->name('salary-calculations.index');
    Route::put('salary-calculations/{teacher}', [\App\Http\Controllers\Master\SalaryCalculationController::class, 'update'])->name('salary-calculations.update');

    Route::get('bpjs', [\App\Http\Controllers\Master\BpjsController::class, 'index'])->name('bpjs.index');
    Route::put('bpjs/config', [\App\Http\Controllers\Master\BpjsController::class, 'updateConfig'])->name('bpjs.config.update');
    Route::put('bpjs/teachers/{teacher}', [\App\Http\Controllers\Master\BpjsController::class, 'updateTeacherCategory'])->name('bpjs.teacher.update');

    Route::resource('users', UserController::class);
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/transactions', [ReportController::class, 'transactionReport'])->name('reports.transactions');
    Route::get('reports/salary-spj', [\App\Http\Controllers\Report\SalarySpjController::class, 'index'])->name('reports.salary-spj');
    Route::get('reports/salary-spj/print', [\App\Http\Controllers\Report\SalarySpjController::class, 'print'])->name('reports.salary-spj.print');
    Route::get('reports/salary-spj/excel', [\App\Http\Controllers\Report\SalarySpjController::class, 'excel'])->name('reports.salary-spj.excel');
    Route::get('reports/export-expense', [ReportController::class, 'exportExpense'])->name('reports.export-expense');
    Route::get('reports/export-income', [ReportController::class, 'exportIncome'])->name('reports.export-income');

    // Keuangan
    Route::resource('cash-transactions', CashTransactionController::class);

    // Payroll
    Route::post('salaries/generate-bulk', [SalaryController::class, 'generateBulk'])->name('salaries.generate-bulk');
    Route::get('salaries/print-bulk', [SalaryController::class, 'printBulk'])->name('salaries.print-bulk');
    Route::get('salaries/{salary}/print', [SalaryController::class, 'print'])->name('salaries.print');
    Route::post('salaries/{salary}/process-payment', [SalaryController::class, 'processPayment'])->name('salaries.process-payment');
    Route::post('salaries/bulk-pay', [SalaryController::class, 'bulkPay'])->name('salaries.bulk-pay');
    Route::post('salaries/bulk-delete', [SalaryController::class, 'bulkDelete'])->name('salaries.bulk-delete');
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
