<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('salary_templates');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse since this is a clean-up migration
    }
};
