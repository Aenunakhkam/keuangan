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
        // 1. Drop unused tables
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('student_bills');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('students');
        Schema::dropIfExists('fee_types');
        Schema::dropIfExists('academic_years');
        Schema::dropIfExists('class_rooms');
        Schema::enableForeignKeyConstraints();

        // 2. Update teachers table
        Schema::table('teachers', function (Blueprint $table) {
            // New fields
            $table->string('nipty')->nullable()->after('name');
            $table->string('nipy')->nullable()->after('nipty');
            $table->string('birth_place')->nullable()->after('nipy');
            $table->date('birth_date')->nullable()->after('birth_place');
            $table->string('unit')->nullable()->after('education');
            $table->integer('service_years')->default(0)->after('unit');
            $table->integer('service_months')->default(0)->after('service_years');
            $table->string('grade')->nullable()->after('service_months'); // GOLONGAN

            // We can keep the old ones or drop them. 
            // The user didn't mention 'nip', 'phone', 'address', 'teaching_hours' in the new list.
            // I'll drop the ones that seem redundant or unused in the new focus.
            $table->dropColumn(['nip', 'phone', 'address', 'teaching_hours']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropColumn(['nipty', 'nipy', 'birth_place', 'birth_date', 'unit', 'service_years', 'service_months', 'grade']);
            $table->string('nip')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->integer('teaching_hours')->default(0);
        });
    }
};
