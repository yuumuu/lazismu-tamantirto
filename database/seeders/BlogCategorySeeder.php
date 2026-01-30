<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\BlogCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name'          => 'Berita Terkini',
                'color'         => '#3b82f6',
                'description'   => 'Berita terbaru seputar kegiatan LazisMU Tamantirto.',
            ],
            [
                'name'          => 'Kegiatan & Event',
                'color'         => '#22c55e',
                'description'   => 'Dokumentasi kegiatan dan event yang telah dilaksanakan.',
            ],
            [
                'name'          => 'Inspirasi',
                'color'         => '#f59e0b',
                'description'   => 'Kisah inspiratif dari para penerima manfaat.',
            ],
            [
                'name'          => 'Laporan Program',
                'color'         => '#8b5cf6',
                'description'   => 'Laporan pertanggungjawaban program dan penggunaan dana.',
            ],
            [
                'name'          => 'Pengumuman',
                'color'         => '#ef4444',
                'description'   => 'Pengumuman resmi dari LazisMU Tamantirto.',
            ],
            [
                'name'          => 'Tips & Tutorial',
                'color'         => '#06b6d4',
                'description'   => 'Tips dan panduan seputar zakat, infaq, dan sedekah.',
            ],
        ];

        foreach ($categories as $index => $category) {
            BlogCategory::firstOrCreate(
                ['slug' => Str::slug($category['name'])],
                [
                    'name'          => $category['name'],
                    'slug'          => Str::slug($category['name']),
                    'color'         => $category['color'],
                    'description'   => $category['description'],
                    'is_active'     => true,
                    'sort_order'    => $index,
                ]
            );
        }

        $this->command->info('Created '.count($categories).' blog categories.');
    }
}
