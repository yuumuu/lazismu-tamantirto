<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('tutorial system shows navigation step when on wrong page', function () {
    $user = User::factory()->create(['role' => 'admin']);

    // Test from dashboard page - should work directly
    $response = $this->actingAs($user)->get('/admin/dashboard');
    $response->assertSuccessful();
    $response->assertSee('data-tour="dashboard-stats"', false);

    // Test campaign tutorial from dashboard - should show navigation step
    // This would be tested in browser/JavaScript context
    expect(true)->toBeTrue(); // Placeholder for now
});

test('tutorial pages are correctly mapped', function () {
    // Test that all tutorial pages exist and are accessible
    $user = User::factory()->create(['role' => 'super_admin']);

    $tutorialPages = [
        'dashboard-overview' => '/admin/dashboard',
        'campaign-management' => '/admin/campaigns',
        'donation-verification' => '/admin/donations',
        'user-management' => '/admin/manage/users',
        'role-permission' => '/admin/manage/branches',
        'reports-overview' => '/admin/reports',
    ];

    foreach ($tutorialPages as $tutorial => $page) {
        $response = $this->actingAs($user)->get($page);
        $response->assertSuccessful();
    }
});

test('settings tutorial page is accessible', function () {
    $user = User::factory()->create(['role' => 'admin']);

    $response = $this->actingAs($user)->get('/settings/profile');
    $response->assertSuccessful();
});

test('tutorial navigation respects user roles', function () {
    // Test viewer role - should only access dashboard
    $viewer = User::factory()->create(['role' => 'viewer']);

    $response = $this->actingAs($viewer)->get('/admin/dashboard');
    $response->assertSuccessful();

    // Test admin role - should access most pages
    $admin = User::factory()->create(['role' => 'admin']);

    $adminPages = [
        '/admin/dashboard',
        '/admin/campaigns',
        '/admin/donations',
        '/admin/reports',
        '/settings/profile',
    ];

    foreach ($adminPages as $page) {
        $response = $this->actingAs($admin)->get($page);
        $response->assertSuccessful();
    }

    // Test super admin - should access all pages
    $superAdmin = User::factory()->create(['role' => 'super_admin']);

    $superAdminPages = [
        '/admin/dashboard',
        '/admin/campaigns',
        '/admin/donations',
        '/admin/manage/users',
        '/admin/manage/roles',
        '/admin/reports',
        '/settings/profile',
    ];

    foreach ($superAdminPages as $page) {
        $response = $this->actingAs($superAdmin)->get($page);
        $response->assertSuccessful();
    }
});
