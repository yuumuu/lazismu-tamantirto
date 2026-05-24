<?php

namespace Database\Seeders;

use App\Models\Banner;
use Illuminate\Database\Seeder;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Banner::create([
            'title' => 'Zakat Anda, Harapan Mereka',
            'subtitle' => 'Salurkan zakat, infaq dan sedekah Anda melalui Lazismu Tamantirto untuk membantu sesama yang membutuhkan.',
            'button_text' => 'Donasi Sekarang',
            'button_link' => route('guest.donate.form'),
            'image_path' => 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?q=80&w=2070&auto=format&fit=crop',
            'order' => 1,
            'is_active' => true,
        ]);

        Banner::create([
            'title' => 'Sedekah Jariyah untuk Dakwah',
            'subtitle' => 'Mari bergotong royong membangun umat melalui program-program pemberdayaan ekonomi dan pendidikan.',
            'button_text' => 'Lihat Program',
            'button_link' => route('guest.campaigns.index'),
            'image_path' => 'https://images.unsplash.com/photo-1532629345422-7515f3d16bb6?q=80&w=2070&auto=format&fit=crop',
            'order' => 2,
            'is_active' => true,
        ]);
    }
}
