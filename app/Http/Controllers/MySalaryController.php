<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Salary;
use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;

class MySalaryController extends Controller
{
    public function index(Request $request)
    {
        $teacher = Teacher::where('user_id', Auth::id())->first();

        if (!$teacher) {
            return inertia('MySalary/Index', [
                'salaries' => null,
                'error' => 'Data guru/pegawai tidak ditemukan untuk akun ini.'
            ]);
        }

        $query = Salary::with(['salaryDeduction'])
            ->where('teacher_id', $teacher->id)
            ->where('is_published', true);

        if ($request->filled('month')) {
            $query->where('month', $request->input('month'));
        }
        if ($request->filled('year')) {
            $query->where('year', $request->input('year'));
        }

        $salaries = $query->latest()->paginate(12)->withQueryString()->through(function ($salary) {
            return [
                'id' => $salary->id,
                'base_salary' => $salary->base_salary,
                'allowance' => $salary->allowance,
                'net_salary' => $salary->net_salary,
                'final_net_salary' => $salary->salaryDeduction ? $salary->salaryDeduction->gaji_bersih : $salary->net_salary,
                'month' => $salary->month,
                'year' => $salary->year,
                'status' => $salary->status,
                'payment_date' => $salary->payment_date,
            ];
        });

        return inertia('MySalary/Index', [
            'salaries' => $salaries,
            'filters' => $request->only(['month', 'year'])
        ]);
    }
}
