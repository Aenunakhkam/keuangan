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
    public function getReportData($month, $year)
    {
        $bpjsConfig = BpjsConfig::first();
        $salaries = Salary::with(['teacher.positions', 'teacher.bpjsCategory'])
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        return $salaries->map(function ($salary) use ($bpjsConfig) {
            $teacher = $salary->teacher;
            
            $jabatanText = $teacher && $teacher->positions->count() > 0 
                ? $teacher->positions->pluck('name')->join(', ') 
                : '-';

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

            $gajiPokok = (float) $salary->base_salary;
            $tunjanganJabatan = (float) $salary->allowance;

            $jumlahTunjangan = $tunjanganJabatan + $bpjsAllowance;
            $jumlahBruto = $gajiPokok + $jumlahTunjangan;
            $jumlahPotonganBpjs = $bpjsHealth + $bpjsNaker;
            
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
    }

    public function index(Request $request)
    {
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));

        $reportData = $this->getReportData($month, $year);

        return Inertia::render('Report/SalarySpj/Index', [
            'reportData' => $reportData,
            'filters' => [
                'month' => $month,
                'year' => $year,
            ]
        ]);
    }

    private function getCommonData($month, $year)
    {
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        return [
            'reportData' => $this->getReportData($month, $year),
            'monthName' => $months[$month] ?? 'Bulan',
            'year' => $year,
            'settingLogo' => \App\Models\Setting::where('key', 'logo_url')->value('value') ?? \App\Models\Setting::where('key', 'school_logo')->value('value'),
            'settingName' => \App\Models\Setting::where('key', 'school_name')->value('value'),
            'settingAddress' => \App\Models\Setting::where('key', 'school_address')->value('value'),
            'settingCity' => \App\Models\Setting::where('key', 'school_city')->value('value') ?? 'Tegal',
            'settingFooter' => \App\Models\Setting::where('key', 'copyright')->value('value'),
        ];
    }

    public function print(Request $request)
    {
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));
        
        return view('salary-spj-print', $this->getCommonData($month, $year));
    }

    public function excel(Request $request)
    {
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));
        $data = $this->getCommonData($month, $year);
        
        $filename = "Laporan_SPJ_Gaji_{$data['monthName']}_{$year}.xls";
        
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        return view('salary-spj-excel', $data);
    }
}
