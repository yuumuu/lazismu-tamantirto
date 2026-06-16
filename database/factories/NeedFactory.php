<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Need;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NeedFactory extends Factory
{
    protected $model = Need::class;

    public function definition(): array
    {
        return [
            'branch_id' => Branch::factory(),
            'tracking_token' => strtoupper(Str::random(8)),
            'applicant_name' => $this->faker->name(),
            'applicant_phone' => '08'.$this->faker->numerify('##########'),
            'applicant_address' => $this->faker->address(),
            'applicant_email' => $this->faker->optional()->email(),
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->paragraph(3),
            'category' => $this->faker->randomElement(['health', 'education', 'business', 'basic_needs', 'other']),
            'amount_requested' => $this->faker->numberBetween(100000, 10000000),
            'status' => 'pending',
        ];
    }
}
