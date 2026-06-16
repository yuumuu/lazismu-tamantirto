<?php

use App\Models\User;
use Livewire\Volt\Volt;

beforeEach(function () {
    $this->user = User::factory()->create(['role' => 'admin']);
    $this->actingAs($this->user);
});

test('dashboard includes tutorial menu component', function () {
    $response = $this->get(route('dashboard'));

    $response->assertStatus(200)
        ->assertSeeLivewire('admin.tutorial-menu');
});

test('dashboard includes user role meta tag', function () {
    $response = $this->get(route('dashboard'));

    $response->assertStatus(200)
        ->assertSee('<meta name="user-role" content="admin"', false);
});

test('dashboard includes data-tour attributes', function () {
    $response = $this->get(route('dashboard'));

    $response->assertStatus(200)
        ->assertSee('data-tour="dashboard-stats"', false)
        ->assertSee('data-tour="recent-donations"', false)
        ->assertSee('data-tour="navigation-menu"', false);
});

test('campaign page includes data-tour attributes', function () {
    $response = $this->get(route('admin.campaigns.index'));

    $response->assertStatus(200)
        ->assertSee('data-tour="campaign-create"', false)
        ->assertSee('data-tour="campaign-filters"', false)
        ->assertSee('data-tour="campaign-table"', false);
});

test('tutorial menu shows correct tutorials for admin role', function () {
    $response = $this->get(route('dashboard'));

    $response->assertStatus(200);

    // Test the Livewire component directly
    Volt::test('admin.tutorial-menu')
        ->assertSee('Tutorial')
        ->call('openModal')
        ->assertSet('showModal', true)
        ->assertSee('Pengenalan Dashboard')
        ->assertSee('Manajemen Campaign')
        ->assertSee('Verifikasi Donasi')
        ->assertDontSee('Manajemen User'); // Admin shouldn't see super_admin only tutorials
});

test('tutorial menu shows correct tutorials for super admin role', function () {
    $this->user->update(['role' => 'super_admin']);

    Volt::test('admin.tutorial-menu')
        ->assertSee('Tutorial')
        ->call('openModal')
        ->assertSet('showModal', true)
        ->assertSee('Pengenalan Dashboard')
        ->assertSee('Manajemen Campaign')
        ->assertSee('Verifikasi Donasi')
        ->assertSee('Manajemen User') // Super admin should see all tutorials
        ->assertSee('Role & Permission');
});

test('tutorial menu shows limited tutorials for viewer role', function () {
    $this->user->update(['role' => 'viewer']);

    Volt::test('admin.tutorial-menu')
        ->assertSee('Tutorial')
        ->call('openModal')
        ->assertSet('showModal', true)
        ->assertSee('Pengenalan Dashboard')
        ->assertDontSee('Manajemen Campaign') // Viewer shouldn't see admin tutorials
        ->assertDontSee('Verifikasi Donasi')
        ->assertDontSee('Manajemen User');
});

test('javascript includes driver.js library', function () {
    $response = $this->get(route('dashboard'));

    $response->assertStatus(200);

    // Check if the built JavaScript file exists and contains driver.js
    $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
    $jsFile = public_path('build/'.$manifest['resources/js/app.js']['file']);

    expect(file_exists($jsFile))->toBeTrue();

    $jsContent = file_get_contents($jsFile);
    expect($jsContent)->toContain('driver'); // Should contain driver.js code
});
