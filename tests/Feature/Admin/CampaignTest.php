<?php

use App\Models\Campaign;
use App\Models\CampaignCategory;
use App\Models\User;
use Livewire\Volt\Volt;

beforeEach(function () {
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    $this->user = User::factory()->create();
    $this->user->assignRole('admin');
    $this->category = CampaignCategory::factory()->create();
});

test('can view campaign index', function () {
    $this->actingAs($this->user)
        ->get(route('admin.campaigns.index'))
        ->assertStatus(200)
        ->assertSeeLivewire('admin.campaigns.index');
});

test('can create campaign', function () {
    $this->actingAs($this->user);

    Volt::test('admin.campaigns.create')
        ->set('category_id', $this->category->id)
        ->set('title', 'Campaign Test Title')
        ->set('short_description', 'This is a short description with at least fifty characters required for validation.')
        ->set('description', 'This is a long description with at least one hundred characters required for validation to pass the test successfully.')
        ->set('target_amount', 1000000)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.campaigns.index'));

    expect(Campaign::where('title', 'Campaign Test Title')->exists())->toBeTrue();
});

test('can search campaigns', function () {
    Campaign::factory()->create(['title' => 'Searchable Campaign']);
    Campaign::factory()->create(['title' => 'Other Campaign']);

    $this->actingAs($this->user);

    Volt::test('admin.campaigns.index')
        ->set('search', 'Searchable')
        ->assertSee('Searchable Campaign')
        ->assertDontSee('Other Campaign');
});

test('can delete campaign', function () {
    $campaign = Campaign::factory()->create();

    $this->actingAs($this->user);

    Volt::test('admin.campaigns.index')
        ->call('delete', $campaign->id)
        ->assertHasNoErrors();

    expect(Campaign::find($campaign->id))->toBeNull();
});
