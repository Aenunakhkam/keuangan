<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Salary;
use App\Models\Teacher;
use App\Models\CashTransaction;
use App\Http\Requests\StoreSalaryRequest;
use App\Http\Requests\UpdateSalaryRequest;

class SalaryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $salaries = Salary::with('teacher')->latest()->paginate(10)->through(function ($salary) {
            return [
                'id' => $salary->id,
                'teacher_id' => $salary->teacher_id,
                'base_salary' => $salary->base_salary,
                'allowance' => $salary->allowance,
                'transport_per_day' => $salary->transport_per_day,
                'days_present' => $salary->days_present,
                'transport_allowance' => $salary->transport_allowance,
                'deduction' => $salary->deduction,
                'deduction_description' => $salary->deduction_description,
                'net_salary' => $salary->net_salary,
                'month' => $salary->month,
                'year' => $salary->year,
                'status' => $salary->status,
                'paid_at' => $salary->paid_at,
                'payment_date' => $salary->payment_date,
                'teacher' => $salary->teacher ? [
                    'id' => $salary->teacher->id,
                    'name' => $salary->teacher->name,
                    'nip' => $salary->teacher->nip,
                ] : null,
            ];
        });

        $teachers = Teacher::with('positions')->get()->map(function ($teacher) {
            return [
                'id' => $teacher->id,
                'name' => $teacher->name,
                'nip' => $teacher->nip,
                'teaching_hours' => $teacher->teaching_hours,
                'positions' => $teacher->positions->map(function ($pos) {
                    return [
                        'id' => $pos->id,
                        'name' => $pos->name,
                        'allowance' => $pos->allowance,
                    ];
                })->toArray(),
            ];
        });
        // Fallback ke 0 jika setting belum diset
        $teachingRate = \App\Models\Setting::where('key', 'teaching_rate_per_hour')->value('value') ?? 0;
        $transportAllowance = \App\Models\Setting::where('key', 'transport_allowance')->value('value') ?? 0;

        return inertia('Salary/Index', [
            'salaries' => $salaries,
            'teachers' => $teachers,
            'teachingRate' => (int) $teachingRate,
            'transportAllowance' => (int) $transportAllowance
        ]);
    }

    public function store(StoreSalaryRequest $request)
    {
        $salary = Salary::create($request->validated());
        
        if ($salary->status === 'paid') {
            $this->logToCashJournal($salary);
        }

        return redirect()->back()->with('success', 'Data gaji berhasil disimpan.');
    }

    public function update(UpdateSalaryRequest $request, Salary $salary)
    {
        $salary->update($request->validated());

        return redirect()->back()->with('success', 'Data slip gaji berhasil diperbarui.');
    }

    public function generateBulk(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer',
        ]);

        $teachers = Teacher::with('positions')->get();
        $teachingRate = \App\Models\Setting::where('key', 'teaching_rate_per_hour')->value('value') ?? 0;
        $transportAllowance = \App\Models\Setting::where('key', 'transport_allowance')->value('value') ?? 0;

        foreach ($teachers as $teacher) {
            $baseSalary = $teacher->teaching_hours * $teachingRate;
            $allowance = $teacher->positions ? ($teacher->positions->sum('allowance') + $teacher->positions->sum('health_allowance')) : 0;
            // Saat generate massal, days_present = 0 (harus diisi manual per guru)
            $transport = 0; // transport_per_day * 0 hari = 0 (belum tahu hari hadir)
            $netSalary = $baseSalary + $allowance + $transport;

            Salary::create([
                'teacher_id'        => $teacher->id,
                'base_salary'       => $baseSalary,
                'allowance'         => $allowance,
                'transport_per_day' => $transportAllowance, // simpan tarif harian dari settings
                'days_present'      => 0,
                'transport_allowance' => 0,
                'deduction'         => 0,
                'net_salary'        => $netSalary,
                'month'             => $request->month,
                'year'              => $request->year,
                'status'            => 'pending'
            ]);
        }

        return redirect()->back()->with('success', 'Gaji massal berhasil di-generate.');
    }

    public function processPayment(Salary $salary)
    {
        $salary->update([
            'status' => 'paid',
            'paid_at' => now(),
            'payment_date' => now()->format('Y-m-d')
        ]);

        $this->logToCashJournal($salary);

        return redirect()->back()->with('success', 'Gaji berhasil dibayarkan.');
    }

    private function logToCashJournal(Salary $salary)
    {
        CashTransaction::create([
            'type' => 'expense',
            'category' => 'Gaji Guru',
            'amount' => $salary->net_salary,
            'description' => "Gaji {$salary->teacher->name} - Periode {$salary->month}/{$salary->year}",
            'transaction_date' => $salary->payment_date ?? now()
        ]);
    }

    public function destroy(Salary $salary)
    {
        $salary->delete();
        return redirect()->back()->with('success', 'Data gaji berhasil dihapus.');
    }

    public function print(Salary $salary)
    {
        $salary->load('teacher.positions');
        $settingLogo = \App\Models\Setting::where('key', 'school_logo')->value('value');
        $settingAddress = \App\Models\Setting::where('key', 'school_address')->value('value');
        $settingName = \App\Models\Setting::where('key', 'school_name')->value('value');
        $teachingRate = \App\Models\Setting::where('key', 'teaching_rate_per_hour')->value('value') ?? 0;

        return view('salary-slip', compact('salary', 'settingLogo', 'settingAddress', 'settingName', 'teachingRate'));
    }
}
