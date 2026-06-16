<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::all();

        foreach ($branches as $branch) {
            if ($branch->id === 1) {
                $superAdmin = User::where('email', 'admin@lazismu.org')->first();
                if ($superAdmin) {
                    $superAdmin->update([
                        'name' => 'Super Administrator',
                        'password' => Hash::make('SuperAdmin123!'),
                        'role' => 'super_admin',
                    ]);
                }

                $pusatUsers = [
                    ['name' => 'Siti Aminah', 'email' => 'siti@lazismu.org', 'role' => 'editor'],
                    ['name' => 'Ahmad Fauzi', 'email' => 'ahmad@lazismu.org', 'role' => 'editor'],
                    ['name' => 'Rina Wijaya', 'email' => 'rina@lazismu.org', 'role' => 'viewer'],
                ];

                foreach ($pusatUsers as $userData) {
                    User::create([
                        'name' => $userData['name'],
                        'email' => $userData['email'],
                        'password' => Hash::make('password'),
                        'email_verified_at' => now(),
                        'is_active' => true,
                        'branch_id' => 1,
                        'role' => $userData['role'],
                    ]);
                }
            }
        }
    }
}
