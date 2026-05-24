<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Masjid;
use Illuminate\Database\Seeder;

class MasjidSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pusat (ID 1) - LAZISMU Tamantirto
        // Sebagai pengelola utama yang bisa mengakses data masjid lain
        Masjid::create([
            'name' => 'LAZISMU Tamantirto',
            'slug' => 'pusat',
            'address' => 'Tamantirto, Kasihan, Bantul, DIY',
            'phone' => '081234567890',
            'email' => 'info@lazismutamantirto.org',
            'is_active' => true,
        ]);

        // Beberapa contoh masjid cabang/mitra
        $masjids = [
            [
                'name' => 'Masjid Al-Falah',
                'slug' => 'al-falah',
                'address' => 'Jl. Garuda No. 1, Tamantirto',
                'phone' => '081111111111',
                'email' => 'alfalah@example.com',
                'is_active' => true,
            ],
            [
                'name' => 'Masjid At-Taqwa',
                'slug' => 'at-taqwa',
                'address' => 'Jl. Merpati No. 2, Tamantirto',
                'phone' => '082222222222',
                'email' => 'attaqwa@example.com',
                'is_active' => true,
            ],
        ];

        foreach ($masjids as $masjidData) {
            Masjid::create($masjidData);
        }
    }
}
