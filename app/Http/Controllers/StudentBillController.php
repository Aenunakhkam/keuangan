<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StudentBill;
use App\Models\Student;
use App\Models\FeeType;
use App\Http\Requests\StoreStudentBillRequest;
use App\Http\Requests\UpdateStudentBillRequest;

class StudentBillController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $bills = StudentBill::with(['student', 'feeType'])
            ->when($request->search, function ($query, $search) {
                $query->whereHas('student', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")->orWhere('nis', 'like', "%{$search}%");
                });
            })
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->paginate(10)
            ->through(function ($bill) {
                return [
                    'id' => $bill->id,
                    'student_id' => $bill->student_id,
                    'fee_type_id' => $bill->fee_type_id,
                    'amount' => $bill->amount,
                    'due_date' => $bill->due_date,
                    'status' => $bill->status,
                    'student' => $bill->student ? ['id' => $bill->student->id, 'name' => $bill->student->name, 'nis' => $bill->student->nis] : null,
                    'fee_type' => $bill->feeType ? ['id' => $bill->feeType->id, 'name' => $bill->feeType->name] : null,
                ];
            })->withQueryString();

        return inertia('StudentBill/Index', [
            'bills' => $bills,
            'students' => Student::all()->map(function ($s) {
                return ['id' => $s->id, 'name' => $s->name, 'nis' => $s->nis];
            }),
            'feeTypes' => FeeType::all()->map(function ($ft) {
                return ['id' => $ft->id, 'name' => $ft->name, 'default_amount' => $ft->default_amount];
            }),
            'filters' => $request->only(['search', 'status'])
        ]);
    }

    public function store(StoreStudentBillRequest $request)
    {
        StudentBill::create($request->validated());
        return redirect()->back()->with('success', 'Tagihan berhasil dibuat.');
    }

    public function generateBulk(Request $request)
    {
        $request->validate([
            'fee_type_id' => 'required|exists:fee_types,id',
            'due_date' => 'required|date',
        ]);

        $feeType = FeeType::find($request->fee_type_id);
        $students = Student::all();

        foreach ($students as $student) {
            // Check if bill already exists to avoid duplicates (optional but recommended)
            StudentBill::create([
                'student_id' => $student->id,
                'fee_type_id' => $feeType->id,
                'amount' => $feeType->default_amount,
                'due_date' => $request->due_date,
                'status' => 'unpaid'
            ]);
        }

        return redirect()->back()->with('success', 'Tagihan massal berhasil di-generate.');
    }

    public function update(UpdateStudentBillRequest $request, StudentBill $studentBill)
    {
        $studentBill->update($request->validated());
        return redirect()->back()->with('success', 'Tagihan berhasil diperbarui.');
    }

    public function destroy(StudentBill $studentBill)
    {
        $studentBill->delete();
        return redirect()->back()->with('success', 'Tagihan berhasil dihapus.');
    }
}
