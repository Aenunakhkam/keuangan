<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AcademicYear;
use Inertia\Inertia;

class AcademicYearController extends Controller
{
    public function index(Request $request)
    {
        $query = AcademicYear::query();
        if ($request->filled('search')) {
            $query->where('year_name', 'like', '%' . $request->search . '%');
        }
        $academicYears = $query->latest()->get()->map(function ($ay) {
            return [
                'id' => $ay->id,
                'year_name' => $ay->year_name,
                'semester' => $ay->semester,
                'is_active' => $ay->is_active,
            ];
        });

        return Inertia::render('Academic/AcademicYear/Index', [
            'academicYears' => $academicYears,
            'filters' => $request->only(['search'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'year_name' => 'required|string|max:255',
            'semester'  => 'required|in:1,2',
            'is_active' => 'boolean',
        ]);

        if (isset($validated['is_active']) && $validated['is_active']) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
        }

        AcademicYear::create($validated);
        return redirect()->back()->with('success', 'Tahun pelajaran berhasil ditambahkan.');
    }

    public function update(Request $request, AcademicYear $academicYear)
    {
        $validated = $request->validate([
            'year_name' => 'required|string|max:255',
            'semester'  => 'required|in:1,2',
            'is_active' => 'boolean',
        ]);

        if (isset($validated['is_active']) && $validated['is_active']) {
            AcademicYear::where('is_active', true)->where('id', '!=', $academicYear->id)->update(['is_active' => false]);
        }

        $academicYear->update($validated);
        return redirect()->back()->with('success', 'Tahun pelajaran berhasil diperbarui.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();
        return redirect()->back()->with('success', 'Tahun pelajaran berhasil dihapus.');
    }
}
