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
        Schema::table('salaries', function (Blueprint $table) {
            // Tarif transport per hari (diambil dari settings, disimpan per slip gaji)
            $table->decimal('transport_per_day', 15, 2)->default(0)->after('allowance');
            // Jumlah hari hadir / berangkat dalam bulan berjalan
            $table->unsignedInteger('days_present')->default(0)->after('transport_per_day');
            // transport_allowance (sudah ada) = transport_per_day × days_present (dihitung & disimpan)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            $table->dropColumn(['transport_per_day', 'days_present']);
        });
    }
};
