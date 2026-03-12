<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FeeType;
use App\Http\Requests\StoreFeeTypeRequest;
use App\Http\Requests\UpdateFeeTypeRequest;

class FeeTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = FeeType::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $feeTypes = $query->latest()->get()->map(function ($ft) {
            return [
                'id' => $ft->id,
                'name' => $ft->name,
                'default_amount' => $ft->default_amount,
            ];
        });

        return inertia('FeeType/Index', [
            'feeTypes' => $feeTypes,
            'filters' => $request->only(['search'])
        ]);
    }

    public function store(StoreFeeTypeRequest $request)
    {
        FeeType::create($request->validated());
        return redirect()->back()->with('success', 'Jenis pembayaran berhasil ditambahkan.');
    }

    public function update(UpdateFeeTypeRequest $request, FeeType $feeType)
    {
        $feeType->update($request->validated());
        return redirect()->back()->with('success', 'Jenis pembayaran berhasil diperbarui.');
    }

    public function destroy(FeeType $feeType)
    {
        $feeType->delete();
        return redirect()->back()->with('success', 'Jenis pembayaran berhasil dihapus.');
    }
}
