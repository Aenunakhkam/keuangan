<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Salary;
use App\Models\Teacher;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\DashboardGuruExport;
use App\Exports\DashboardBendaharaExport;

class DashboardExportController extends Controller
{
    public function exportPdf(Request $request)
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first() ?? 'user';
        
        $settingLogo = Setting::where('key', 'school_logo')->value('value');
        $settingAddress = Setting::where('key', 'school_address')->value('value');
        $settingName = Setting::where('key', 'school_name')->value('value');

        if ($role === 'guru') {
            $teacher = Teacher::where('user_id', $user->id)->first();
            if (!$teacher) {
                return back()->with('error', 'Data guru tidak ditemukan');
            }
            $salaries = Salary::where('teacher_id', $teacher->id)->latest()->get();
            $pdf = Pdf::loadView('reports.dashboard-guru-pdf', compact('salaries', 'teacher', 'settingLogo', 'settingAddress', 'settingName'));
            return $pdf->download('riwayat-gaji-saya.pdf');
        } elseif ($role === 'bendahara') {
            $recentSalaries = Salary::with('teacher')->latest()->limit(50)->get();
            $pdf = Pdf::loadView('reports.dashboard-bendahara-pdf', compact('recentSalaries', 'settingLogo', 'settingAddress', 'settingName'));
            return $pdf->download('riwayat-penggajian-terakhir.pdf');
        }

        return back()->with('error', 'Role tidak diizinkan');
    }

    public function exportExcel(Request $request)
    {
        $user = auth()->user();
        $role = $user->getRoleNames()->first() ?? 'user';

        if ($role === 'guru') {
            $teacher = Teacher::where('user_id', $user->id)->first();
            if (!$teacher) {
                return back()->with('error', 'Data guru tidak ditemukan');
            }
            return Excel::download(new DashboardGuruExport($teacher->id), 'riwayat-gaji-saya.xlsx');
        } elseif ($role === 'bendahara') {
            return Excel::download(new DashboardBendaharaExport(), 'riwayat-penggajian-terakhir.xlsx');
        }

        return back()->with('error', 'Role tidak diizinkan');
    }
}
