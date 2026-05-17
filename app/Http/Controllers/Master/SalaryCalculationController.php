<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\SalaryScale;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SalaryCalculationController extends Controller
{
    public function index(Request $request)
    {
        $query = Teacher::query();
        
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $teachers = $query->latest()->paginate($request->input('per_page', 10))->through(function ($teacher) {
            $normative = SalaryScale::getAmount($teacher->education, $teacher->service_years);
            
            // Formula assumption: proportional to teaching hours (base 32)
            // But if user wants it exactly like the image, for 32 it is 100%
            $real = $teacher->teaching_hours > 0 
                ? ($normative * $teacher->teaching_hours) / 32 
                : $normative;

            $sanction_amount = ($real * $teacher->discipline_percentage) / 100;
            $after_absent = $real - $sanction_amount;

            return [
                'id' => $teacher->id,
                'name' => $teacher->name,
                'education' => $teacher->education,
                'service_years' => $teacher->service_years,
                'basic_salary_normative' => $normative,
                'teaching_hours' => $teacher->teaching_hours,
                'basic_salary_real' => $real,
                'discipline_percentage' => $teacher->discipline_percentage,
                'sanction_amount' => $sanction_amount,
                'after_absent' => $after_absent,
            ];
        })->withQueryString();

        return Inertia::render('Master/SalaryCalculation/Index', [
            'teachers' => $teachers,
            'filters' => $request->only(['search', 'per_page'])
        ]);
    }

    public function update(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'teaching_hours' => 'required|integer|min:0',
            'discipline_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $teacher->update($validated);

        return redirect()->back()->with('success', 'Perhitungan gaji berhasil diperbarui.');
    }
}
