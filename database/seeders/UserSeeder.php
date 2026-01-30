<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@lazismu.org',
                'role' => 'admin',
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'siti@lazismu.org',
                'role' => 'editor',
            ],
            [
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad@lazismu.org',
                'role' => 'editor',
            ],
            [
                'name' => 'Rina Wijaya',
                'email' => 'rina@lazismu.org',
                'role' => 'viewer',
            ],
            [
                'name' => 'Doni Kusuma',
                'email' => 'doni@lazismu.org',
                'role' => 'viewer',
            ],
            [
                'name' => 'Operator 1',
                'email' => 'op1@lazismu.org',
                'role' => 'viewer',
            ],
        ];

        foreach ($users as $userData) {
            $user = User::create([
                'name' => $userData['name'],
                'email' => $userData['email'],
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]);

            $user->assignRole($userData['role']);
        }
    }
}
