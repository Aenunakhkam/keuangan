<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Teacher;
use App\Models\BpjsConfig;
use App\Models\Salary;
use Carbon\Carbon;

class SalarySpjController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));

        // Ambil konfigurasi BPJS aktif
        $bpjsConfig = BpjsConfig::first();

        // Ambil data gaji bulan terkait beserta relasinya
        // Kita juga butuh data teacher dan posisinya untuk menampilkan nama & jabatan
        $salaries = Salary::with(['teacher.positions', 'teacher.bpjsCategory'])
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        // Transformasi data untuk dihitung secara on-the-fly (terutama untuk BPJS)
        $reportData = $salaries->map(function ($salary) use ($bpjsConfig) {
            $teacher = $salary->teacher;
            
            // Jabatan Text
            $jabatanText = $teacher && $teacher->positions->count() > 0 
                ? $teacher->positions->pluck('name')->join(', ') 
                : '-';

            // Menghitung BPJS on the fly (jika ada konfigurasi dan kategori)
            $bpjsAllowance = 0;
            $bpjsHealth = 0;
            $bpjsNaker = 0;

            if ($bpjsConfig && $teacher && $teacher->bpjsCategory) {
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

            // Gaji Pokok (dari tabel salaries)
            $gajiPokok = (float) $salary->base_salary;
            
            // Tunjangan Jabatan (dari tabel salaries, dikurangi tunjangan transport jika ingin persis, tapi transport saat ini sudah pisah di 'transport_per_day')
            $tunjanganJabatan = (float) $salary->allowance;

            // Perhitungan Matriks SPJ
            $jumlahTunjangan = $tunjanganJabatan + $bpjsAllowance;
            $jumlahBruto = $gajiPokok + $jumlahTunjangan;
            $jumlahPotonganBpjs = $bpjsHealth + $bpjsNaker;
            
            // Perhatikan: Jika sistem lama Netto sudah dihitung di 'salaries', di sini kita menghitung ulang
            // versi SPJ agar sesuai dengan penambahan BPJS yang on-the-fly
            // Jika ada potongan lain (seperti indisipliner), bisa dimasukkan ke net. 
            // Namun karena format Excel SPJ hanya spesifik BPJS, kita hitung murni seperti format.
            $jumlahNetto = $jumlahBruto - $jumlahPotonganBpjs;

            return [
                'id' => $salary->id,
                'nama' => $teacher ? $teacher->name : 'Unknown',
                'jabatan' => $jabatanText,
                'gaji_pokok' => $gajiPokok,
                'tunjangan_jabatan' => $tunjanganJabatan,
                'tunjangan_bpjs' => $bpjsAllowance,
                'jumlah_tunjangan' => $jumlahTunjangan,
                'jumlah_bruto' => $jumlahBruto,
                'potongan_kes' => $bpjsHealth,
                'potongan_naker' => $bpjsNaker,
                'jumlah_potongan' => $jumlahPotonganBpjs,
                'jumlah_netto' => $jumlahNetto,
            ];
        });

        return Inertia::render('Report/SalarySpj/Index', [
            'reportData' => $reportData,
            'filters' => [
                'month' => $month,
                'year' => $year,
            ]
        ]);
    }
}
