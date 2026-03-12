<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = Payment::with('studentBill.student', 'studentBill.feeType')->latest()->paginate(10)->through(function ($payment) {
            return [
                'id' => $payment->id,
                'student_bill_id' => $payment->student_bill_id,
                'amount' => $payment->amount,
                'payment_date' => $payment->payment_date,
                'method' => $payment->method,
                'reference' => $payment->reference,
                'student_bill' => $payment->studentBill ? [
                    'id' => $payment->studentBill->id,
                    'amount' => $payment->studentBill->amount,
                    'status' => $payment->studentBill->status,
                    'student' => $payment->studentBill->student ? ['id' => $payment->studentBill->student->id, 'name' => $payment->studentBill->student->name] : null,
                    'fee_type' => $payment->studentBill->feeType ? ['id' => $payment->studentBill->feeType->id, 'name' => $payment->studentBill->feeType->name] : null,
                ] : null,
            ];
        });
        
        return inertia('Payment/Index', [
            'payments' => $payments
        ]);
    }

    public function store(StorePaymentRequest $request)
    {
        $payment = Payment::create($request->validated());

        // Update bill status
        $bill = StudentBill::find($payment->student_bill_id);
        $totalPaid = Payment::where('student_bill_id', $bill->id)->sum('amount');
        
        if ($totalPaid >= $bill->amount) {
            $bill->update(['status' => 'paid']);
        } elseif ($totalPaid > 0) {
            $bill->update(['status' => 'partial']);
        }

        // Create Cash Transaction (Income)
        CashTransaction::create([
            'type' => 'income',
            'category' => 'Pembayaran Siswa',
            'amount' => $payment->amount,
            'description' => "Pembayaran {$bill->feeType->name} - {$bill->student->name}",
            'transaction_date' => $payment->payment_date
        ]);

        return redirect()->back()->with('success', 'Pembayaran berhasil dicatat.');
    }

    public function destroy(Payment $payment)
    {
        $billId = $payment->student_bill_id;
        $payment->delete();

        // Re-check bill status
        $bill = StudentBill::find($billId);
        $totalPaid = Payment::where('student_bill_id', $bill->id)->sum('amount');
        
        if ($totalPaid >= $bill->amount) {
            $bill->update(['status' => 'paid']);
        } elseif ($totalPaid > 0) {
            $bill->update(['status' => 'partial']);
        } else {
            $bill->update(['status' => 'unpaid']);
        }

        return redirect()->back()->with('success', 'Data pembayaran berhasil dihapus.');
    }
}
