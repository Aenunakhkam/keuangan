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
        Schema::table('teachers', function (Blueprint $table) {
            $table->foreignId('position_id')->nullable()->after('user_id')->constrained('positions')->nullOnDelete();
            $table->integer('teaching_hours')->default(0)->after('position_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropForeign(['position_id']);
            $table->dropColumn(['position_id', 'teaching_hours']);
        });
    }
};
