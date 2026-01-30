<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CampaignCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CampaignCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name'          => 'Pendidikan',
                'icon'          => 'graduation-cap',
                'color'         => '#3b82f6',
                'description'   => 'Program beasiswa dan bantuan pendidikan untuk anak-anak kurang mampu.',
            ],
            [
                'name'          => 'Kesehatan',
                'icon'          => 'heart-pulse',
                'color'         => '#ef4444',
                'description'   => 'Bantuan kesehatan dan pengobatan untuk masyarakat yang membutuhkan.',
            ],
            [
                'name'          => 'Masjid',
                'icon'          => 'building-2',
                'color'         => '#22c55e',
                'description'   => 'Pembangunan dan renovasi masjid serta sarana ibadah.',
            ],
            [
                'name'          => 'Bencana',
                'icon'          => 'alert-triangle',
                'color'         => '#f59e0b',
                'description'   => 'Tanggap darurat dan pemulihan pasca bencana alam.',
            ],
            [
                'name'          => 'Pangan',
                'icon'          => 'utensils',
                'color'         => '#8b5cf6',
                'description'   => 'Program ketahanan pangan dan bantuan sembako.',
            ],
            [
                'name'          => 'Sosial',
                'icon'          => 'users',
                'color'         => '#ea580c',
                'description'   => 'Program pemberdayaan sosial dan ekonomi masyarakat.',
            ],
        ];

        foreach ($categories as $index => $category) {
            CampaignCategory::firstOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name'          => $category['name'],
                    'slug'          => Str::slug($category['name']),
                    'icon'          => $category['icon'],
                    'color'         => $category['color'],
                    'description'   => $category['description'],
                    'is_active'     => true,
                    'sort_order'    => $index,
                ]
            );
        }

        $this->command->info('Created '.count($categories).' campaign categories.');
    }
}
