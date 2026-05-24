<?php

declare(strict_types=1);

use App\Models\Masjid;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

beforeEach(function () {
    $this->seed(\Database\Seeders\RolePermissionSeeder::class);

    $this->centralMasjid = Masjid::factory()->create(['id' => 1, 'name' => 'Lazismu Pusat']);

    $this->superAdmin = User::factory()->create(['masjid_id' => 1]);
    $this->superAdmin->assignRole('super_admin');

    $this->masjid = Masjid::factory()->create(['name' => 'Masjid Cabang']);
    $this->tenantAdmin = User::factory()->create(['masjid_id' => $this->masjid->id]);
    $this->tenantAdmin->assignRole('admin');
});

test('super admin can impersonate tenant admin', function () {
    $this->actingAs($this->superAdmin)
        ->get(route('admin.impersonate', $this->tenantAdmin))
        ->assertRedirect(route('dashboard'));

    expect(Auth::id())->toBe($this->tenantAdmin->id);
    expect(session('impersonated_by'))->toBe($this->superAdmin->id);
});

test('regular admin cannot impersonate others', function () {
    $otherUser = User::factory()->create();

    $this->actingAs($this->tenantAdmin)
        ->get(route('admin.impersonate', $otherUser))
        ->assertStatus(403);

    expect(Auth::id())->toBe($this->tenantAdmin->id);
    expect(session()->has('impersonated_by'))->toBeFalse();
});

test('can leave impersonation', function () {
    $this->withoutExceptionHandling();
    $this->actingAs($this->superAdmin);

    // Start impersonation
    $this->get(route('admin.impersonate', $this->tenantAdmin));
    expect(Auth::id())->toBe($this->tenantAdmin->id);

    // Leave impersonation
    $this->post(route('admin.impersonate.leave'))
        ->assertRedirect(route('admin.masjids.index'));

    expect(Auth::id())->toBe($this->superAdmin->id);
    expect(session()->has('impersonated_by'))->toBeFalse();
});

test('impersonation banner is visible when impersonating', function () {
    $this->actingAs($this->superAdmin);

    // Manually simulate impersonation state
    session(['impersonated_by' => $this->superAdmin->id]);
    Auth::login($this->tenantAdmin);

    $this->get(route('dashboard'))
        ->assertSee('Anda sedang masuk sebagai')
        ->assertSee($this->tenantAdmin->name)
        ->assertSee('Kembali ke Akun Saya');
});

test('monitoring show page resolves masjid parameter correctly', function () {
    $this->actingAs($this->superAdmin);

    $this->get(route('admin.monitoring.show', $this->masjid))
        ->assertOk()
        ->assertSee('Detail Monitoring')
        ->assertSee($this->masjid->name);
});
