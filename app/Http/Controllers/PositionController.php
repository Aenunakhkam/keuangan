<?php

namespace App\Http\Controllers;

use App\Models\Position;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PositionController extends Controller
{
    public function index(Request $request)
    {
        $query = Position::query();
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $positions = $query->latest()->get()->map(function ($pos) {
            return [
                'id' => $pos->id,
                'name' => $pos->name,
                'allowance' => $pos->allowance,
                'health_allowance' => $pos->health_allowance,
            ];
        });

        return Inertia::render('Master/Position/Index', [
            'positions' => $positions,
            'filters' => $request->only(['search'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:positions,name',
            'allowance' => 'required|numeric|min:0',
            'health_allowance' => 'required|numeric|min:0',
        ]);

        Position::create($validated);
        return redirect()->back()->with('success', 'Data jabatan berhasil ditambahkan.');
    }

    public function update(Request $request, Position $position)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:positions,name,' . $position->id,
            'allowance' => 'required|numeric|min:0',
            'health_allowance' => 'required|numeric|min:0',
        ]);

        $position->update($validated);
        return redirect()->back()->with('success', 'Data jabatan berhasil diperbarui.');
    }

    public function destroy(Position $position)
    {
        // Cegah hapus jika masih dipakai teacher (diurus di DB constraint nullOnDelete)
        $position->delete();
        return redirect()->back()->with('success', 'Data jabatan berhasil dihapus.');
    }
}
