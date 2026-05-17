<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BpjsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Default Config
        \App\Models\BpjsConfig::updateOrCreate(['id' => 1], [
            'umk_reference' => 2333586,
            'health_school_percent' => 4.00,
            'health_employee_percent' => 1.00,
            'naker_school_percent' => 6.24,
            'naker_employee_percent' => 3.00,
        ]);

        // Default Categories
        $categories = [
            [
                'code' => 'A',
                'name' => 'Peserta BPJS Kes dan Naker (Berbagi Premi)',
                'has_health' => true,
                'has_naker' => true,
                'is_full_school' => false,
            ],
            [
                'code' => 'B',
                'name' => 'Peserta BPJS Kes dan Naker (Full Sekolah)',
                'has_health' => true,
                'has_naker' => true,
                'is_full_school' => true,
            ],
            [
                'code' => 'C',
                'name' => 'Peserta BPJS Kesehatan Saja',
                'has_health' => true,
                'has_naker' => false,
                'is_full_school' => true,
            ],
            [
                'code' => 'D',
                'name' => 'Peserta BPJS Ketenagakerjaan Saja',
                'has_health' => false,
                'has_naker' => true,
                'is_full_school' => true,
            ],
            [
                'code' => 'E',
                'name' => 'Belum Peserta',
                'has_health' => false,
                'has_naker' => false,
                'is_full_school' => false,
            ],
        ];

        foreach ($categories as $cat) {
            \App\Models\BpjsCategory::updateOrCreate(['code' => $cat['code']], $cat);
        }
    }
}
