<?php

namespace Database\Seeders;

use App\Enums\AsnafType;
use App\Models\Distributor;
use App\Models\Mustahik;
use App\Models\Muzakki;
use App\Models\User;
use Illuminate\Database\Seeder;

class StakeholderSeeder extends Seeder
{
    public function run(): void
    {
        // Muzakki
        $users = User::where('email', '!=', 'admin@lazismu.org')->limit(10)->get();

        foreach ($users as $user) {
            Muzakki::create([
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'phone' => '08'.rand(100000000, 999999999),
                'address' => 'Jl. Tamantirto No. '.rand(1, 100),
                'nik' => '3404'.rand(100000000000, 999999999999),
                'type' => 'individu',
                'is_active' => true,
            ]);
        }

        // Mustahik
        $asnafs = [AsnafType::Fakir, AsnafType::Miskin, AsnafType::Fisabilillah, AsnafType::Gharim];
        for ($i = 0; $i < 15; $i++) {
            Mustahik::create([
                'name' => 'Mustahik '.($i + 1),
                'address' => 'Dusun '.(rand(1, 5)).', Tamantirto',
                'phone' => '0812'.rand(10000000, 99999999),
                'asnaf_type' => $asnafs[array_rand($asnafs)],
                'nik' => '3404'.rand(100000000000, 999999999999),
                'family_card_number' => '3404'.rand(100000000000, 999999999999),
                'occupation' => 'Buruh',
                'income_range' => '< 1.000.000',
                'is_active' => true,
            ]);
        }

        // Distributor
        $types = ['volunteer', 'staff', 'partner'];
        for ($i = 0; $i < 5; $i++) {
            Distributor::create([
                'name' => 'Distributor '.($i + 1),
                'phone' => '0877'.rand(10000000, 99999999),
                'address' => 'Kantor LazisMU Unit '.($i + 1),
                'type' => $types[array_rand($types)],
                'is_active' => true,
            ]);
        }
    }
}
