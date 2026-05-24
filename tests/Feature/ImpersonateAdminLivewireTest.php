<?php

use App\Models\User;
use App\Models\Masjid;
use Livewire\Livewire;
use Database\Seeders\RolePermissionSeeder;

it('super admin can trigger impersonateAdmin in masjids component', function () {
    $this->seed(RolePermissionSeeder::class);
    // 1. Create a super admin
    $pusat = Masjid::factory()->create(['id' => 1, 'name' => 'Pusat', 'slug' => 'pusat']);
    $superAdmin = User::factory()->create(['masjid_id' => 1]);
    $superAdmin->assignRole('super_admin');

    // 2. Create a tenant and tenant admin
    $tenant = Masjid::factory()->create(['id' => 2, 'name' => 'Cabang', 'slug' => 'cabang']);
    $tenantAdmin = User::factory()->create(['masjid_id' => 2]);
    $tenantAdmin->assignRole('admin');

    // 3. Act as super admin
    $this->actingAs($superAdmin);

    // 4. Test Livewire component
    Livewire::test('admin.masjids.index')
        ->call('impersonateAdmin', $tenant->id)
        ->assertRedirect(route('admin.impersonate', $tenantAdmin->id));
});
