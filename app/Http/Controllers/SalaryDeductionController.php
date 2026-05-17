<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Salary;
use App\Models\Teacher;
use App\Models\BpjsConfig;
use App\Models\SalaryDeduction;

class SalaryDeductionController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));

        $bpjsConfig = BpjsConfig::first();

        // Ambil data gaji yang sudah di-generate untuk bulan tersebut
        // Sertakan relasi teacher dan salaryDeduction (jika sudah ada)
        $salaries = Salary::with(['teacher.positions', 'teacher.bpjsCategory'])
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        $deductions = [];

        foreach ($salaries as $salary) {
            $teacher = $salary->teacher;
            if (!$teacher) continue;

            // Hitung spj_netto secara on-the-fly untuk rujukan
            $bpjsAllowance = 0;
            $bpjsHealth = 0;
            $bpjsNaker = 0;

            if ($bpjsConfig && $teacher->bpjsCategory) {
                $category = $teacher->bpjsCategory;
                $umk = (float) $bpjsConfig->umk_reference;
                
                $healthSchool = (float) $bpjsConfig->health_school_percent;
                $healthEmp = (float) $bpjsConfig->health_employee_percent;
                $nakerSchool = (float) $bpjsConfig->naker_school_percent;
                $nakerEmp = (float) $bpjsConfig->naker_employee_percent;

                $healthTotalPercent = $healthSchool + $healthEmp;
                $nakerTotalPercent = $nakerSchool + $nakerEmp;

                $allowancePercent = 0;
                if ($category->code === 'A') {
                    $allowancePercent = $healthSchool + $nakerSchool;
                } elseif ($category->code === 'B') {
                    $allowancePercent = $healthTotalPercent + $nakerTotalPercent;
                } elseif ($category->code === 'C') {
                    $allowancePercent = $healthTotalPercent;
                } elseif ($category->code === 'D') {
                    $allowancePercent = $nakerTotalPercent;
                }

                $bpjsAllowance = round($umk * $allowancePercent / 100);
                $bpjsHealth = $category->has_health ? round($umk * $healthTotalPercent / 100) : 0;
                $bpjsNaker = $category->has_naker ? round($umk * $nakerTotalPercent / 100) : 0;
            }

            $jumlahBruto = $salary->base_salary + $salary->allowance + $bpjsAllowance;
            $spjNetto = $jumlahBruto - ($bpjsHealth + $bpjsNaker);

            // Cek apakah sudah ada di tabel salary_deductions
            $existingDeduction = SalaryDeduction::where('salary_id', $salary->id)->first();

            if ($existingDeduction) {
                // Update spj_netto jika ada perubahan pada UMK/Gaji, tapi tetap pertahankan isian manual
                // Untuk amannya, kita tidak otomatis update DB di sini, hanya tampilkan di UI
                // Jika ingin update otomatis, bisa ditaruh di sini
                $existingDeduction->spj_netto = $spjNetto;
                $existingDeduction->save(); // Memicu re-kalkulasi boot()
                $deductionData = $existingDeduction->fresh()->toArray();
            } else {
                // Buat dummy/inisialisasi untuk frontend
                $tabKhusus = round($spjNetto * 0.08);
                $simpananWajib = 5000;
                $dansos = 20000;
                
                $jumlahKoperasi = $tabKhusus + $simpananWajib;
                $jumlahPotongan = $jumlahKoperasi + $dansos;
                $gajiBersih = $spjNetto - $jumlahPotongan;

                $deductionData = [
                    'id' => null,
                    'salary_id' => $salary->id,
                    'teacher_id' => $teacher->id,
                    'spj_netto' => $spjNetto,
                    'tab_khusus' => $tabKhusus,
                    'simpanan_wajib' => $simpananWajib,
                    'simpanan_sukarela' => 0,
                    'angsuran_koperasi' => 0,
                    'jumlah_koperasi' => $jumlahKoperasi,
                    'dplk_slawi' => 0,
                    'dplk_kemantran' => 0,
                    'pinjaman_bpd_jateng' => 0,
                    'jumlah_bpd' => 0,
                    'bank_tgr' => 0,
                    'premi_bpjs_anggota' => 0,
                    'dansos' => $dansos,
                    'lainnya_1' => 0,
                    'lainnya_2' => 0,
                    'denda_fingerprint' => 0,
                    'jumlah_potongan' => $jumlahPotongan,
                    'gaji_bersih' => $gajiBersih,
                ];
            }

            // Tambahkan metadata untuk UI
            $deductionData['teacher_name'] = $teacher->name;
            $deductionData['teacher_nipy'] = $teacher->nipy;
            $deductionData['teacher_position'] = $teacher->positions->pluck('name')->join(', ') ?: '-';
            
            $deductions[] = $deductionData;
        }

        return Inertia::render('SalaryDeduction/Index', [
            'deductions' => $deductions,
            'filters' => [
                'month' => $month,
                'year' => $year,
            ]
        ]);
    }

    public function storeBulk(Request $request)
    {
        $request->validate([
            'deductions' => 'required|array',
            'deductions.*.salary_id' => 'required|exists:salaries,id',
            'deductions.*.teacher_id' => 'required|exists:teachers,id',
            // Kita izinkan field lainnya numerik
        ]);
        
        \Log::info('StoreBulk Payload:', $request->deductions);

        foreach ($request->deductions as $data) {
            $deduction = SalaryDeduction::updateOrCreate(
                ['salary_id' => $data['salary_id']],
                [
                    'teacher_id' => $data['teacher_id'],
                    'spj_netto' => $data['spj_netto'] ?? 0,
                    'simpanan_wajib' => $data['simpanan_wajib'] ?? 0,
                    'simpanan_sukarela' => $data['simpanan_sukarela'] ?? 0,
                    'angsuran_koperasi' => $data['angsuran_koperasi'] ?? 0,
                    'dplk_slawi' => $data['dplk_slawi'] ?? 0,
                    'dplk_kemantran' => $data['dplk_kemantran'] ?? 0,
                    'pinjaman_bpd_jateng' => $data['pinjaman_bpd_jateng'] ?? 0,
                    'bank_tgr' => $data['bank_tgr'] ?? 0,
                    'premi_bpjs_anggota' => $data['premi_bpjs_anggota'] ?? 0,
                    'dansos' => $data['dansos'] ?? 0,
                    'lainnya_1' => $data['lainnya_1'] ?? 0,
                    'lainnya_2' => $data['lainnya_2'] ?? 0,
                    'denda_fingerprint' => $data['denda_fingerprint'] ?? 0,
                ]
            );
        }

        return redirect()->back()->with('success', 'Data potongan berhasil disimpan & dikalkulasi!');
    }
}
