<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Border;

use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class TeacherTemplateExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize, WithColumnFormatting
{
    public function array(): array
    {
        return [
            [
                'Tina Agustina, S.Pd', 
                'tina.agustina@contoh.com',
                '200103121', 
                '', 
                'P', 
                'Tegal', 
                '1990-05-12', 
                'S1', 
                'Pendidikan Matematika',
                'SMK', 
                5, 
                6, 
                'III/a', 
                50000, 
                '2018-07-01', 
                'Guru, Bendahara Sekolah'
            ],
            [
                'Ahmad Subarjo, M.Pd', 
                '', 
                '', 
                '199703019', 
                'L', 
                'Brebes', 
                '1985-11-23', 
                'S2', 
                'Manajemen Pendidikan',
                'SMK', 
                10, 
                0, 
                'III/c', 
                100000, 
                '2014-01-15', 
                'Kepala Sekolah'
            ]
        ];
    }

    public function headings(): array
    {
        return [
            'Nama Pegawai (Wajib)', 
            'Email Akun (Opsional)',
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
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text with colored background
            1    => [
                'font' => [
                    'bold' => true,
                    'color' => ['argb' => Color::COLOR_WHITE]
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF003B73']
                ],
            ],
        ];
    }

    public function columnFormats(): array
    {
        return [
            // Kolom K: Masa Kerja Tahun
            'K' => NumberFormat::FORMAT_NUMBER,
            // Kolom L: Masa Kerja Bulan
            'L' => NumberFormat::FORMAT_NUMBER,
            // Kolom N: Tunjangan Lainnya
            'N' => '#,##0',
        ];
    }
}
