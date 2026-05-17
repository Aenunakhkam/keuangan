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
        Schema::create('bpjs_configs', function (Blueprint $table) {
            $table->id();
            $table->decimal('umk_reference', 15, 2)->default(2333586);
            $table->decimal('health_school_percent', 5, 2)->default(4.00);
            $table->decimal('health_employee_percent', 5, 2)->default(1.00);
            $table->decimal('naker_school_percent', 5, 2)->default(6.24);
            $table->decimal('naker_employee_percent', 5, 2)->default(3.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bpjs_configs');
    }
};
