<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Salary;

class SalaryApprovalController extends Controller
{
    public function index(Request $request)
    {
        $query = Salary::with(['teacher.positions', 'salaryDeduction'])
            ->where('approval_status', '!=', 'pending');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('teacher', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nipty', 'like', "%{$search}%")
                  ->orWhere('nipy', 'like', "%{$search}%");
            });
        }

        if ($request->filled('month')) {
            $query->where('month', $request->input('month'));
        }
        if ($request->filled('year')) {
            $query->where('year', $request->input('year'));
        }
        
        if ($request->filled('status')) {
            $query->where('approval_status', $request->input('status'));
        }

        $perPage = $request->input('per_page', 10);
        if ($perPage === 'all') {
            $perPage = 100000;
        }

        $salaries = $query->latest()->paginate($perPage)->withQueryString()->through(function ($salary) {
            return [
                'id' => $salary->id,
                'teacher_id' => $salary->teacher_id,
                'base_salary' => $salary->base_salary,
                'allowance' => $salary->allowance,
                'net_salary' => $salary->net_salary,
                'final_net_salary' => $salary->salaryDeduction ? $salary->salaryDeduction->gaji_bersih : $salary->net_salary,
                'month' => $salary->month,
                'year' => $salary->year,
                'status' => $salary->status,
                'approval_status' => $salary->approval_status,
                'rejection_note' => $salary->rejection_note,
                'teacher' => $salary->teacher ? [
                    'name' => $salary->teacher->name,
                    'nipy' => $salary->teacher->nipy,
                    'nipty' => $salary->teacher->nipty,
                ] : null,
            ];
        });
        $totalSubmittedAmount = (clone $query)->where('approval_status', 'submitted')->get()->sum(function($s) {
            return $s->salaryDeduction ? $s->salaryDeduction->gaji_bersih : $s->net_salary;
        });

        return inertia('SalaryApproval/Index', [
            'salaries' => $salaries,
            'totalSubmittedAmount' => $totalSubmittedAmount,
            'filters' => $request->only(['search', 'month', 'year', 'status', 'per_page'])
        ]);
    }

    public function approve(Salary $salary)
    {
        if ($salary->approval_status !== 'submitted') {
            return redirect()->back()->with('error', 'Hanya gaji dengan status Diajukan yang dapat disetujui.');
        }

        $salary->update([
            'approval_status' => 'approved',
            'rejection_note' => null
        ]);

        return redirect()->back()->with('success', 'Gaji berhasil disetujui.');
    }

    public function reject(Request $request, Salary $salary)
    {
        $request->validate([
            'rejection_note' => 'required|string|max:1000'
        ]);

        if ($salary->approval_status !== 'submitted') {
            return redirect()->back()->with('error', 'Hanya gaji dengan status Diajukan yang dapat ditolak.');
        }

        $salary->update([
            'approval_status' => 'rejected',
            'rejection_note' => $request->rejection_note
        ]);

        return redirect()->back()->with('success', 'Gaji berhasil ditolak.');
    }

    public function bulkApprove(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:salaries,id'
        ]);

        Salary::whereIn('id', $request->ids)
            ->where('approval_status', 'submitted')
            ->update([
                'approval_status' => 'approved',
                'rejection_note' => null
            ]);

        return redirect()->back()->with('success', 'Gaji massal berhasil disetujui.');
    }
}
