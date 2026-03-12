<?php

use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

echo "Syncing Students...\n";
$students = Student::whereNull('user_id')->get();
foreach ($students as $student) {
    $user = User::create([
        'name' => $student->name,
        'email' => $student->nis . '@school.local',
        'password' => Hash::make('12345678'),
    ]);
    $user->assignRole('siswa');
    $student->update(['user_id' => $user->id]);
    echo "Created user for student: {$student->name}\n";
}

echo "Syncing Teachers...\n";
$teachers = Teacher::whereNull('user_id')->get();
foreach ($teachers as $teacher) {
    $user = User::create([
        'name' => $teacher->name,
        'email' => ($teacher->nip ?? uniqid()) . '@school.local',
        'password' => Hash::make('12345678'),
    ]);
    $user->assignRole('guru');
    $teacher->update(['user_id' => $user->id]);
    echo "Created user for teacher: {$teacher->name}\n";
}

echo "Sync completed!\n";
