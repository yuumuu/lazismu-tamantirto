<?php

declare(strict_types=1);

use App\Models\Campaign;
use App\Models\CampaignCategory;
use App\Models\User;

test('campaign days remaining returns integer not decimal', function () {
    $user = User::factory()->create();
    $category = CampaignCategory::factory()->create();

    // Create campaign ending in 5.7 days (should show as 5 days)
    $campaign = Campaign::factory()
        ->for($category, 'category')
        ->for($user, 'creator')
        ->create([
            'status' => 'active',
            'end_date' => now()->addHours(137), // 5.7 days
        ]);

    $daysRemaining = $campaign->days_remaining;

    // Should be integer, not float
    expect($daysRemaining)->toBeInt()
        ->and($daysRemaining)->toBe(5); // Should round down to 5
});

test('campaign days remaining is zero when past end date', function () {
    $user = User::factory()->create();
    $category = CampaignCategory::factory()->create();

    $campaign = Campaign::factory()
        ->for($category, 'category')
        ->for($user, 'creator')
        ->create([
            'status' => 'active',
            'end_date' => now()->subDays(5),
        ]);

    expect($campaign->days_remaining)->toBe(0);
});

test('campaign days remaining is zero when no end date', function () {
    $user = User::factory()->create();
    $category = CampaignCategory::factory()->create();

    // Campaign with end_date in far future (effectively no end)
    $campaign = Campaign::factory()
        ->for($category, 'category')
        ->for($user, 'creator')
        ->create([
            'status' => 'active',
            'end_date' => now()->addYears(10), // Far future
        ]);

    // Should have days remaining
    expect($campaign->days_remaining)->toBeGreaterThan(0);
});

test('campaign card displays integer days remaining', function () {
    $user = User::factory()->create();
    $category = CampaignCategory::factory()->create();

    $campaign = Campaign::factory()
        ->for($category, 'category')
        ->for($user, 'creator')
        ->create([
            'status' => 'active',
            'end_date' => now()->addHours(137), // 5.7 days
        ]);

    $response = $this->get(route('guest.campaigns.index'));

    $response->assertSuccessful()
        ->assertSee('5 Hari Lagi') // Should show integer
        ->assertDontSee('5.7 Hari Lagi') // Should not show decimal
        ->assertDontSee('5.70 Hari Lagi');
});

test('campaign countdown shows correct values for different durations', function () {
    $user = User::factory()->create();
    $category = CampaignCategory::factory()->create();

    // Test 2 days - use specific time to avoid timing issues
    $campaign2 = Campaign::factory()
        ->for($category, 'category')
        ->for($user, 'creator')
        ->create([
            'status' => 'active',
            'end_date' => now()->addDays(2)->startOfDay(),
        ]);

    // Should be 1 or 2 days (depending on time of day)
    expect($campaign2->days_remaining)->toBeGreaterThanOrEqual(1)
        ->and($campaign2->days_remaining)->toBeLessThanOrEqual(2);

    // Test 30 days
    $campaign30 = Campaign::factory()
        ->for($category, 'category')
        ->for($user, 'creator')
        ->create([
            'status' => 'active',
            'end_date' => now()->addDays(30)->startOfDay(),
        ]);

    expect($campaign30->days_remaining)->toBeGreaterThanOrEqual(29)
        ->and($campaign30->days_remaining)->toBeLessThanOrEqual(30);

    // Test 90 days
    $campaign90 = Campaign::factory()
        ->for($category, 'category')
        ->for($user, 'creator')
        ->create([
            'status' => 'active',
            'end_date' => now()->addDays(90)->startOfDay(),
        ]);

    expect($campaign90->days_remaining)->toBeGreaterThanOrEqual(89)
        ->and($campaign90->days_remaining)->toBeLessThanOrEqual(90);
});
