<?php

declare(strict_types=1);

use Livewire\Volt\Volt;

test('program specific flow shows redirect link', function () {
    Volt::test('guest.donate-wizard')
        ->set('is_specific_campaign', true)
        ->assertSee('Klik Untuk Pilih Program')
        ->assertSee('href="'.route('guest.campaigns.index').'"', false);
});

test('general donation flow does not show redirect link', function () {
    Volt::test('guest.donate-wizard')
        ->set('is_specific_campaign', false)
        ->assertDontSee('Klik Untuk Pilih Program');
});

test('redirect link points to campaigns index', function () {
    $response = $this->get('/donasi');

    $response->assertStatus(200);

    // Test that when specific campaign is selected, the link appears
    Volt::test('guest.donate-wizard')
        ->set('is_specific_campaign', true)
        ->assertSee(route('guest.campaigns.index'), false);
});
