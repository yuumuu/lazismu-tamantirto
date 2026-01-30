<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\MenuLocation;
use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Header Menu
        $headerMenus = [
            ['name' => 'header-home',       'label' => 'Beranda',   'url' => '/',           'icon' => 'home'],
            ['name' => 'header-campaigns',  'label' => 'Donasi',    'url' => '/campaigns',  'icon' => 'heart'],
            ['name' => 'header-blog',       'label' => 'Artikel',   'url' => '/blog',       'icon' => 'newspaper'],
            ['name' => 'header-about',      'label' => 'Tentang',   'url' => '/about',      'icon' => 'info'],
            ['name' => 'header-contact',    'label' => 'Kontak',    'url' => '/contact',    'icon' => 'phone'],
        ];

        foreach ($headerMenus as $index => $menu) {
            Menu::firstOrCreate(
                ['name' => $menu['name']],
                [
                    'name' => $menu['name'],
                    'location' => MenuLocation::Header,
                    'label' => $menu['label'],
                    'url' => $menu['url'],
                    'icon' => $menu['icon'],
                    'sort_order' => $index,
                    'is_active' => true,
                ]
            );
        }

        // Footer Menu
        $footerMenus = [
            ['name' => 'footer-privacy',    'label' => 'Kebijakan Privasi',     'url' => '/privacy',     'icon' => 'lock'],
            ['name' => 'footer-terms',      'label' => 'Syarat & Ketentuan',    'url' => '/terms',       'icon' => 'file-text'],
            ['name' => 'footer-faq',        'label' => 'FAQ',                   'url' => '/faq',         'icon' => 'question'],
            ['name' => 'footer-report',     'label' => 'Laporan Keuangan',      'url' => '/report',      'icon' => 'file-chart'],
        ];

        foreach ($footerMenus as $index => $menu) {
            Menu::firstOrCreate(
                ['name' => $menu['name']],
                [
                    'name' => $menu['name'],
                    'location' => MenuLocation::Footer,
                    'label' => $menu['label'],
                    'url' => $menu['url'],
                    'sort_order' => $index,
                    'is_active' => true,
                ]
            );
        }

        // Mobile Menu
        $mobileMenus = [
            ['name' => 'mobile-home',           'label' => 'Beranda',       'url' => '/',               'icon' => 'home'],
            ['name' => 'mobile-campaigns',      'label' => 'Donasi',        'url' => '/campaigns',      'icon' => 'heart'],
            ['name' => 'mobile-calculator',     'label' => 'Kalkulator',    'url' => '/calculator',     'icon' => 'calculator'],
            ['name' => 'mobile-blog',           'label' => 'Artikel',       'url' => '/blog',           'icon' => 'newspaper'],
            ['name' => 'mobile-profile',        'label' => 'Profil',        'url' => '/about',          'icon' => 'user'],
        ];

        foreach ($mobileMenus as $index => $menu) {
            Menu::firstOrCreate(
                ['name' => $menu['name']],
                [
                    'name' => $menu['name'],
                    'location' => MenuLocation::Mobile,
                    'label' => $menu['label'],
                    'url' => $menu['url'],
                    'icon' => $menu['icon'],
                    'sort_order' => $index,
                    'is_active' => true,
                ]
            );
        }

        $total = count($headerMenus) + count($footerMenus) + count($mobileMenus);
        $this->command->info("Created {$total} menu items.");
    }
}
