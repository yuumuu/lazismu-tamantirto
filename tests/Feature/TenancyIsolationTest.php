<?php

declare(strict_types=1);

use App\Models\Masjid;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    $this->centralMasjid = Masjid::factory()->create(['id' => 1, 'name' => 'Lazismu Pusat']);

    $this->superAdmin = User::factory()->create(['masjid_id' => 1, 'role' => 'super_admin']);

    $this->tenantMasjid = Masjid::factory()->create(['name' => 'Masjid Cabang A']);
    $this->tenantAdmin = User::factory()->create(['masjid_id' => $this->tenantMasjid->id, 'role' => 'admin']);

    $this->otherMasjid = Masjid::factory()->create(['name' => 'Masjid Cabang B']);
    $this->otherAdmin = User::factory()->create(['masjid_id' => $this->otherMasjid->id, 'role' => 'admin']);
});

// ==================== SIDEBAR MENU ISOLATION ====================

test('tenant admin cannot see Halaman Statis menu', function () {
    $this->actingAs($this->tenantAdmin)
        ->withSession(['active_masjid_id' => $this->tenantMasjid->id])
        ->get(route('dashboard'))
        ->assertDontSee('Halaman Statis');
});

test('tenant admin cannot see Hero Banners menu', function () {
    $this->actingAs($this->tenantAdmin)
        ->withSession(['active_masjid_id' => $this->tenantMasjid->id])
        ->get(route('dashboard'))
        ->assertDontSee('Hero Banners');
});

test('tenant admin cannot see Manajemen Akses menu', function () {
    $this->actingAs($this->tenantAdmin)
        ->withSession(['active_masjid_id' => $this->tenantMasjid->id])
        ->get(route('dashboard'))
        ->assertDontSee('Manajemen Akses');
});

test('super admin can see all menus', function () {
    $this->actingAs($this->superAdmin)
        ->withSession(['active_masjid_id' => 1])
        ->get(route('dashboard'))
        ->assertSee('Halaman Statis')
        ->assertSee('Hero Banners')
        ->assertSee('Manajemen Akses');
});

// ==================== USER MANAGEMENT SCOPING ====================

test('tenant admin can access user management', function () {
    $this->actingAs($this->tenantAdmin)
        ->withSession(['active_masjid_id' => $this->tenantMasjid->id])
        ->get(route('admin.users.index'))
        ->assertOk();
});

test('tenant admin cannot impersonate users', function () {
    $tenantUser = User::factory()->create(['masjid_id' => $this->tenantMasjid->id, 'role' => 'editor']);

    $this->actingAs($this->tenantAdmin)
        ->withSession(['active_masjid_id' => $this->tenantMasjid->id])
        ->get(route('admin.impersonate', $tenantUser))
        ->assertForbidden();
});

// ==================== RBAC ROUTE PROTECTION ====================

test('tenant admin cannot access branches management', function () {
    $this->actingAs($this->tenantAdmin)
        ->withSession(['active_masjid_id' => $this->tenantMasjid->id])
        ->get(route('admin.branches.index'))
        ->assertForbidden();
});

test('tenant admin cannot access masjids management', function () {
    $this->actingAs($this->tenantAdmin)
        ->withSession(['active_masjid_id' => $this->tenantMasjid->id])
        ->get(route('admin.masjids.index'))
        ->assertForbidden();
});

// ==================== SETTINGS FALLBACK ====================

test('settings fall back to pusat when tenant has no override', function () {
    Cache::flush();

    Setting::withoutGlobalScope('masjid')->create([
        'masjid_id' => 1,
        'key' => 'site_name',
        'value' => 'Lazismu Pusat',
        'type' => 'string',
        'group_name' => 'general',
        'label' => 'Nama Situs',
    ]);

    session(['active_masjid_id' => $this->tenantMasjid->id]);

    expect(Setting::getValue('site_name'))->toBe('Lazismu Pusat');
});

test('tenant settings override pusat defaults', function () {
    Cache::flush();

    Setting::withoutGlobalScope('masjid')->create([
        'masjid_id' => 1,
        'key' => 'site_name',
        'value' => 'Lazismu Pusat',
        'type' => 'string',
        'group_name' => 'general',
        'label' => 'Nama Situs',
    ]);

    Setting::withoutGlobalScope('masjid')->create([
        'masjid_id' => $this->tenantMasjid->id,
        'key' => 'site_name',
        'value' => 'Masjid Cabang A',
        'type' => 'string',
        'group_name' => 'general',
        'label' => 'Nama Situs',
    ]);

    session(['active_masjid_id' => $this->tenantMasjid->id]);

    expect(Setting::getValue('site_name'))->toBe('Masjid Cabang A');
});

test('setValue creates tenant-specific setting without affecting pusat', function () {
    Cache::flush();

    Setting::withoutGlobalScope('masjid')->create([
        'masjid_id' => 1,
        'key' => 'site_name',
        'value' => 'Lazismu Pusat',
        'type' => 'string',
        'group_name' => 'general',
        'label' => 'Nama Situs',
    ]);

    session(['active_masjid_id' => $this->tenantMasjid->id]);

    Setting::setValue('site_name', 'Cabang Override');

    // Tenant gets override
    expect(Setting::getValue('site_name'))->toBe('Cabang Override');

    // Pusat is untouched
    $pusatValue = Setting::withoutGlobalScope('masjid')
        ->where('masjid_id', 1)
        ->where('key', 'site_name')
        ->first();

    expect($pusatValue->value)->toBe('Lazismu Pusat');
});

test('getGroup merges pusat defaults with tenant overrides', function () {
    Cache::flush();

    Setting::withoutGlobalScope('masjid')->create([
        'masjid_id' => 1,
        'key' => 'site_name',
        'value' => 'Lazismu Pusat',
        'type' => 'string',
        'group_name' => 'general',
        'label' => 'Nama Situs',
    ]);

    Setting::withoutGlobalScope('masjid')->create([
        'masjid_id' => 1,
        'key' => 'site_email',
        'value' => 'pusat@lazismu.id',
        'type' => 'string',
        'group_name' => 'general',
        'label' => 'Email Situs',
    ]);

    Setting::withoutGlobalScope('masjid')->create([
        'masjid_id' => $this->tenantMasjid->id,
        'key' => 'site_name',
        'value' => 'Cabang A',
        'type' => 'string',
        'group_name' => 'general',
        'label' => 'Nama Situs',
    ]);

    session(['active_masjid_id' => $this->tenantMasjid->id]);

    $group = Setting::getGroup('general');

    expect($group['site_name'])->toBe('Cabang A');
    expect($group['site_email'])->toBe('pusat@lazismu.id');
});
