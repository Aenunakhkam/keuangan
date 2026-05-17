<?php

namespace App\Http\Controllers;

use App\Models\CashTransaction;
use App\Models\Salary;
use App\Models\Teacher;
use App\Models\Position;
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
                    'teachers_count' => Teacher::count(),
                    'positions_count' => Position::count(),
                    'total_income' => CashTransaction::where('type', 'income')->sum('amount'),
                    'total_expense' => CashTransaction::where('type', 'expense')->sum('amount'),
                    'payroll_this_month' => Salary::where('month', date('n'))->where('year', date('Y'))->sum('net_salary'),
                    'pending_payroll_count' => Salary::where('month', date('n'))->where('year', date('Y'))->where('status', 'pending')->count(),
                ];

                // Generate the last 6 months as dynamic labels and a template map
                $chartLabels = [];
                $incomeMap = [];
                $expenseMap = [];

                for ($i = 5; $i >= 0; $i--) {
                    $date = now()->subMonths($i);
                    $monthNum = (int)$date->format('n');
                    $monthName = $date->translatedFormat('M');
                    $year = $date->format('Y');

                    $key = $year . '-' . str_pad((string)$monthNum, 2, '0', STR_PAD_LEFT);
                    $chartLabels[] = $monthName;
                    $incomeMap[$key] = 0;
                    $expenseMap[$key] = 0;
                }

                $driver = \DB::getDriverName();
                if ($driver === 'sqlite') {
                    $monthYearFormat = "strftime('%Y-%m', transaction_date)";
                } else {
                    $monthYearFormat = "DATE_FORMAT(transaction_date, '%Y-%m')";
                }

                $incomeTransactions = CashTransaction::where('type', 'income')
                    ->where('transaction_date', '>=', now()->subMonths(5)->startOfMonth())
                    ->selectRaw("$monthYearFormat as month_year, SUM(amount) as total")
                    ->groupBy('month_year')
                    ->get();

                foreach ($incomeTransactions as $tx) {
                    if (isset($incomeMap[$tx->month_year])) {
                        $incomeMap[$tx->month_year] = (float)$tx->total;
                    }
                }

                $expenseTransactions = CashTransaction::where('type', 'expense')
                    ->where('transaction_date', '>=', now()->subMonths(5)->startOfMonth())
                    ->selectRaw("$monthYearFormat as month_year, SUM(amount) as total")
                    ->groupBy('month_year')
                    ->get();

                foreach ($expenseTransactions as $tx) {
                    if (isset($expenseMap[$tx->month_year])) {
                        $expenseMap[$tx->month_year] = (float)$tx->total;
                    }
                }

                $data['stats']['chart_data'] = [
                    'labels' => $chartLabels,
                    'income' => array_values($incomeMap),
                    'expense' => array_values($expenseMap),
                ];
                break;
            case 'bendahara':
                $data['stats'] = [
                    'recent_salaries' => Salary::with('teacher')->latest()->limit(5)->get()->map(function ($s) {
                        return [
                            'id' => $s->id,
                            'amount' => $s->net_salary,
                            'month' => $s->month,
                            'year' => $s->year,
                            'teacher' => $s->teacher ? ['name' => $s->teacher->name] : null,
                        ];
                    }),
                    'pending_payroll_count' => Salary::where('status', 'pending')->count(),
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
                            'net_salary' => $s->net_salary,
                            'status' => $s->status,
                        ];
                    });
                }

                $data['stats'] = [
                    'teacher' => $teacher ? [
                        'name' => $teacher->name,
                        'nipty' => $teacher->nipty,
                        'nipy' => $teacher->nipy,
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
        }

        return Inertia::render('Dashboard', $data);
    }
}
