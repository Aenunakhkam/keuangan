<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashTransaction;
use App\Models\Salary;
use App\Models\Teacher;
use Inertia\Inertia;

class ReportController extends Controller
{
    public function index()
    {
        $driver = \DB::getDriverName();
        $monthFormat = $driver === 'sqlite' ? "strftime('%m', transaction_date)" : "MONTH(transaction_date)";

        // Monthly Income
        $incomeByMonth = CashTransaction::where('type', 'income')
            ->selectRaw("$monthFormat as month, SUM(amount) as total")
            ->groupBy('month')
            ->get();

        // Monthly Expense
        $expenseByMonth = CashTransaction::where('type', 'expense')
            ->selectRaw("$monthFormat as month, SUM(amount) as total")
            ->groupBy('month')
            ->get();

        // Expense by Category (Current Year)
        $expenseByCategory = CashTransaction::where('type', 'expense')
            ->whereYear('transaction_date', date('Y'))
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();

        // Recent Salary Distributions
        $recentSalaries = Salary::with('teacher')
            ->latest()
            ->limit(10)
            ->get()->map(function ($sal) {
                return [
                    'id' => $sal->id,
                    'amount' => $sal->net_salary,
                    'status' => $sal->status,
                    'period' => $sal->month.'/'.$sal->year,
                    'teacher' => $sal->teacher ? ['name' => $sal->teacher->name] : null,
                ];
            });

        // Income by Category (Current Year)
        $incomeByCategory = CashTransaction::where('type', 'income')
            ->whereYear('transaction_date', date('Y'))
            ->selectRaw('category, SUM(amount) as total')
            ->groupBy('category')
            ->get();

        // Payroll Status Summary
        $payrollStatusSummary = [
            'paid' => Salary::where('status', 'paid')->count(),
            'pending' => Salary::where('status', 'pending')->count(),
        ];

        return inertia('Report/Index', [
            'incomeByMonth' => $incomeByMonth,
            'expenseByMonth' => $expenseByMonth,
            'expenseByCategory' => $expenseByCategory,
            'incomeByCategory' => $incomeByCategory,
            'recentSalaries' => $recentSalaries,
            'payrollStatusSummary' => $payrollStatusSummary,
        ]);
    }

    public function exportExpense(Request $request)
    {
        $year = $request->year ?? date('Y');
        $month = $request->month;

        $query = CashTransaction::where('type', 'expense')
            ->whereYear('transaction_date', $year);

        if ($month) {
            $query->whereMonth('transaction_date', $month);
        }

        $expenses = $query->get()->groupBy('category');
        $totalExpense = $query->sum('amount');
        
        $settingLogo = \App\Models\Setting::where('key', 'school_logo')->value('value');
        $settingAddress = \App\Models\Setting::where('key', 'school_address')->value('value');
        $settingName = \App\Models\Setting::where('key', 'school_name')->value('value');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.expense-report', compact('expenses', 'totalExpense', 'year', 'month', 'settingLogo', 'settingAddress', 'settingName'));
        // Menggunakan ukuran F4 landscape (215mm x 330mm) -> approx 8.5 x 13 in
        $pdf->setPaper([0, 0, 935.433, 609.448], 'landscape');
        
        return $pdf->download('laporan-pengeluaran-' . $year . ($month ? '-' . $month : '') . '.pdf');
    }

    public function exportIncome(Request $request)
    {
        $year = $request->year ?? date('Y');
        $month = $request->month;

        $query = CashTransaction::where('type', 'income')
            ->whereYear('transaction_date', $year);

        if ($month) {
            $query->whereMonth('transaction_date', $month);
        }

        $income = $query->get()->groupBy('category');
        $totalIncome = $query->sum('amount');
        
        $settingLogo = \App\Models\Setting::where('key', 'school_logo')->value('value');
        $settingAddress = \App\Models\Setting::where('key', 'school_address')->value('value');
        $settingName = \App\Models\Setting::where('key', 'school_name')->value('value');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('reports.income-report', compact('income', 'totalIncome', 'year', 'month', 'settingLogo', 'settingAddress', 'settingName'));
        $pdf->setPaper([0, 0, 935.433, 609.448], 'landscape');
        
        return $pdf->download('laporan-pemasukan-' . $year . ($month ? '-' . $month : '') . '.pdf');
    }

    public function transactionReport(Request $request)
    {
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-t');
        $type = $request->type;
        $category = $request->category;

        $query = CashTransaction::query()
            ->whereBetween('transaction_date', [$startDate, $endDate]);

        if ($type && in_array($type, ['income', 'expense'])) {
            $query->where('type', $type);
        }

        if ($category && $category !== 'all') {
            $query->where('category', $category);
        }

        $transactions = $query->orderBy('transaction_date', 'asc')->get();

        $summary = [
            'total_income' => CashTransaction::whereBetween('transaction_date', [$startDate, $endDate])
                ->when($category && $category !== 'all', fn($q) => $q->where('category', $category))
                ->where('type', 'income')->sum('amount'),
            'total_expense' => CashTransaction::whereBetween('transaction_date', [$startDate, $endDate])
                ->when($category && $category !== 'all', fn($q) => $q->where('category', $category))
                ->where('type', 'expense')->sum('amount'),
        ];
        $summary['balance'] = $summary['total_income'] - $summary['total_expense'];

        $categories = CashTransaction::distinct()->pluck('category')->filter()->values();

        return inertia('Report/Transactions', [
            'transactions' => $transactions,
            'summary' => $summary,
            'categories' => $categories,
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
                'type' => $type ?? 'all',
                'category' => $category ?? 'all',
            ]
        ]);
    }
}
