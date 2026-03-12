<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     * Admin user harus dibuat langsung via aplikasi atau artisan tinker.
     */
    public function run(): void
    {
        // Create Roles only - no hardcoded credentials
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'bendahara']);
        Role::firstOrCreate(['name' => 'guru']);
        Role::firstOrCreate(['name' => 'siswa']);
    }
}
