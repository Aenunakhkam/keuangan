<?php

namespace App\Exports;

use App\Models\Salary;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DashboardBendaharaExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Salary::with('teacher')->latest()->limit(50)->get();
    }

    public function headings(): array
    {
        return [
            'Nama Pegawai',
            'Bulan',
            'Tahun',
            'Gaji Pokok',
            'Tunjangan',
            'Potongan',
            'Gaji Bersih',
            'Status'
        ];
    }

    public function map($row): array
    {
        return [
            $row->teacher ? $row->teacher->name : '-',
            $row->month,
            $row->year,
            $row->base_salary,
            $row->allowance,
            $row->deduction,
            $row->net_salary,
            $row->status === 'paid' ? 'Lunas' : 'Proses'
        ];
    }
}
