<?php

declare(strict_types=1);

use App\Models\BlogPost;
use App\Models\Campaign;
use App\Models\CampaignCategory;
use Illuminate\Support\Facades\DB;

beforeEach(function () {
    // Create test data with proper relationships
    $user = \App\Models\User::factory()->create();
    $category = CampaignCategory::factory()->create();

    Campaign::factory()
        ->count(5)
        ->for($category, 'category')
        ->for($user, 'creator')
        ->create(['is_featured' => true, 'status' => 'active']);

    $blogCategory = \App\Models\BlogCategory::factory()->create();

    BlogPost::factory()
        ->count(5)
        ->for($blogCategory, 'category')
        ->for($user, 'author')
        ->create(['status' => 'published', 'published_at' => now()]);
});

test('home page loads with minimal queries', function () {
    // Enable query logging
    DB::enableQueryLog();

    $response = $this->get(route('guest.home'));

    $queries = DB::getQueryLog();
    $queryCount = count($queries);

    // Should be around 10-15 queries (realistic with Laravel overhead):
    // 1. Featured campaigns
    // 2. Verified donations count
    // 3. Verified donations sum
    // 4. Latest blog posts
    // 5. Categories eager load
    // 6. Banners
    // 7-10. Session/cache/permission queries
    expect($queryCount)->toBeLessThan(20)
        ->and($response)->assertSuccessful();

    // Verify no N+1 queries by checking for duplicate query patterns
    $queryStrings = array_map(fn ($q) => $q['query'], $queries);
    $uniqueQueries = array_unique($queryStrings);

    // Should not have many duplicate queries (allow up to 5 for Laravel internal queries)
    expect(count($queryStrings) - count($uniqueQueries))->toBeLessThan(6);
});

test('campaigns index loads with minimal queries', function () {
    DB::enableQueryLog();

    $response = $this->get(route('guest.campaigns.index'));

    $queries = DB::getQueryLog();
    $queryCount = count($queries);

    // Should be around 10-15 queries (realistic):
    // 1. Campaigns with pagination
    // 2. Categories eager load
    // 3. Creators eager load
    // 4. Verified donations count
    // 5. Verified donations sum
    // 6-10. Session/pagination/permission queries
    expect($queryCount)->toBeLessThan(20)
        ->and($response)->assertSuccessful();
});

test('campaign show page loads with minimal queries', function () {
    $user = \App\Models\User::factory()->create();
    $category = CampaignCategory::factory()->create();

    $campaign = Campaign::factory()
        ->for($category, 'category')
        ->for($user, 'creator')
        ->create([
            'status' => 'active',
            'is_featured' => true,
        ]);

    DB::enableQueryLog();

    $response = $this->get(route('guest.campaigns.show', $campaign->slug));

    $queries = DB::getQueryLog();
    $queryCount = count($queries);

    // Should be around 4-6 queries:
    // 1. Campaign with relationships
    // 2. Verified donations count
    // 3. Verified donations sum
    // 4. Latest donors
    // 5-6. Session queries
    expect($queryCount)->toBeLessThan(10)
        ->and($response)->assertSuccessful();
});

test('no N+1 queries when displaying multiple campaigns', function () {
    // Create 10 campaigns to test N+1
    $user = \App\Models\User::factory()->create();
    $category = CampaignCategory::factory()->create();

    Campaign::factory()
        ->count(10)
        ->for($category, 'category')
        ->for($user, 'creator')
        ->create(['status' => 'active']);

    DB::enableQueryLog();

    $this->get(route('guest.campaigns.index'));

    $queries = DB::getQueryLog();

    // With 10 campaigns, should still be under 25 queries (realistic)
    // If we had N+1, it would be 40+ queries
    expect(count($queries))->toBeLessThan(25);
});

test('campaign progress calculation uses eager loaded data', function () {
    $user = \App\Models\User::factory()->create();
    $category = CampaignCategory::factory()->create();

    $campaign = Campaign::factory()
        ->for($category, 'category')
        ->for($user, 'creator')
        ->create(['status' => 'active']);

    // Create some donations
    \App\Models\Donation::factory()
        ->count(5)
        ->for($campaign)
        ->create(['status' => 'verified']);

    DB::enableQueryLog();

    // Load campaign with eager loading
    $campaignWithEagerLoad = Campaign::query()
        ->withSum('verifiedDonations', 'amount')
        ->find($campaign->id);

    // Access progress percentage (should not trigger additional query)
    $progress = $campaignWithEagerLoad->progress_percentage;

    $queries = DB::getQueryLog();

    // Should only have 1 query (the initial load with eager loading)
    expect(count($queries))->toBe(1)
        ->and($progress)->toBeGreaterThanOrEqual(0);
});

test('blog posts load with category without N+1', function () {
    DB::enableQueryLog();

    $this->get(route('guest.home'));

    $queries = DB::getQueryLog();

    // Check that we're not querying categories separately for each post
    $categoryQueries = array_filter($queries, function ($query) {
        return str_contains($query['query'], 'blog_categories');
    });

    // Should have at most 1 category query (eager loading)
    expect(count($categoryQueries))->toBeLessThanOrEqual(1);
});
