<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\CampaignCategory;
use App\Models\User;
use App\Enums\CampaignStatus;
use App\Enums\CampaignType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CampaignFactory extends Factory
{
    protected $model = Campaign::class;

    public function definition(): array
    {
        $title = $this->faker->sentence();

        return [
            'category_id' => CampaignCategory::factory(),
            'created_by' => User::factory(),
            'type' => $this->faker->randomElement(CampaignType::cases()),
            'title' => $title,
            'slug' => Str::slug($title),
            'short_description' => $this->faker->paragraph(),
            'description' => $this->faker->realText(500),
            'target_amount' => $this->faker->numberBetween(1000000, 50000000),
            'current_amount' => 0,
            'start_date' => now(),
            'end_date' => now()->addMonths(2),
            'status' => CampaignStatus::Active,
            'is_featured' => $this->faker->boolean(20),
            'is_urgent' => $this->faker->boolean(10),
            'published_at' => now(),
        ];
    }
}
