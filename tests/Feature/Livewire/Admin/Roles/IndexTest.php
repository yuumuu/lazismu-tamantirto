<?php

namespace Tests\Feature\Livewire\Admin\Roles;

use Livewire\Volt\Volt;
use Tests\TestCase;

class IndexTest extends TestCase
{
    public function test_it_can_render(): void
    {
        $component = Volt::test('admin.roles.index');

        $component->assertSee('');
    }
}
