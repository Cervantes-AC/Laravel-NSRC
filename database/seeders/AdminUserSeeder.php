<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'full_name' => 'Admin User',
            'email' => 'admin@nsrc.gov.ph',
            'password' => Hash::make('Admin@123'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Member User',
            'full_name' => 'Member User',
            'email' => 'member@nsrc.gov.ph',
            'password' => Hash::make('Member@123'),
            'role' => 'member',
            'status' => 'active',
        ]);

        // Create sample member users
        $members = [
            ['name' => 'Juan Dela Cruz', 'email' => 'juan@nsrc.gov.ph'],
            ['name' => 'Maria Santos', 'email' => 'maria@nsrc.gov.ph'],
            ['name' => 'Pedro Reyes', 'email' => 'pedro@nsrc.gov.ph'],
        ];

        foreach ($members as $member) {
            User::create([
                'name' => $member['name'],
                'full_name' => $member['name'],
                'email' => $member['email'],
                'password' => Hash::make('Member@123'),
                'role' => 'member',
                'status' => 'active',
                'school_id' => 'NSRC-' . str_pad(random_int(1, 9999), 4, '0', STR_PAD_LEFT),
            ]);
        }
    }
}
