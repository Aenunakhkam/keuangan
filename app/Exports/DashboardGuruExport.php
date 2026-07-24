<?php

namespace App\Exports;

use App\Models\Salary;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DashboardGuruExport implements FromCollection, WithHeadings, WithMapping
{
    protected $teacherId;

    public function __construct($teacherId)
    {
        $this->teacherId = $teacherId;
    }

    public function collection()
    {
        return Salary::where('teacher_id', $this->teacherId)->latest()->get();
    }

    public function headings(): array
    {
        return [
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
