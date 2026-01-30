<?php

use App\Models\Donation;
use App\Models\Campaign;
use App\Models\User;
use App\Enums\DonationStatus;
use Livewire\Volt\Volt;

beforeEach(function () {
    $this->user = User::factory()->create();
    // Ensure user has permission if needed, but for now we use auth
    $this->campaign = Campaign::factory()->create();
});

test('can view donation index', function () {
    $this->actingAs($this->user)
        ->get(route('admin.donations.index'))
        ->assertStatus(200)
        ->assertSeeLivewire('admin.donations.index');
});

test('can verify donation', function () {
    $donation = Donation::factory()->create([
        'campaign_id' => $this->campaign->id,
        'status' => DonationStatus::Pending,
        'amount' => 50000,
    ]);

    $this->actingAs($this->user);

    Volt::test('admin.donations.show', ['donation' => $donation])
        ->set('action', 'verify')
        ->set('notes', 'Bukti valid')
        ->call('process')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.donations.index'));

    $donation->refresh();
    expect($donation->status)->toBe(DonationStatus::Verified);
    expect($this->campaign->refresh()->current_amount)->toBe(50000.0);
});

test('can reject donation', function () {
    $donation = Donation::factory()->create([
        'campaign_id' => $this->campaign->id,
        'status' => DonationStatus::Pending,
    ]);

    $this->actingAs($this->user);

    Volt::test('admin.donations.show', ['donation' => $donation])
        ->set('action', 'reject')
        ->set('notes', 'Bukti tidak terbaca')
        ->call('process')
        ->assertHasNoErrors();

    expect($donation->refresh()->status)->toBe(DonationStatus::Rejected);
});

test('rejecting donation requires notes', function () {
    $donation = Donation::factory()->create();

    $this->actingAs($this->user);

    Volt::test('admin.donations.show', ['donation' => $donation])
        ->set('action', 'reject')
        ->set('notes', '')
        ->call('process')
        ->assertHasErrors(['notes' => 'required']);
});
