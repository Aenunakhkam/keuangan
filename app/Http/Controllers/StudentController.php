<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\ClassRoom;
use App\Http\Requests\StoreStudentRequest;
use App\Http\Requests\UpdateStudentRequest;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Student::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('nis', 'like', '%' . $request->search . '%');
            });
        }

        $students = $query->with('classRoom')->latest()->paginate(10)->through(function ($student) {
            return [
                'id' => $student->id,
                'nis' => $student->nis,
                'name' => $student->name,
                'class_room_id' => $student->class_room_id,
                'gender' => $student->gender,
                'address' => $student->address,
                'phone' => $student->phone,
                'parent_name' => $student->parent_name,
                'class_room' => $student->classRoom ? [
                    'id' => $student->classRoom->id,
                    'name' => $student->classRoom->name,
                ] : null,
            ];
        })->withQueryString();

        return inertia('Student/Index', [
            'students' => $students,
            'classRooms' => ClassRoom::orderBy('name')->get()->map(function ($cr) {
                return ['id' => $cr->id, 'name' => $cr->name];
            }),
            'filters' => $request->only(['search'])
        ]);
    }

    public function store(StoreStudentRequest $request)
    {
        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->nis . '@school.local',
            'password' => \Illuminate\Support\Facades\Hash::make('12345678'),
        ]);
        $user->assignRole('siswa');

        $data = $request->validated();
        $data['user_id'] = $user->id;

        Student::create($data);
        return redirect()->back()->with('success', 'Data siswa dan akun berhasil ditambahkan.');
    }

    public function update(UpdateStudentRequest $request, Student $student)
    {
        $student->update($request->validated());
        return redirect()->back()->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->back()->with('success', 'Data siswa berhasil dihapus.');
    }
}
