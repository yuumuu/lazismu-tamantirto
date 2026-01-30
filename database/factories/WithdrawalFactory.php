<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Withdrawal;
use App\Models\Campaign;
use App\Models\Mustahik;
use App\Models\Distributor;
use App\Models\User;
use App\Enums\WithdrawalStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Withdrawal>
 */
class WithdrawalFactory extends Factory
{
    protected $model = Withdrawal::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'campaign_id' => null, // Can be null for general withdrawals
            'mustahik_id' => null, // Can be null for general distributions
            'distributor_id' => null, // Can be null for internal distributions
            'amount' => $this->faker->numberBetween(50000, 5000000),
            'date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'description' => $this->faker->paragraph(2),
            'status' => $this->faker->randomElement(WithdrawalStatus::cases()),
            'proof_image' => null,
            'created_by' => User::factory(),
            'verified_by' => null,
        ];
    }

    /**
     * Indicate that the withdrawal is in draft status.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => WithdrawalStatus::Draft,
            'verified_by' => null,
        ]);
    }

    /**
     * Indicate that the withdrawal is verified.
     */
    public function verified(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => WithdrawalStatus::Verified,
            'verified_by' => User::factory(),
        ]);
    }

    /**
     * Indicate that the withdrawal is sent/completed.
     */
    public function sent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => WithdrawalStatus::Sent,
            'verified_by' => User::factory(),
            'proof_image' => 'withdrawals/proof-' . $this->faker->uuid() . '.jpg',
        ]);
    }

    /**
     * Indicate that the withdrawal has a campaign.
     */
    public function withCampaign(): static
    {
        return $this->state(fn (array $attributes) => [
            'campaign_id' => Campaign::factory(),
        ]);
    }

    /**
     * Indicate that the withdrawal has a mustahik.
     */
    public function withMustahik(): static
    {
        return $this->state(fn (array $attributes) => [
            'mustahik_id' => Mustahik::factory(),
        ]);
    }

    /**
     * Indicate that the withdrawal has a distributor.
     */
    public function withDistributor(): static
    {
        return $this->state(fn (array $attributes) => [
            'distributor_id' => Distributor::factory(),
        ]);
    }
}
