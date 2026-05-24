<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\Masjid;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonationReproductionTest extends TestCase
{
    use RefreshDatabase;

    public function test_donation_creation_includes_transaction_id(): void
    {
        $masjid = Masjid::factory()->create();
        $campaign = Campaign::factory()->create(['masjid_id' => $masjid->id]);

        $donation = new Donation([
            'transaction_id' => Donation::generateTransactionId(),
            'campaign_id' => $campaign->id,
            'donor_name' => 'Haidar',
            'donor_email' => 'haidar@mail.com',
            'donor_phone' => '012345678901',
            'amount' => 1000000,
            'donation_type' => 'infaq',
            'payment_method' => 'qris',
            'donor_message' => 'semoga rezeki nya dilipatgandakan',
            'is_anonymous' => false,
            'status' => \App\Enums\DonationStatus::Pending,
        ]);

        $donation->masjid_id = $masjid->id;
        $donation->save();

        $this->assertDatabaseHas('donations', [
            'id' => $donation->id,
            'donor_name' => 'Haidar',
        ]);

        $this->assertNotNull($donation->transaction_id);
        $this->assertNotEmpty($donation->transaction_id);
    }
}
