<?php

namespace App\Http\Controllers;

use App\Models\CashTransaction;
use App\Models\Payment;
use App\Models\Salary;
use App\Models\Student;
use App\Models\StudentBill;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first() ?? 'user';
        $data = [
            'role' => $role,
            'stats' => [], // Ensure stats is always present
        ];

        switch ($role) {
            case 'admin':
                $data['stats'] = [
                    'students_count' => Student::count(),
                    'teachers_count' => Teacher::count(),
                    'total_income' => CashTransaction::where('type', 'income')->sum('amount'),
                    'total_expense' => CashTransaction::where('type', 'expense')->sum('amount'),
                    'today_paid_count' => Payment::whereDate('payment_date', now())->count(),
                    'pending_bills_count' => StudentBill::where('status', '!=', 'paid')->count(),
                ];

                $driver = \DB::getDriverName();
                $monthFormat = $driver === 'sqlite' ? "strftime('%m', transaction_date)" : "MONTH(transaction_date)";

                $incomeData = CashTransaction::where('type', 'income')
                    ->where('transaction_date', '>=', now()->subMonths(6))
                    ->selectRaw("$monthFormat as month, SUM(amount) as total")
                    ->groupBy('month')
                    ->orderBy('month')
                    ->pluck('total')
                    ->toArray();

                $expenseData = CashTransaction::where('type', 'expense')
                    ->where('transaction_date', '>=', now()->subMonths(6))
                    ->selectRaw("$monthFormat as month, SUM(amount) as total")
                    ->groupBy('month')
                    ->orderBy('month')
                    ->pluck('total')
                    ->toArray();

                $incomeData = array_pad(array_slice($incomeData, -6), -6, 0);
                $expenseData = array_pad(array_slice($expenseData, -6), -6, 0);

                $data['stats']['chart_data'] = [
                    'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'],
                    'income' => array_values($incomeData),
                    'expense' => array_values($expenseData),
                ];
                break;
            case 'bendahara':
                $data['stats'] = [
                    'recent_payments' => Payment::with('studentBill.student')->latest()->limit(5)->get()->map(function ($p) {
                        return [
                            'id' => $p->id,
                            'amount' => $p->amount,
                            'payment_date' => $p->payment_date,
                            'student_bill' => $p->studentBill ? [
                                'student' => $p->studentBill->student ? ['name' => $p->studentBill->student->name] : null,
                            ] : null,
                        ];
                    }),
                    'pending_bills_count' => StudentBill::where('status', '!=', 'paid')->count(),
                    'cash_balance' => CashTransaction::where('type', 'income')->sum('amount') - CashTransaction::where('type', 'expense')->sum('amount'),
                ];
                break;
            case 'guru':
                $teacher = Teacher::with('positions')->where('user_id', $user->id)->first();
                $salaries = collect();
                $totalSalaryThisYear = 0;
                $lastSalary = null;

                if ($teacher && isset($teacher->id)) {
                    $salariesResult = Salary::where('teacher_id', $teacher->id)->latest()->get();
                    $totalSalaryThisYear = Salary::where('teacher_id', $teacher->id)
                        ->where('year', date('Y'))
                        ->where('status', 'paid')
                        ->sum('net_salary');
                    
                    $lastSalaryRecord = $salariesResult->first();
                    $lastSalary = $lastSalaryRecord ? $lastSalaryRecord->net_salary : 0;

                    $salaries = $salariesResult->map(function ($s) {
                        return [
                            'id' => $s->id,
                            'month' => $s->month,
                            'year' => $s->year,
                            'base_salary' => $s->base_salary,
                            'allowance' => $s->allowance,
                            'transport_allowance' => $s->transport_allowance,
                            'deduction' => $s->deduction,
                            'deduction_description' => $s->deduction_description,
                            'net_salary' => $s->net_salary,
                            'status' => $s->status,
                        ];
                    });
                }

                $data['stats'] = [
                    'teacher' => $teacher ? [
                        'name' => $teacher->name,
                        'nip' => $teacher->nip,
                        'teaching_hours' => $teacher->teaching_hours,
                        'positions' => $teacher->positions ? $teacher->positions->map(function ($pos) {
                            return ['name' => $pos->name, 'allowance' => $pos->allowance];
                        }) : collect(),
                    ] : null,
                    'financials' => [
                        'total_year' => $totalSalaryThisYear,
                        'last_salary' => $lastSalary,
                    ],
                    'salaries' => $salaries,
                ];
                break;
            case 'siswa':
                $student = Student::with('classRoom')->where('user_id', $user->id)->first();
                $bankSettings = \App\Models\Setting::whereIn('key', ['bank_name', 'bank_account_number', 'bank_account_name'])
                    ->get()->pluck('value', 'key');
                
                $bills = $student ? StudentBill::with('feeType')->where('student_id', $student->id)->get() : collect();
                
                $data['stats'] = [
                    'student' => $student ? [
                        'name' => $student->name,
                        'nis' => $student->nis,
                        'class_name' => $student->classRoom ? $student->classRoom->name : '-',
                    ] : null,
                    'summary' => [
                        'total_bills' => $bills->sum('amount'),
                        'total_paid' => $bills->where('status', 'paid')->sum('amount'),
                        'total_unpaid' => $bills->where('status', '!=', 'paid')->sum('amount'),
                    ],
                    'bills' => $bills->map(function ($b) {
                        return [
                            'id' => $b->id,
                            'amount' => $b->amount,
                            'due_date' => $b->due_date,
                            'status' => $b->status,
                            'fee_type' => $b->feeType ? ['name' => $b->feeType->name] : null,
                        ];
                    }),
                    'bank_name'           => $bankSettings['bank_name'] ?? null,
                    'bank_account_number' => $bankSettings['bank_account_number'] ?? null,
                    'bank_account_name'   => $bankSettings['bank_account_name'] ?? null,
                ];
                break;
        }

        return Inertia::render('Dashboard', $data);
    }
}
