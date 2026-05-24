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
        // Ambil semua masjid
        $masjids = \App\Models\Masjid::all();

        foreach ($masjids as $masjid) {
            // Admin default untuk setiap masjid sudah otomatis dibuat oleh event 'created' pada model Masjid.
            // Di sini kita hanya perlu memproses tambahan khusus untuk Pusat (ID 1).

            // Jika ini pusat, kita tambahkan super admin dan beberapa user tambahan
            if ($masjid->id === 1) {
                // Update the auto-generated admin@lazismu.org to be super_admin
                $superAdmin = User::where('email', 'admin@lazismu.org')->first();
                if ($superAdmin) {
                    $superAdmin->update([
                        'name' => 'Super Administrator',
                        'password' => Hash::make('SuperAdmin123!'),
                    ]);
                    $superAdmin->removeRole('admin');
                    $superAdmin->assignRole('super_admin');
                }

                $pusatUsers = [
                    ['name' => 'Siti Aminah', 'email' => 'siti@lazismu.org', 'role' => 'editor'],
                    ['name' => 'Ahmad Fauzi', 'email' => 'ahmad@lazismu.org', 'role' => 'editor'],
                    ['name' => 'Rina Wijaya', 'email' => 'rina@lazismu.org', 'role' => 'viewer'],
                ];

                foreach ($pusatUsers as $userData) {
                    $u = User::create([
                        'name' => $userData['name'],
                        'email' => $userData['email'],
                        'password' => Hash::make('password'),
                        'email_verified_at' => now(),
                        'is_active' => true,
                        'masjid_id' => 1,
                    ]);
                    $u->assignRole($userData['role']);
                }
            }
        }
    }
}
