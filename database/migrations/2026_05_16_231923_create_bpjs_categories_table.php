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
        Schema::create('bpjs_categories', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // A, B, C, D, E
            $table->string('name');
            $table->boolean('has_health')->default(false);
            $table->boolean('has_naker')->default(false);
            $table->boolean('is_full_school')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bpjs_categories');
    }
};
