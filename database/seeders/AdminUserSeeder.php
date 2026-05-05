<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed an admin user and role.
     */
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@tasktrail.test'],
            [
                'firstname' => 'System',
                'surname' => 'Admin',
                'department' => 'Administration',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
            ]
        );

        UserRole::updateOrCreate(
            [
                'user_id' => $admin->id,
                'role' => 'admin',
            ],
            []
        );
    }
}