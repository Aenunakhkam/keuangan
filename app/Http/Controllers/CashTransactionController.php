<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CashTransaction;
use App\Http\Requests\StoreCashTransactionRequest;
use App\Http\Requests\UpdateCashTransactionRequest;

class CashTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $transactions = CashTransaction::query()
            ->when($request->type, fn($q, $type) => $q->where('type', $type))
            ->when($request->category, fn($q, $cat) => $q->where('category', 'like', "%{$cat}%"))
            ->latest()
            ->paginate(15)
            ->through(function ($tx) {
                return [
                    'id' => $tx->id,
                    'type' => $tx->type,
                    'category' => $tx->category,
                    'amount' => $tx->amount,
                    'description' => $tx->description,
                    'transaction_date' => $tx->transaction_date,
                    'reference' => $tx->reference,
                ];
            })->withQueryString();

        $stats = [
            'total_income' => CashTransaction::where('type', 'income')->sum('amount'),
            'total_expense' => CashTransaction::where('type', 'expense')->sum('amount'),
            'balance' => CashTransaction::where('type', 'income')->sum('amount') - CashTransaction::where('type', 'expense')->sum('amount'),
        ];

        return inertia('CashTransaction/Index', [
            'transactions' => $transactions,
            'stats' => $stats,
            'filters' => $request->only(['type', 'category'])
        ]);
    }

    public function store(StoreCashTransactionRequest $request)
    {
        CashTransaction::create($request->validated());
        return redirect()->back()->with('success', 'Transaksi berhasil dicatat.');
    }

    public function update(UpdateCashTransactionRequest $request, CashTransaction $cashTransaction)
    {
        $cashTransaction->update($request->validated());
        return redirect()->back()->with('success', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(CashTransaction $cashTransaction)
    {
        $cashTransaction->delete();
        return redirect()->back()->with('success', 'Transaksi berhasil dihapus.');
    }
}
