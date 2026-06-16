<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;

uses(RefreshDatabase::class);

test('tutorial modal can be opened and closed', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user);

    Volt::test('admin.tutorial-menu')
        ->assertSet('showModal', false)
        ->call('openModal')
        ->assertSet('showModal', true)
        ->call('closeModal')
        ->assertSet('showModal', false);
});

test('tutorial modal shows available tutorials for user role', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $this->actingAs($user);

    $component = Volt::test('admin.tutorial-menu');

    $availableTutorials = $component->get('availableTutorials');

    expect($availableTutorials)->toBeArray();
    expect(count($availableTutorials))->toBeGreaterThan(0);

    // Admin should have access to multiple tutorials
    $tutorialNames = collect($availableTutorials)->pluck('name')->toArray();
    expect($tutorialNames)->toContain('dashboard-overview');
    expect($tutorialNames)->toContain('campaign-management');
    expect($tutorialNames)->toContain('donation-verification');
});

test('super admin has access to all tutorials', function () {
    $user = User::factory()->create(['role' => 'super_admin']);

    $this->actingAs($user);

    $component = Volt::test('admin.tutorial-menu');

    $availableTutorials = $component->get('availableTutorials');

    expect($availableTutorials)->toBeArray();
    expect(count($availableTutorials))->toBe(7); // All tutorials

    $tutorialNames = collect($availableTutorials)->pluck('name')->toArray();
    expect($tutorialNames)->toContain('user-management');
    expect($tutorialNames)->toContain('role-permission');
});

test('viewer has limited tutorial access', function () {
    $user = User::factory()->create(['role' => 'viewer']);

    $this->actingAs($user);

    $component = Volt::test('admin.tutorial-menu');

    $availableTutorials = $component->get('availableTutorials');

    expect($availableTutorials)->toBeArray();
    expect(count($availableTutorials))->toBe(1); // Only dashboard overview

    $tutorialNames = collect($availableTutorials)->pluck('name')->toArray();
    expect($tutorialNames)->toContain('dashboard-overview');
    expect($tutorialNames)->not->toContain('user-management');
});
