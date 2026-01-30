<?php

namespace Tests\Feature\Livewire\Admin\Permissions;

use Livewire\Volt\Volt;
use Tests\TestCase;

class IndexTest extends TestCase
{
    public function test_it_can_render(): void
    {
        $component = Volt::test('admin.permissions.index');

        $component->assertSee('');
    }
}
