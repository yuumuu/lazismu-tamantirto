<?php

namespace Database\Factories;

use App\Models\Masjid;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class MasjidFactory extends Factory
{
    protected $model = Masjid::class;

    public function definition(): array
    {
        $name = $this->faker->company.' Masjid';

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'address' => $this->faker->address,
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->unique()->safeEmail,
            'is_active' => true,
        ];
    }
}
