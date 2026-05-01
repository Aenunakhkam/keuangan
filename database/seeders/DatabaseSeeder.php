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
        // Create Roles only - no hardcoded credentials initially, but we add default admin for fresh install
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'bendahara']);
        Role::firstOrCreate(['name' => 'guru']);
        Role::firstOrCreate(['name' => 'siswa']);

        // Create Default Admin User
        $adminUser = \App\Models\User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrator',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
            ]
        );
        $adminUser->assignRole($adminRole);
    }
}
