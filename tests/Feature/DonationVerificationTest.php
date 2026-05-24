<?php

declare(strict_types=1);

use App\Enums\DonationStatus;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Livewire\Volt\Volt;

beforeEach(function () {
    $this->seed(RolePermissionSeeder::class);
    $this->user = User::factory()->create();
    $this->user->assignRole('admin'); // Assign admin role for testing
    $this->campaign = Campaign::factory()->create();
});

test('admin can verify donation from table', function () {
    $donation = Donation::factory()->create([
        'campaign_id' => $this->campaign->id,
        'status' => DonationStatus::Pending,
        'amount' => 100000,
    ]);

    $this->actingAs($this->user);

    Volt::test('admin.donations.index')
        ->call('openVerifyModal', $donation->id)
        ->assertSet('selectedDonationId', $donation->id)
        ->call('verifyDonation')
        ->assertDispatched('notification');

    $donation->refresh();
    expect($donation->status)->toBe(DonationStatus::Verified);
    expect($donation->verified_by)->toBe($this->user->id);
    expect($donation->verified_at)->not->toBeNull();
});

test('admin can reject donation from table', function () {
    $donation = Donation::factory()->create([
        'campaign_id' => $this->campaign->id,
        'status' => DonationStatus::Pending,
        'amount' => 100000,
    ]);

    $this->actingAs($this->user);

    Volt::test('admin.donations.index')
        ->call('openRejectModal', $donation->id)
        ->assertSet('selectedDonationId', $donation->id)
        ->set('confirmationNotes', 'Bukti transfer tidak jelas')
        ->call('rejectDonation')
        ->assertDispatched('notification');

    $donation->refresh();
    expect($donation->status)->toBe(DonationStatus::Rejected);
    expect($donation->verified_by)->toBe($this->user->id);
    expect($donation->verified_at)->not->toBeNull();
    expect($donation->verification_notes)->toBe('Bukti transfer tidak jelas');
});

test('verification buttons only show for verifiable donations', function () {
    $pendingDonation = Donation::factory()->create([
        'campaign_id' => $this->campaign->id,
        'status' => DonationStatus::Pending,
    ]);

    $verifiedDonation = Donation::factory()->create([
        'campaign_id' => $this->campaign->id,
        'status' => DonationStatus::Verified,
    ]);

    $this->actingAs($this->user);

    $response = $this->get(route('admin.donations.index'));

    $response->assertSee('wire:click="openVerifyModal('.$pendingDonation->id.')"', false);
    $response->assertSee('wire:click="openRejectModal('.$pendingDonation->id.')"', false);

    // Should not show verification buttons for verified donation
    $response->assertDontSee('wire:click="openVerifyModal('.$verifiedDonation->id.')"', false);
    $response->assertDontSee('wire:click="openRejectModal('.$verifiedDonation->id.')"', false);
});

test('rejection requires reason', function () {
    $donation = Donation::factory()->create([
        'campaign_id' => $this->campaign->id,
        'status' => DonationStatus::Pending,
        'amount' => 100000,
    ]);

    $this->actingAs($this->user);

    Volt::test('admin.donations.index')
        ->call('openRejectModal', $donation->id)
        ->assertSet('selectedDonationId', $donation->id)
        ->set('confirmationNotes', '') // Empty reason
        ->call('rejectDonation')
        ->assertHasErrors(['confirmationNotes']);

    $donation->refresh();
    expect($donation->status)->toBe(DonationStatus::Pending); // Should still be pending
});

test('campaign card displays rounded numbers', function () {
    $campaign = Campaign::factory()->create([
        'current_amount' => 1500000, // 1.5M
        'target_amount' => 5000000,  // 5M
    ]);

    $html = view('partials.guest.campaign-card', compact('campaign'))->render();

    expect($html)->toContain('Rp 1.5M');
    expect($html)->toContain('Rp 5M');
});
