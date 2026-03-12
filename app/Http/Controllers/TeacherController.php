<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Teacher::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('nip', 'like', '%' . $request->search . '%');
            });
        }

        $teachers = $query->with('positions')->latest()->paginate(10)->through(function ($teacher) {
            return [
                'id' => $teacher->id,
                'nip' => $teacher->nip,
                'name' => $teacher->name,
                'phone' => $teacher->phone,
                'address' => $teacher->address,
                'joined_date' => $teacher->joined_date,
                'teaching_hours' => $teacher->teaching_hours,
                'positions' => $teacher->positions->map(function ($pos) {
                    return ['id' => $pos->id, 'name' => $pos->name];
                })->toArray(),
            ];
        })->withQueryString();
        
        $positions = \App\Models\Position::orderBy('name')->get()->map(function ($pos) {
            return ['id' => $pos->id, 'name' => $pos->name];
        });

        return inertia('Teacher/Index', [
            'teachers' => $teachers,
            'positions' => $positions,
            'filters' => $request->only(['search'])
        ]);
    }

    public function store(StoreTeacherRequest $request)
    {
        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => ($request->nip ?? uniqid()) . '@school.local',
            'password' => \Illuminate\Support\Facades\Hash::make('12345678'),
        ]);
        $user->assignRole('guru');

        $data = $request->except('position_ids');
        $data['user_id'] = $user->id;

        $teacher = Teacher::create($data);
        
        if ($request->has('position_ids')) {
            $teacher->positions()->attach($request->position_ids);
        }

        return redirect()->back()->with('success', 'Data guru dan akun berhasil ditambahkan.');
    }

    // This new store method for students is added as per user instruction.
    // Note: Having two methods named 'store' in the same class is generally not
    // good practice unless they are intended to be distinct actions for different
    // resources, typically handled by separate controllers or distinct method names.
    public function update(UpdateTeacherRequest $request, Teacher $teacher)
    {
        $data = $request->except('position_ids');
        $teacher->update($data);
        
        if ($request->has('position_ids')) {
            $teacher->positions()->sync($request->position_ids);
        } else {
            $teacher->positions()->detach();
        }

        return redirect()->back()->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->delete();
        return redirect()->back()->with('success', 'Data guru berhasil dihapus.');
    }
}
