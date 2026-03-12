<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassRoom;
use Inertia\Inertia;

class ClassRoomController extends Controller
{
    public function index(Request $request)
    {
        $query = ClassRoom::query();
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        $classRooms = $query->latest()->get()->map(function ($cr) {
            return [
                'id' => $cr->id,
                'name' => $cr->name,
                'description' => $cr->description,
            ];
        });

        return Inertia::render('Academic/ClassRoom/Index', [
            'classRooms' => $classRooms,
            'filters' => $request->only(['search'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        ClassRoom::create($validated);
        return redirect()->back()->with('success', 'Data kelas berhasil ditambahkan.');
    }

    public function update(Request $request, ClassRoom $classRoom)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        $classRoom->update($validated);
        return redirect()->back()->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy(ClassRoom $classRoom)
    {
        $classRoom->delete();
        return redirect()->back()->with('success', 'Data kelas berhasil dihapus.');
    }
}
