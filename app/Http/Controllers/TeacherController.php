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

        $bpjsConfig = \App\Models\BpjsConfig::first();

        $teachers = $query->with(['positions', 'bpjsCategory'])->latest()->paginate($request->input('per_page', 10))->through(function ($teacher) use ($bpjsConfig) {
            $bpjsAllowance = 0;
            $bpjsHealth = 0;
            $bpjsNaker = 0;

            if ($bpjsConfig && $teacher->bpjsCategory) {
                $category = $teacher->bpjsCategory;
                $umk = (float) $bpjsConfig->umk_reference;
                
                $healthSchool = (float) $bpjsConfig->health_school_percent;
                $healthEmp = (float) $bpjsConfig->health_employee_percent;
                $nakerSchool = (float) $bpjsConfig->naker_school_percent;
                $nakerEmp = (float) $bpjsConfig->naker_employee_percent;

                $healthTotalPercent = $healthSchool + $healthEmp;
                $nakerTotalPercent = $nakerSchool + $nakerEmp;

                $allowancePercent = 0;
                if ($category->code === 'A') {
                    $allowancePercent = $healthSchool + $nakerSchool;
                } elseif ($category->code === 'B') {
                    $allowancePercent = $healthTotalPercent + $nakerTotalPercent;
                } elseif ($category->code === 'C') {
                    $allowancePercent = $healthTotalPercent;
                } elseif ($category->code === 'D') {
                    $allowancePercent = $nakerTotalPercent;
                }

                $bpjsAllowance = round($umk * $allowancePercent / 100);
                $bpjsHealth = $category->has_health ? round($umk * $healthTotalPercent / 100) : 0;
                $bpjsNaker = $category->has_naker ? round($umk * $nakerTotalPercent / 100) : 0;
            }

            return [
                'id' => $teacher->id,
                'name' => $teacher->name,
                'nipty' => $teacher->nipty,
                'nipy' => $teacher->nipy,
                'birth_place' => $teacher->birth_place,
                'birth_date' => $teacher->birth_date ? $teacher->birth_date->format('Y-m-d') : null,
                'gender' => $teacher->gender,
                'education' => $teacher->education,
                'major' => $teacher->major,
                'unit' => $teacher->unit,
                'service_years' => $teacher->service_years,
                'service_months' => $teacher->service_months,
                'grade' => $teacher->grade,
                'mkg' => floor($teacher->service_years / 2) * 2,
                'basic_salary' => $teacher->basic_salary > 0 ? (float)$teacher->basic_salary : (float)\App\Models\SalaryScale::getAmount($teacher->education, $teacher->service_years),
                'other_allowance' => $teacher->other_allowance,
                'joined_date' => $teacher->joined_date ? $teacher->joined_date->format('Y-m-d') : null,
                'bpjs_info' => [
                    'category_name' => $teacher->bpjsCategory ? $teacher->bpjsCategory->name : 'Belum Diatur',
                    'category_code' => $teacher->bpjsCategory ? $teacher->bpjsCategory->code : null,
                    'has_health' => $teacher->bpjsCategory ? (bool)$teacher->bpjsCategory->has_health : false,
                    'has_naker' => $teacher->bpjsCategory ? (bool)$teacher->bpjsCategory->has_naker : false,
                    'bpjs_allowance' => $bpjsAllowance,
                    'bpjs_health' => $bpjsHealth,
                    'bpjs_naker' => $bpjsNaker,
                ],
                'positions' => $teacher->positions->map(function ($pos) {
                    return [
                        'id' => $pos->id, 
                        'name' => $pos->name, 
                        'allowance' => $pos->allowance,
                    ];
                })->toArray(),
            ];
        })->withQueryString();
        
        $positions = \App\Models\Position::orderBy('name')->get()->map(function ($pos) {
            return [
                'id' => $pos->id, 
                'name' => $pos->name, 
                'allowance' => $pos->allowance,
            ];
        });

        return inertia('Teacher/Index', [
            'teachers' => $teachers,
            'positions' => $positions,
            'filters' => $request->only(['search', 'per_page'])
        ]);
    }

    public function store(StoreTeacherRequest $request)
    {
        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => ($request->nipty ?? $request->nipy ?? uniqid()) . '@school.local',
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

    public function downloadTemplate()
    {
        $headers = [
            'Nama Pegawai (Wajib)', 
            'NIPTY (Opsional)', 
            'NIPY (Opsional)', 
            'Jenis Kelamin (L/P)', 
            'Tempat Lahir (Opsional)', 
            'Tanggal Lahir (Format: YYYY-MM-DD)', 
            'Pendidikan (SMA/SMK/D3/S1/S2/S3)', 
            'Jurusan / Prodi (Opsional)',
            'Unit Kerja (Contoh: SMK)', 
            'Masa Kerja Tahun (Angka)', 
            'Masa Kerja Bulan (Angka: 0-11)', 
            'Golongan (Opsional)', 
            'Tunjangan Lainnya (Angka)', 
            'Tanggal Masuk (Format: YYYY-MM-DD)', 
            'Nama Jabatan (Pisahkan koma jika lebih dari satu, contoh: Guru, Bendahara Sekolah)'
        ];

        $examples = [
            [
                'Tina Agustina, S.Pd', 
                '200103121', 
                '', 
                'P', 
                'Tegal', 
                '1990-05-12', 
                'S1', 
                'Pendidikan Matematika',
                'SMK', 
                '5', 
                '6', 
                'III/a', 
                '50000', 
                '2018-07-01', 
                'Guru, Bendahara Sekolah'
            ],
            [
                'Ahmad Subarjo, M.Pd', 
                '', 
                '199703019', 
                'L', 
                'Brebes', 
                '1985-11-23', 
                'S2', 
                'Manajemen Pendidikan',
                'SMK', 
                '10', 
                '0', 
                'III/c', 
                '100000', 
                '2014-01-15', 
                'Kepala Sekolah'
            ]
        ];

        $callback = function() use ($headers, $examples) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            fputcsv($file, $headers, ';'); // Semicolon is better for Indonesian Excel defaults
            
            foreach ($examples as $row) {
                fputcsv($file, $row, ';');
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=template_import_pegawai.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();
        
        $handle = fopen($path, 'r');
        if (!$handle) {
            return redirect()->back()->with('error', 'Gagal membuka file CSV.');
        }

        // Determine separator (comma or semicolon)
        $firstLine = fgets($handle);
        $separator = (strpos($firstLine, ';') !== false) ? ';' : ',';
        rewind($handle);

        // Skip UTF-8 BOM if present
        $bom = fread($handle, 3);
        if ($bom !== chr(0xEF).chr(0xBB).chr(0xBF)) {
            rewind($handle);
        }

        // Skip header
        fgetcsv($handle, 0, $separator);

        $successCount = 0;
        $errors = [];
        $rowNum = 1;

        \DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 0, $separator)) !== false) {
                $rowNum++;
                // Skip empty rows
                if (empty($row) || !isset($row[0]) || empty(trim($row[0]))) {
                    continue;
                }

                $name = trim($row[0]);
                $nipty = !empty($row[1]) ? trim($row[1]) : null;
                $nipy = !empty($row[2]) ? trim($row[2]) : null;
                $gender = !empty($row[3]) ? strtoupper(trim($row[3])) : 'L';
                $birthPlace = !empty($row[4]) ? trim($row[4]) : null;
                $birthDate = !empty($row[5]) ? trim($row[5]) : null;
                $education = !empty($row[6]) ? trim($row[6]) : 'S1';
                $major = !empty($row[7]) ? trim($row[7]) : null;
                $unit = !empty($row[8]) ? trim($row[8]) : 'SMK';
                $serviceYears = isset($row[9]) ? (int) $row[9] : 0;
                $serviceMonths = isset($row[10]) ? (int) $row[10] : 0;
                $grade = !empty($row[11]) ? trim($row[11]) : null;
                $otherAllowance = isset($row[12]) ? (float) $row[12] : 0;
                $joinedDate = !empty($row[13]) ? trim($row[13]) : null;
                $positionsStr = !empty($row[14]) ? trim($row[14]) : '';

                // Validate birth date and joined date format
                if ($birthDate && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $birthDate)) {
                    $birthDate = null;
                }
                if ($joinedDate && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $joinedDate)) {
                    $joinedDate = null;
                }

                // Check uniqueness of NIPTY / NIPY
                if ($nipty && Teacher::where('nipty', $nipty)->exists()) {
                    $errors[] = "Baris $rowNum: NIPTY '$nipty' sudah terdaftar.";
                    continue;
                }
                if ($nipy && Teacher::where('nipy', $nipy)->exists()) {
                    $errors[] = "Baris $rowNum: NIPY '$nipy' sudah terdaftar.";
                    continue;
                }

                // Create User Account
                $email = ($nipty ?? $nipy ?? uniqid()) . '@school.local';
                $user = \App\Models\User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => \Illuminate\Support\Facades\Hash::make('12345678'),
                ]);
                $user->assignRole('guru');

                // Calculate basic salary using SalaryScale
                $basicSalary = \App\Models\SalaryScale::getAmount($education, $serviceYears);

                // Create Teacher
                $teacher = Teacher::create([
                    'user_id' => $user->id,
                    'name' => $name,
                    'nipty' => $nipty,
                    'nipy' => $nipy,
                    'gender' => $gender,
                    'birth_place' => $birthPlace,
                    'birth_date' => $birthDate,
                    'education' => $education,
                    'major' => $major,
                    'unit' => $unit,
                    'service_years' => $serviceYears,
                    'service_months' => $serviceMonths,
                    'grade' => $grade,
                    'basic_salary' => $basicSalary,
                    'other_allowance' => $otherAllowance,
                    'joined_date' => $joinedDate,
                ]);

                // Map and attach positions by matching names
                if (!empty($positionsStr)) {
                    $positionsArr = array_map('trim', explode(',', $positionsStr));
                    $posIds = [];
                    foreach ($positionsArr as $posName) {
                        if (!empty($posName)) {
                            $pos = \App\Models\Position::firstOrCreate(
                                ['name' => $posName],
                                ['allowance' => 0]
                            );
                            $posIds[] = $pos->id;
                        }
                    }
                    $teacher->positions()->attach($posIds);
                }

                $successCount++;
            }
            fclose($handle);

            if (!empty($errors)) {
                \DB::rollBack();
                return redirect()->back()->with('error', 'Gagal mengimpor data. Kesalahan: <br>' . implode('<br>', $errors));
            }

            \DB::commit();
            return redirect()->back()->with('success', "Sukses mengimpor $successCount data pegawai.");
        } catch (\Exception $e) {
            \DB::rollBack();
            fclose($handle);
            return redirect()->back()->with('error', 'Terjadi kesalahan sistem saat impor: ' . $e->getMessage());
        }
    }
}
