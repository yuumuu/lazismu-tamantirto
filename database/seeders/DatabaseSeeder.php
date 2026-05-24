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
        // Set the active masjid id so that the BelongsToMasjid trait
        // automatically assigns it to all seeded models.
        session(['active_masjid_id' => 1]);

        $this->call([
            RolePermissionSeeder::class,
            MasjidSeeder::class,
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
