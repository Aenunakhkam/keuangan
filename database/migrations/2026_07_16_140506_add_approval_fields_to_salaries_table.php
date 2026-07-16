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
            $table->enum('approval_status', ['pending', 'submitted', 'approved', 'rejected'])->default('pending')->after('status');
            $table->text('rejection_note')->nullable()->after('approval_status');
            $table->boolean('is_published')->default(false)->after('rejection_note');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            $table->dropColumn(['approval_status', 'rejection_note', 'is_published']);
        });
    }
};
