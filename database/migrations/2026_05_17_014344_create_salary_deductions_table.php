<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('salary_deductions', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke Data Pegawai dan Data Penggajian
            $table->foreignId('salary_id')->constrained('salaries')->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('teachers')->cascadeOnDelete();
            
            // Kolom Dasar dari Laporan SPJ (sebagai rujukan Netto Pendapatan)
            $table->decimal('spj_netto', 15, 2)->default(0)->comment('Nilai Netto dari Laporan SPJ (Gaji Pokok + Tunjangan - BPJS)');

            // KLUSTER 1: KOPERASI
            $table->decimal('tab_khusus', 15, 2)->default(0)->comment('8% dari spj_netto');
            $table->decimal('simpanan_wajib', 15, 2)->default(5000);
            $table->decimal('simpanan_sukarela', 15, 2)->default(0);
            $table->decimal('angsuran_koperasi', 15, 2)->default(0);
            $table->decimal('jumlah_koperasi', 15, 2)->default(0);

            // KLUSTER 2: BPD SLAWI & KEMANTRAN
            $table->decimal('dplk_slawi', 15, 2)->default(0);
            $table->decimal('dplk_kemantran', 15, 2)->default(0);
            $table->decimal('pinjaman_bpd_jateng', 15, 2)->default(0);
            $table->decimal('jumlah_bpd', 15, 2)->default(0);

            // KLUSTER 3: SEKTORAL & LAINNYA
            $table->decimal('bank_tgr', 15, 2)->default(0);
            $table->decimal('premi_bpjs_anggota', 15, 2)->default(0);
            $table->decimal('dansos', 15, 2)->default(20000);
            $table->decimal('lainnya_1', 15, 2)->default(0);
            $table->decimal('lainnya_2', 15, 2)->default(0);
            $table->decimal('denda_fingerprint', 15, 2)->default(0);
            
            // MUARA AKHIR
            $table->decimal('jumlah_potongan', 15, 2)->default(0)->comment('Total seluruh kluster potongan');
            $table->decimal('gaji_bersih', 15, 2)->default(0)->comment('Take Home Pay (spj_netto - jumlah_potongan)');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_deductions');
    }
};
