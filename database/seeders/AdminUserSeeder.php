<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate([
            'email' => 'admin@gmail.com',
        ], [
            'name' => 'Admin User',
            'full_name' => 'Admin User',
            'password' => Hash::make('Admin@123'),
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $members = [
            [
                'name' => 'Gabriel Francis Araneta',
                'full_name' => 'Gabriel Francis Araneta',
                'email' => 'gabriel.araneta@example.com',
                'school_id' => '2026-0001',
                'nsrc_serial_number' => 'NSRC-2026-0001',
                'college' => 'College of Information Sciences and Computing',
                'major' => 'BSIT',
                'year_level' => '3A',
                'primary_competency' => 'Web Software Tools',
                'gender' => 'Male',
                'status' => 'active',
            ],
            [
                'name' => 'Rio Pinatacan',
                'full_name' => 'Rio Pinatacan',
                'email' => 'rio.pinatacan@example.com',
                'school_id' => '2026-0002',
                'nsrc_serial_number' => 'NSRC-2026-0002',
                'college' => 'College of Information Sciences and Computing',
                'major' => 'BSIT',
                'year_level' => '3B',
                'primary_competency' => 'Network Administration',
                'gender' => 'Male',
                'status' => 'active',
            ],
            [
                'name' => 'Aaron Clyde Cervantes',
                'full_name' => 'Aaron Clyde Cervantes',
                'email' => 'aaron.cervantes@example.com',
                'school_id' => '2026-0003',
                'nsrc_serial_number' => 'NSRC-2026-0003',
                'college' => 'College of Information Sciences and Computing',
                'major' => 'BSIT',
                'year_level' => '3C',
                'primary_competency' => 'Database Management',
                'gender' => 'Male',
                'status' => 'active',
            ],
        ];

        $password = Hash::make('Member@123');

        foreach ($members as $member) {
            User::updateOrCreate(
                ['email' => $member['email']],
                array_merge($member, [
                    'password' => $password,
                    'role' => 'member',
                    'email_verified_at' => now(),
                ])
            );
        }
    }
}
