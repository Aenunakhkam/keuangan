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
        $query = Salary::with(['teacher.positions', 'salaryDeduction']);

        // Filter Nama Pegawai
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('teacher', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nipty', 'like', "%{$search}%")
                  ->orWhere('nipy', 'like', "%{$search}%");
            });
        }

        // Filter Periode (Bulan & Tahun)
        if ($request->filled('month')) {
            $query->where('month', $request->input('month'));
        }
        if ($request->filled('year')) {
            $query->where('year', $request->input('year'));
        }

        $perPage = $request->input('per_page', 10);
        if ($perPage === 'all') {
            $perPage = 1000000;
        } else {
            $perPage = (int) $perPage;
            if (!in_array($perPage, [10, 15, 25, 50, 100])) {
                $perPage = 10;
            }
        }

        $salaries = $query->latest()->paginate($perPage)->withQueryString()->through(function ($salary) {
            return [
                'id' => $salary->id,
                'teacher_id' => $salary->teacher_id,
                'base_salary' => $salary->base_salary,
                'allowance' => $salary->allowance,
                'days_present' => $salary->days_present,
                'deduction' => $salary->deduction,
                'deduction_description' => $salary->deduction_description,
                'net_salary' => $salary->net_salary,
                'salary_deduction' => $salary->salaryDeduction,
                'final_net_salary' => $salary->salaryDeduction ? $salary->salaryDeduction->gaji_bersih : $salary->net_salary,
                'month' => $salary->month,
                'year' => $salary->year,
                'status' => $salary->status,
                'paid_at' => $salary->paid_at,
                'payment_date' => $salary->payment_date,
                'teacher' => $salary->teacher ? [
                    'id' => $salary->teacher->id,
                    'name' => $salary->teacher->name,
                    'nipy' => $salary->teacher->nipy,
                    'nipty' => $salary->teacher->nipty,
                    'positions' => $salary->teacher->positions ? $salary->teacher->positions->map(function ($pos) {
                        return [
                            'id' => $pos->id,
                            'name' => $pos->name,
                        ];
                    })->toArray() : [],
                ] : null,
            ];
        });

        $teachers = Teacher::with('positions')->get()->map(function ($teacher) {
            $baseSalary = (float) $teacher->basic_salary;
            if ($baseSalary <= 0) {
                $baseSalary = (float) \App\Models\SalaryScale::getAmount($teacher->education, $teacher->service_years);
            }
            
            $structuralAllowance = $teacher->positions ? $teacher->positions->sum('allowance') : 0;
            $allowance = $structuralAllowance + ($teacher->other_allowance ?? 0);

            return [
                'id' => $teacher->id,
                'name' => $teacher->name,
                'nipy' => $teacher->nipy,
                'nipty' => $teacher->nipty,
                'basic_salary' => $baseSalary,
                'allowance' => $allowance,
            ];
        });
        // Fallback ke 25000 jika setting belum diset
        $teachingRate = \App\Models\Setting::where('key', 'teaching_rate_per_hour')->value('value') ?? 25000;
        $schoolLogo = \App\Models\Setting::where('key', 'logo_url')->value('value')
            ?? \App\Models\Setting::where('key', 'school_logo')->value('value');
        $schoolName = \App\Models\Setting::where('key', 'school_name')->value('value') ?? 'SMK NU 1 ISLAMIYAH KRAMAT';
        $schoolAddress = \App\Models\Setting::where('key', 'school_address')->value('value') ?? 'Jl. Garuda No. 39 - Kemantran';

        return inertia('Salary/Index', [
            'salaries' => $salaries,
            'teachers' => $teachers,
            'teachingRate' => (int) $teachingRate,
            'schoolLogo' => $schoolLogo,
            'schoolName' => $schoolName,
            'schoolAddress' => $schoolAddress,
            'filters' => $request->only(['search', 'month', 'year', 'per_page'])
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

        foreach ($teachers as $teacher) {
            // Hindari generate ganda jika sudah ada data gaji untuk guru ini di bulan/tahun yang sama
            $exists = Salary::where('teacher_id', $teacher->id)
                ->where('month', $request->month)
                ->where('year', $request->year)
                ->exists();
                
            if ($exists) {
                continue;
            }

            // Gaji pokok dasar diambil dari record teacher, fallback ke matriks skala gaji jika kosong/0
            $baseSalary = $teacher->basic_salary;
            if (is_null($baseSalary) || $baseSalary <= 0) {
                $baseSalary = \App\Models\SalaryScale::getAmount($teacher->education, $teacher->service_years);
            }
            
            // Tunjangan Jabatan (Allowance) + Tunjangan Tetap Lainnya
            $structuralAllowance = $teacher->positions ? $teacher->positions->sum('allowance') : 0;
            $allowance = $structuralAllowance + ($teacher->other_allowance ?? 0);
            
            $netSalary = $baseSalary + $allowance;

            Salary::create([
                'teacher_id'        => $teacher->id,
                'base_salary'       => $baseSalary,
                'allowance'         => $allowance,
                'days_present'      => 0,
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

    public function bulkPay(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:salaries,id',
        ]);

        $salaries = Salary::whereIn('id', $request->ids)
            ->where('status', 'pending')
            ->get();

        if ($salaries->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data gaji berstatus pending yang dipilih.');
        }

        foreach ($salaries as $salary) {
            $salary->update([
                'status' => 'paid',
                'paid_at' => now(),
                'payment_date' => now()->format('Y-m-d')
            ]);

            $this->logToCashJournal($salary);
        }

        return redirect()->back()->with('success', count($salaries) . ' gaji berhasil dibayarkan secara massal.');
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:salaries,id',
        ]);

        $count = Salary::whereIn('id', $request->ids)->delete();

        return redirect()->back()->with('success', $count . ' data gaji berhasil dihapus secara massal.');
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
        $salary->load(['teacher.positions', 'salaryDeduction']);
        $settingLogo = \App\Models\Setting::where('key', 'logo_url')->value('value') 
            ?? \App\Models\Setting::where('key', 'school_logo')->value('value');
        $settingAddress = \App\Models\Setting::where('key', 'school_address')->value('value');
        $settingName = \App\Models\Setting::where('key', 'school_name')->value('value');
        $teachingRate = \App\Models\Setting::where('key', 'teaching_rate_per_hour')->value('value') ?? 25000;
        $settingFooter = \App\Models\Setting::where('key', 'copyright')->value('value') ?? 'Aplikasi Keuangan Sekolah (SIAKAD Keuangan)';

        return view('salary-slip', compact('salary', 'settingLogo', 'settingAddress', 'settingName', 'teachingRate', 'settingFooter'));
    }

    public function printBulk(Request $request)
    {
        $query = Salary::with(['teacher.positions', 'salaryDeduction']);

        // Filter Nama Pegawai
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('teacher', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nipty', 'like', "%{$search}%")
                  ->orWhere('nipy', 'like', "%{$search}%");
            });
        }

        // Filter Periode (Bulan & Tahun)
        if ($request->filled('month')) {
            $query->where('month', $request->input('month'));
        }
        if ($request->filled('year')) {
            $query->where('year', $request->input('year'));
        }

        $salaries = $query->latest()->get();

        if ($salaries->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data gaji untuk dicetak.');
        }

        $settingLogo = \App\Models\Setting::where('key', 'logo_url')->value('value') 
            ?? \App\Models\Setting::where('key', 'school_logo')->value('value');
        $settingAddress = \App\Models\Setting::where('key', 'school_address')->value('value');
        $settingName = \App\Models\Setting::where('key', 'school_name')->value('value');
        $teachingRate = \App\Models\Setting::where('key', 'teaching_rate_per_hour')->value('value') ?? 25000;
        $settingFooter = \App\Models\Setting::where('key', 'copyright')->value('value') ?? 'Aplikasi Keuangan Sekolah (SIAKAD Keuangan)';

        return view('salary-slip-bulk', compact('salaries', 'settingLogo', 'settingAddress', 'settingName', 'teachingRate', 'settingFooter'));
    }
}
