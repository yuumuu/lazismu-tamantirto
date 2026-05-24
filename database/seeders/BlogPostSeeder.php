<?php

namespace Database\Seeders;

use App\Enums\PostStatus;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Warta LazisMU', 'slug' => 'warta-lazismu'],
            ['name' => 'Kisah Inspiratif', 'slug' => 'kisah-inspiratif'],
            ['name' => 'Edukasi Zakat', 'slug' => 'edukasi-zakat'],
            ['name' => 'Laporan Program', 'slug' => 'laporan-program'],
        ];

        foreach ($categories as $cat) {
            BlogCategory::updateOrCreate(['slug' => $cat['slug']], $cat);
        }

        $allCats = BlogCategory::all();
        $author = User::where('email', 'admin@lazismu.org')->first() ?? User::first();

        $posts = [
            [
                'title' => 'Penyaluran Zakat Fitrah di Wilayah Tamantirto',
                'excerpt' => 'LazisMU Tamantirto telah menyalurkan zakat fitrah kepada ratusan mustahik...',
                'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Penyaluran dilakukan secara bertahap di berbagai titik di Tamantirto. Kami berkomitmen untuk memastikan amanah donatur sampai kepada yang berhak.',
            ],
            [
                'title' => 'Kisah Haru Bapak Ahmad: Berjuang Melawan Penyakit',
                'excerpt' => 'Bapak Ahmad mendapatkan bantuan pengobatan dari LazisMU untuk biaya rumah sakit...',
                'content' => 'Berkat bantuan para donatur melalui LazisMU, Bapak Ahmad kini bisa menjalani operasi yang selama ini tertunda. Kondisinya kini berangsur membaik dan stabil.',
            ],
            [
                'title' => 'Mengapa Zakat Penting Bagi Kemajuan Ekonomi Umat?',
                'excerpt' => 'Memahami peran strategis zakat dalam memutus rantai kemiskinan di tingkat lokal...',
                'content' => 'Zakat bukan sekadar kewajiban agama, tapi merupakan instrumen ekonomi yang luar biasa. Jika dikelola secara profesional, zakat bisa memberdayakan mustahik menjadi muzakki.',
            ],
            [
                'title' => 'Laporan Transparansi Triwulan IV 2025',
                'excerpt' => 'LazisMU merilis laporan penggunaan dana untuk periode Oktober hingga Desember...',
                'content' => 'Sebagai bentuk pertanggungjawaban kepada publik, kami menyajikan rincian penyaluran dana zakat dan infaq. Transparansi adalah kunci kepercayaan donatur kepada kami.',
            ],
            [
                'title' => 'Pelatihan UMKM Untuk Ibu-Ibu Kreatif',
                'excerpt' => 'Program pemberdayaan ekonomi melalui pelatihan menjahit dan pemasaran digital...',
                'content' => 'Ibu-ibu di lingkungan sekitar diberikan pelatihan intensif selama satu minggu. Diharapkan mereka memiliki keahlian tambahan untuk menopang ekonomi keluarga.',
            ],
        ];

        foreach ($posts as $index => $postData) {
            BlogPost::create([
                'category_id' => $allCats->random()->id,
                'author_id' => $author->id,
                'title' => $postData['title'],
                'slug' => Str::slug($postData['title']),
                'excerpt' => $postData['excerpt'],
                'content' => '<p>'.$postData['content'].'</p>',
                'status' => PostStatus::Published,
                'is_featured' => $index === 0,
                'published_at' => now()->subDays($index * 2),
                'view_count' => rand(100, 1000),
            ]);
        }
    }
}
