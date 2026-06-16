<?php

declare(strict_types=1);

use App\Models\Banner;
use App\Models\User;
use Livewire\Volt\Volt;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($this->admin);
});

test('banner create shows smart order suggestions when banners exist', function () {
    // Create existing banners
    Banner::factory()->order(1)->create();
    Banner::factory()->order(3)->create();

    Volt::test('admin.banners.create')
        ->assertSee('Banner saat ini: 1 - 3')
        ->assertSee('Disarankan: 4')
        ->assertSee('Tips Pengurutan')
        ->assertSee('Gunakan 4 untuk menambah di akhir')
        ->assertSee('Gunakan angka di antara 1-3 untuk menyisipkan');
});

test('banner create shows first banner message when no banners exist', function () {
    Volt::test('admin.banners.create')
        ->assertSee('Ini akan menjadi banner pertama (urutan 1)')
        ->assertDontSee('Tips Pengurutan');
});

test('banner create sets default order to next available position', function () {
    Banner::factory()->order(2)->create();
    Banner::factory()->order(5)->create();

    $component = Volt::test('admin.banners.create');

    // Should default to max order + 1 = 6
    expect($component->get('order'))->toBe(6);
});

test('banner edit shows current order context', function () {
    $banner1 = Banner::factory()->order(1)->create();
    $banner2 = Banner::factory()->order(2)->create();
    $banner3 = Banner::factory()->order(4)->create();

    Volt::test('admin.banners.edit', ['banner' => $banner2])
        ->assertSee('Banner lain: 1 - 4')
        ->assertSee('Saat ini: 2')
        ->assertSee('Tips Pengurutan')
        ->assertSee('Gunakan angka > 4 untuk pindah ke akhir')
        ->assertSee('Gunakan angka < 1 untuk pindah ke awal');
});

test('banner edit shows single banner message when only one exists', function () {
    $banner = Banner::factory()->order(1)->create();

    Volt::test('admin.banners.edit', ['banner' => $banner])
        ->assertSee('Ini adalah satu-satunya banner')
        ->assertDontSee('Tips Pengurutan');
});

test('banner create form has proper min and max values', function () {
    Banner::factory()->order(2)->create();
    Banner::factory()->order(5)->create();

    $component = Volt::test('admin.banners.create');

    // Check that min is set to minimum existing order
    $html = $component->html();
    expect($html)->toContain('min="2"');
    // Max should be suggested order + 10 = 6 + 10 = 16
    expect($html)->toContain('max="16"');
});

test('banner order input uses live wire model for real-time feedback', function () {
    Banner::factory()->order(1)->create();

    Volt::test('admin.banners.create')
        ->set('order', 2)
        ->assertSet('order', 2);
});
