<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pusat (ID 1) - LAZISMU Tamantirto
        // Sebagai pengelola utama yang bisa mengakses data branch lain
        Branch::create([
            'name' => 'LAZISMU Tamantirto',
            'slug' => 'pusat',
            'address' => 'Tamantirto, Kasihan, Bantul, DIY',
            'phone' => '081234567890',
            'email' => 'info@lazismutamantirto.org',
            'is_active' => true,
        ]);

        // Beberapa contoh branch cabang/mitra
        $branches = [
            [
                'name' => 'branch Al-Falah',
                'slug' => 'al-falah',
                'address' => 'Jl. Garuda No. 1, Tamantirto',
                'phone' => '081111111111',
                'email' => 'alfalah@example.com',
                'is_active' => true,
            ],
            [
                'name' => 'branch At-Taqwa',
                'slug' => 'at-taqwa',
                'address' => 'Jl. Merpati No. 2, Tamantirto',
                'phone' => '082222222222',
                'email' => 'attaqwa@example.com',
                'is_active' => true,
            ],
        ];

        foreach ($branches as $branchData) {
            Branch::create($branchData);
        }
    }
}
