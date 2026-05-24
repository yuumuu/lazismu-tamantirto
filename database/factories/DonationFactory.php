<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\CampaignType;
use App\Enums\DonationStatus;
use App\Enums\PaymentMethod;
use App\Models\Campaign;
use App\Models\Donation;
use Illuminate\Database\Eloquent\Factories\Factory;

class DonationFactory extends Factory
{
    protected $model = Donation::class;

    public function definition(): array
    {
        return [
            'campaign_id' => Campaign::factory(),
            'transaction_id' => Donation::generateTransactionId(),
            'donor_name' => $this->faker->name(),
            'donor_email' => $this->faker->safeEmail(),
            'donor_phone' => $this->faker->phoneNumber(),
            'amount' => $this->faker->numberBetween(10000, 1000000),
            'donation_type' => CampaignType::Infaq,
            'payment_method' => PaymentMethod::BankTransfer,
            'status' => DonationStatus::Pending,
            'proof_image' => 'donations/proofs/test_proof.jpg',
            'is_anonymous' => false,
        ];
    }
}
