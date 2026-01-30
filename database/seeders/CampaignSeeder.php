<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Campaign;
use App\Models\CampaignCategory;
use App\Models\User;
use App\Enums\CampaignStatus;
use App\Enums\CampaignType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CampaignSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::first();
        $categories = CampaignCategory::all();

        if ($categories->isEmpty()) {
            return;
        }

        $campaigns = [
            [
                'title' => 'Bantuan Logistik Korban Banjir Tamantirto',
                'description' => 'Bantuan berupa sembako, pakaian layak pakai, dan obat-obatan untuk warga terdampak banjir.',
                'target_amount' => 50000000,
                'is_featured' => true,
                'is_urgent' => true,
            ],
            [
                'title' => 'Beasiswa Mentari untuk Anak Yatim',
                'description' => 'Program beasiswa pendidikan berkelanjutan bagi anak-anak yatim dan dhuafa di wilayah Tamantirto.',
                'target_amount' => 100000000,
                'is_featured' => true,
                'is_urgent' => false,
            ],
            [
                'title' => 'Pembangunan Sumur Wakaf Masjid Al-Ikhlas',
                'description' => 'Penyediaan sumber air bersih melalui pembangunan sumur bor untuk jamaah dan warga sekitar masjid.',
                'target_amount' => 25000000,
                'is_featured' => false,
                'is_urgent' => false,
            ],
            [
                'title' => 'Aksi Sigap Pangan Lansia Dhuafa',
                'description' => 'Distribusi paket pangan bergizi setiap bulan untuk para lansia yang hidup sebatang kara.',
                'target_amount' => 15000000,
                'is_featured' => true,
                'is_urgent' => true,
            ],
            [
                'title' => 'Renovasi Madrasah Ibtidaiyah Muhammadiyah',
                'description' => 'Perbaikan atap dan ruang kelas yang rusak agar proses belajar mengajar kembali nyaman.',
                'target_amount' => 75000000,
                'is_featured' => false,
                'is_urgent' => false,
            ],
            [
                'title' => 'Program Pemberdayaan Ekonomi UMKM Kreatif',
                'description' => 'Pemberian modal usaha dan pelatihan bagi pelaku UMKM terdampak ekonomi.',
                'target_amount' => 40000000,
                'is_featured' => false,
                'is_urgent' => false,
            ],
        ];

        foreach ($campaigns as $data) {
            Campaign::create(array_merge($data, [
                'category_id' => $categories->random()->id,
                'created_by' => $admin->id,
                'type' => CampaignType::Infaq,
                'slug' => Str::slug($data['title']),
                'short_description' => Str::limit($data['description'], 150),
                'current_amount' => rand(0, (int)$data['target_amount'] / 2),
                'start_date' => now(),
                'end_date' => now()->addDays(rand(30, 90)),
                'status' => CampaignStatus::Active,
                'published_at' => now(),
            ]));
        }
    }
}
