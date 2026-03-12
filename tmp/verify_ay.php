<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\AcademicYear;

try {
    $ay = AcademicYear::create([
        'year_name' => '2025/2026',
        'semester' => 1,
        'is_active' => true
    ]);
    echo "SUCCESS: Created Academic Year ID " . $ay->id . "\n";
    $ay->delete();
    echo "SUCCESS: Deleted test record.\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
