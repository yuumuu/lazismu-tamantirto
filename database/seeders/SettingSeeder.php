<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key' => 'site_name',
                'value' => 'LazisMU',
                'type' => 'text',
                'group_name' => 'general',
                'label' => 'Nama Situs',
                'description' => 'Nama website yang ditampilkan di header dan title.',
                'is_public' => true,
                'branch_id' => 1,
            ],
            [
                'key' => 'site_tagline',
                'value' => 'PCM Kasihan',
                'type' => 'text',
                'group_name' => 'general',
                'label' => 'Tagline',
                'description' => 'Tagline atau slogan website.',
                'is_public' => true,
                'branch_id' => 1,
            ],
            [
                'key' => 'site_description',
                'value' => 'LazisMU Tamantirto adalah lembaga amil zakat yang amanah dan profesional dalam mengelola dan menyalurkan dana zakat, infaq, dan sedekah.',
                'type' => 'text',
                'group_name' => 'general',
                'label' => 'Deskripsi Situs',
                'description' => 'Deskripsi singkat website untuk SEO.',
                'is_public' => true,
                'branch_id' => 1,
            ],
            [
                'key' => 'site_logo',
                'value' => null,
                'type' => 'image',
                'group_name' => 'general',
                'label' => 'Logo',
                'description' => 'Logo utama website.',
                'is_public' => true,
                'branch_id' => 1,
            ],
            [
                'key' => 'site_favicon',
                'value' => null,
                'type' => 'image',
                'group_name' => 'general',
                'label' => 'Favicon',
                'description' => 'Ikon kecil yang muncul di tab browser.',
                'is_public' => true,
                'branch_id' => 1,
            ],

            // Contact Settings
            [
                'key' => 'contact_address',
                'value' => 'Jl. Tamantirto No. 123, Kasihan, Bantul, Yogyakarta 55183',
                'type' => 'text',
                'group_name' => 'contact',
                'label' => 'Alamat',
                'description' => 'Alamat lengkap kantor.',
                'is_public' => true,
                'branch_id' => 1,
            ],
            [
                'key' => 'contact_phone',
                'value' => '0274-123456',
                'type' => 'text',
                'group_name' => 'contact',
                'label' => 'Telepon',
                'description' => 'Nomor telepon kantor.',
                'is_public' => true,
                'branch_id' => 1,
            ],
            [
                'key' => 'contact_email',
                'value' => 'info@lazismu-tamantirto.org',
                'type' => 'text',
                'group_name' => 'contact',
                'label' => 'Email',
                'description' => 'Email utama untuk kontak.',
                'is_public' => true,
                'branch_id' => 1,
            ],
            [
                'key' => 'contact_whatsapp',
                'value' => '6281234567890',
                'type' => 'text',
                'group_name' => 'contact',
                'label' => 'WhatsApp',
                'description' => 'Nomor WhatsApp untuk komunikasi.',
                'is_public' => true,
                'branch_id' => 1,
            ],
            [
                'key' => 'contact_maps_url',
                'value' => 'https://maps.google.com/?q=LazisMU+Tamantirto',
                'type' => 'text',
                'group_name' => 'contact',
                'label' => 'Google Maps URL',
                'description' => 'Link ke lokasi di Google Maps.',
                'is_public' => true,
                'branch_id' => 1,
            ],

            // Social Media Settings
            [
                'key' => 'social_facebook',
                'value' => 'https://facebook.com/lazismutamantirto',
                'type' => 'text',
                'group_name' => 'social',
                'label' => 'Facebook',
                'description' => 'URL halaman Facebook.',
                'is_public' => true,
                'branch_id' => 1,
            ],
            [
                'key' => 'social_instagram',
                'value' => 'https://instagram.com/lazismutamantirto',
                'type' => 'text',
                'group_name' => 'social',
                'label' => 'Instagram',
                'description' => 'URL profil Instagram.',
                'is_public' => true,
                'branch_id' => 1,
            ],
            [
                'key' => 'social_youtube',
                'value' => 'https://youtube.com/@lazismutamantirto',
                'type' => 'text',
                'group_name' => 'social',
                'label' => 'YouTube',
                'description' => 'URL channel YouTube.',
                'is_public' => true,
                'branch_id' => 1,
            ],
            [
                'key' => 'social_twitter',
                'value' => 'https://twitter.com/lazismutamantirto',
                'type' => 'text',
                'group_name' => 'social',
                'label' => 'Twitter/X',
                'description' => 'URL profil Twitter/X.',
                'is_public' => true,
                'branch_id' => 1,
            ],

            // Donation Settings
            [
                'key' => 'bank_accounts',
                'value' => json_encode([
                    [
                        'bank_name' => 'Bank Syariah Indonesia',
                        'account_name' => 'LazisMU Tamantirto',
                        'account_number' => '7123456789',
                    ],
                    [
                        'bank_name' => 'Bank Muamalat',
                        'account_name' => 'LazisMU Tamantirto',
                        'account_number' => '3210987654',
                    ],
                ]),
                'type' => 'json',
                'group_name' => 'donation',
                'label' => 'Rekening Bank',
                'description' => 'Daftar rekening bank untuk transfer donasi.',
                'is_public' => true,
                'branch_id' => 1,
            ],
            [
                'key' => 'site_qris',
                'value' => null,
                'type' => 'image',
                'group_name' => 'donation',
                'label' => 'QRIS Image',
                'description' => 'Gambar QRIS untuk pembayaran.',
                'is_public' => true,
                'branch_id' => 1,
            ],
            [
                'key' => 'zakat_gold_nisab',
                'value' => '85',
                'type' => 'number',
                'group_name' => 'zakat',
                'label' => 'Nisab Emas (Gram)',
                'description' => 'Standar nisab emas untuk kalkulator zakat.',
                'is_public' => true,
                'branch_id' => 1,
            ],
            [
                'key' => 'zakat_gold_price',
                'value' => '1200000',
                'type' => 'number',
                'group_name' => 'zakat',
                'label' => 'Harga Emas Saat Ini',
                'description' => 'Harga emas per gram untuk perhitungan zakat.',
                'is_public' => true,
                'branch_id' => 1,
            ],
            [
                'key' => 'zakat_silver_nisab',
                'value' => '595',
                'type' => 'number',
                'group_name' => 'zakat',
                'label' => 'Nisab Perak (Gram)',
                'description' => 'Standar nisab perak untuk kalkulator zakat.',
                'is_public' => true,
                'branch_id' => 1,
            ],
            [
                'key' => 'min_donation',
                'value' => '10000',
                'type' => 'number',
                'group_name' => 'donation',
                'label' => 'Donasi Minimum',
                'description' => 'Jumlah minimum donasi dalam Rupiah.',
                'is_public' => true,
                'branch_id' => 1,
            ],
            [
                'key' => 'auto_verify_threshold',
                'value' => '1000000',
                'type' => 'number',
                'group_name' => 'donation',
                'label' => 'Batas Auto Verifikasi',
                'description' => 'Donasi di atas jumlah ini memerlukan verifikasi manual.',
                'is_public' => false,
                'branch_id' => 1,
            ],

            // Appearance Settings
            [
                'key' => 'primary_color',
                'value' => '#ea580c',
                'type' => 'text',
                'group_name' => 'appearance',
                'label' => 'Warna Utama',
                'description' => 'Warna utama website (hex code).',
                'is_public' => true,
                'branch_id' => 1,
            ],
            [
                'key' => 'footer_text',
                'value' => '© 2025 LazisMU Tamantirto. Semua hak dilindungi.',
                'type' => 'text',
                'group_name' => 'appearance',
                'label' => 'Teks Footer',
                'description' => 'Teks copyright di bagian footer.',
                'is_public' => true,
                'branch_id' => 1,
            ],

            // Security Settings
            [
                'key' => 'developer_pin',
                'value' => Hash::make('LazismuDev2026@Haidar'),
                'type' => 'text',
                'group_name' => 'security',
                'label' => 'Developer PIN',
                'description' => 'PIN untuk mengakses pengaturan sensitif. Default: LazismuDev2026@Haidar. Hubungi developer untuk perubahan.',
                'is_public' => false,
                'branch_id' => 1,
            ],
            [
                'key' => 'developer_contact',
                'value' => 'Haidar Yahya Mudhofar',
                'type' => 'text',
                'group_name' => 'security',
                'label' => 'Kontak Developer',
                'description' => 'Informasi kontak pengembang sistem.',
                'is_public' => false,
                'branch_id' => 1,
            ],

            // Integration Settings
            [
                'key' => 'fonnte_api_key',
                'value' => '',
                'type' => 'text',
                'group_name' => 'integration',
                'label' => 'Fonnte API Key',
                'description' => 'API Key dari Fonnte untuk notifikasi WhatsApp.',
                'is_public' => false,
                'branch_id' => 1,
            ],
            [
                'key' => 'fonnte_phone',
                'value' => '',
                'type' => 'text',
                'group_name' => 'integration',
                'label' => 'Nomor Pengirim Fonnte',
                'description' => 'Nomor WhatsApp terdaftar di Fonnte.',
                'is_public' => false,
                'branch_id' => 1,
            ],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['key' => $setting['key'], 'branch_id' => $setting['branch_id']],
                $setting
            );
        }

        $this->command->info('Created '.count($settings).' settings.');
    }
}
