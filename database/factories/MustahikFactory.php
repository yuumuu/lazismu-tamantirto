<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\AsnafType;
use App\Models\Mustahik;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Mustahik>
 */
class MustahikFactory extends Factory
{
    protected $model = Mustahik::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'asnaf_type' => $this->faker->randomElement(AsnafType::cases()),
            'notes' => $this->faker->optional()->paragraph(),
            'is_active' => true,
        ];
    }

    /**
     * Indicate that the mustahik is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
