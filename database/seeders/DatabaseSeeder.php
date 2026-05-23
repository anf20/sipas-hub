<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Setup Roles
        $this->call(RoleSeeder::class);

        // 2. Create Super Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('Super Admin');

        // 3. Setup Core Infrastructure (Academic Year & Classes)
        $this->call(SchoolFoundationSeeder::class);

        // 4. Setup Fee Types
        $this->call(FeeTypeSeeder::class);

        // 5. Setup Parents and Students (The specific 30 students / 20 parents requirement)
        $this->call(StudentSeeder::class);
    }
}
