<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Payment;
use App\Models\Salary;
use App\Models\CashTransaction;

echo "Starting sync...\n";

// Sync Payments (Income)
$payments = Payment::with('studentBill.feeType', 'studentBill.student')->get();
foreach ($payments as $payment) {
    if (!CashTransaction::where('amount', $payment->amount)->where('transaction_date', $payment->payment_date)->where('type', 'income')->exists()) {
        CashTransaction::create([
            'type' => 'income',
            'category' => 'Pembayaran Siswa',
            'amount' => $payment->amount,
            'description' => "Pembayaran " . ($payment->studentBill->feeType->name ?? 'Tagihan') . " - " . ($payment->studentBill->student->name ?? 'Siswa'),
            'transaction_date' => $payment->payment_date
        ]);
        echo "Synced Payment ID: {$payment->id}\n";
    }
}

// Sync Salaries (Expense)
$salaries = Salary::with('teacher')->where('status', 'paid')->get();
foreach ($salaries as $salary) {
    if (!CashTransaction::where('amount', $salary->net_salary)->where('transaction_date', $salary->payment_date)->where('type', 'expense')->exists()) {
        CashTransaction::create([
            'type' => 'expense',
            'category' => 'Gaji Guru',
            'amount' => $salary->net_salary,
            'description' => "Gaji " . ($salary->teacher->name ?? 'Guru') . " - Periode {$salary->month}/{$salary->year}",
            'transaction_date' => $salary->payment_date ?? now()
        ]);
        echo "Synced Salary ID: {$salary->id}\n";
    }
}

echo "Sync completed.\n";
