<?php

namespace Tests\Feature\Livewire\Admin\Roles;

use Livewire\Volt\Volt;
use Tests\TestCase;

class IndexTest extends TestCase
{
    public function test_it_can_render(): void
    {
        $this->artisan('migrate:fresh');
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);

        $component = Volt::test('admin.roles.index');

        $component->assertSee('');
    }
}
