<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\BpjsConfig;
use App\Models\BpjsCategory;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BpjsController extends Controller
{
    public function index(Request $request)
    {
        $config = BpjsConfig::first() ?? new BpjsConfig([
            'umk_reference' => 0,
            'health_school_percent' => 0,
            'health_employee_percent' => 0,
            'naker_school_percent' => 0,
            'naker_employee_percent' => 0,
        ]);
        $categories = BpjsCategory::all();
        
        $query = Teacher::query()->with('bpjsCategory');
        
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $teachers = $query->latest()->paginate($request->input('per_page', 10))->withQueryString();

        return Inertia::render('Master/Bpjs/Index', [
            'config' => $config,
            'categories' => $categories,
            'teachers' => $teachers,
            'filters' => $request->only(['search', 'per_page'])
        ]);
    }

    public function updateConfig(Request $request)
    {
        $validated = $request->validate([
            'umk_reference' => 'required|numeric|min:0',
            'health_school_percent' => 'required|numeric|min:0|max:100',
            'health_employee_percent' => 'required|numeric|min:0|max:100',
            'naker_school_percent' => 'required|numeric|min:0|max:100',
            'naker_employee_percent' => 'required|numeric|min:0|max:100',
        ]);

        $config = BpjsConfig::first();
        if ($config) {
            $config->update($validated);
        } else {
            BpjsConfig::create($validated);
        }

        return redirect()->back()->with('success', 'Konfigurasi BPJS berhasil diperbarui.');
    }

    public function updateTeacherCategory(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'bpjs_category_id' => 'nullable|exists:bpjs_categories,id',
        ]);

        $teacher->update($validated);

        return redirect()->back()->with('success', 'Kategori BPJS pegawai berhasil diperbarui.');
    }
}
