<?php

namespace App\Http\Controllers;

use App\Models\SalaryScale;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SalaryScaleController extends Controller
{
    public function index()
    {
        $scales = SalaryScale::orderBy('grade')->orderBy('mkg')->get();
        
        return Inertia::render('Master/SalaryScale/Index', [
            'scales' => $scales
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'grade' => 'required|string',
            'mkg' => 'required|integer|min:0',
            'amount' => 'required|numeric|min:0',
        ]);

        SalaryScale::updateOrCreate(
            ['grade' => $request->grade, 'mkg' => $request->mkg],
            ['amount' => $request->amount]
        );

        return redirect()->back()->with('success', 'Data gaji pokok berhasil disimpan.');
    }

    public function destroy(SalaryScale $salaryScale)
    {
        $salaryScale->delete();
        return redirect()->back()->with('success', 'Data gaji pokok berhasil dihapus.');
    }

    /**
     * Helper to get salary based on Education & Service Years
     */
    public function getSalary(Request $request)
    {
        $amount = SalaryScale::getAmount($request->education, $request->years);
        
        // For the frontend response, we still want to show which grade/mkg was used
        $edu = strtoupper($request->education);
        $grade = 'IV';
        if (str_contains($edu, 'SD') || str_contains($edu, 'SMP') || str_contains($edu, 'SLTP')) $grade = 'I';
        elseif (str_contains($edu, 'SMA') || str_contains($edu, 'SMK') || str_contains($edu, 'SLTA')) $grade = 'II';
        elseif (str_contains($edu, 'D1') || str_contains($edu, 'D2') || str_contains($edu, 'D3')) $grade = 'III';
        elseif (str_contains($edu, 'D4') || str_contains($edu, 'S1')) $grade = 'IV';
        elseif (str_contains($edu, 'S2')) $grade = 'V';
        elseif (str_contains($edu, 'S3')) $grade = 'VI';

        $mkg = floor((int)$request->years / 2) * 2;

        return response()->json([
            'amount' => $amount,
            'grade' => $grade,
            'mkg' => $mkg
        ]);
    }
}
