<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\OrganizationStructure;
use Illuminate\Database\Seeder;

class OrganizationStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Level 1: Pimpinan
        $ketuaUmum = OrganizationStructure::firstOrCreate(
            ['name' => 'Ketua Umum', 'level' => 1],
            [
                'name' => 'Ketua Umum',
                'level' => 1,
                'description' => 'Pimpinan tertinggi LazisMU Tamantirto',
                'responsibilities' => 'Memimpin dan mengkoordinasikan seluruh kegiatan lembaga',
                'sort_order' => 0,
                'is_active' => true,
            ]
        );

        // Level 2: Board
        $wakilKetua = OrganizationStructure::firstOrCreate(
            ['name' => 'Wakil Ketua', 'level' => 2],
            [
                'name' => 'Wakil Ketua',
                'level' => 2,
                'parent_id' => $ketuaUmum->id,
                'description' => 'Wakil pimpinan yang membantu tugas Ketua Umum',
                'responsibilities' => 'Membantu Ketua Umum dalam menjalankan tugas kepemimpinan',
                'sort_order' => 0,
                'is_active' => true,
            ]
        );

        $sekretaris = OrganizationStructure::firstOrCreate(
            ['name' => 'Sekretaris Umum', 'level' => 2],
            [
                'name' => 'Sekretaris Umum',
                'level' => 2,
                'parent_id' => $ketuaUmum->id,
                'description' => 'Pengelola administrasi dan kesekretariatan',
                'responsibilities' => 'Mengelola administrasi, dokumentasi, dan korespondensi lembaga',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $bendahara = OrganizationStructure::firstOrCreate(
            ['name' => 'Bendahara Umum', 'level' => 2],
            [
                'name' => 'Bendahara Umum',
                'level' => 2,
                'parent_id' => $ketuaUmum->id,
                'description' => 'Pengelola keuangan lembaga',
                'responsibilities' => 'Mengelola keuangan, pembukuan, dan pelaporan keuangan lembaga',
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        // Level 3: Divisi
        $divisions = [
            [
                'name' => 'Divisi Program',
                'description' => 'Pengelola program dan kegiatan',
                'responsibilities' => 'Merencanakan, melaksanakan, dan mengevaluasi program lembaga',
            ],
            [
                'name' => 'Divisi Fundraising',
                'description' => 'Penggalangan dana',
                'responsibilities' => 'Menggalang dana melalui berbagai channel dan kampanye',
            ],
            [
                'name' => 'Divisi Pendidikan',
                'description' => 'Program pendidikan dan beasiswa',
                'responsibilities' => 'Mengelola program beasiswa dan bantuan pendidikan',
            ],
            [
                'name' => 'Divisi Kesehatan',
                'description' => 'Program kesehatan masyarakat',
                'responsibilities' => 'Mengelola program bantuan kesehatan dan pengobatan',
            ],
            [
                'name' => 'Divisi Ekonomi',
                'description' => 'Pemberdayaan ekonomi umat',
                'responsibilities' => 'Mengelola program pemberdayaan ekonomi dan usaha mikro',
            ],
            [
                'name' => 'Divisi Humas & Media',
                'description' => 'Hubungan masyarakat dan publikasi',
                'responsibilities' => 'Mengelola komunikasi publik, media sosial, dan publikasi',
            ],
        ];

        foreach ($divisions as $index => $division) {
            OrganizationStructure::firstOrCreate(
                ['name' => $division['name'], 'level' => 3],
                [
                    'name' => $division['name'],
                    'level' => 3,
                    'parent_id' => $wakilKetua->id,
                    'description' => $division['description'],
                    'responsibilities' => $division['responsibilities'],
                    'sort_order' => $index,
                    'is_active' => true,
                ]
            );
        }

        $this->command->info('Created organization structure with '.OrganizationStructure::count().' positions.');
    }
}
