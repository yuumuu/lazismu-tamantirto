<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            UserSeeder::class,
            CampaignCategorySeeder::class,
            CampaignSeeder::class,
            BlogCategorySeeder::class,
            BlogPostSeeder::class,
            SettingSeeder::class,
            MenuSeeder::class,
            OrganizationStructureSeeder::class,
            BannerSeeder::class,
            StakeholderSeeder::class,
        ]);
    }
}
