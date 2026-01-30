<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\CampaignCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CampaignCategoryFactory extends Factory
{
    protected $model = CampaignCategory::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name),
            'icon' => 'folder',
            'color' => $this->faker->hexColor(),
            'description' => $this->faker->sentence(),
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
