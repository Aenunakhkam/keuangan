<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

if (!Schema::hasColumn('academic_years', 'semester')) {
    Schema::table('academic_years', function (Blueprint $table) {
        $table->tinyInteger('semester')->unsigned()->default(1)->after('year_name');
    });
    echo "Semester column added successfully.\n";
} else {
    echo "Semester column already exists.\n";
}
