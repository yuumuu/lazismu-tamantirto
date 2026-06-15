<?php

declare(strict_types=1);

test('guest homepage loads', function () {
    $response = $this->get(route('guest.home'));
    $response->assertStatus(200);
    $response->assertSee('<title>Beranda</title>', false);
});

test('guest about page loads', function () {
    $response = $this->get(route('guest.about'));
    $response->assertStatus(200);
    $response->assertSee('<title>Tentang Kami</title>', false);
});

test('guest contact page loads', function () {
    $response = $this->get(route('guest.contact'));
    $response->assertStatus(200);
    $response->assertSee('<title>Kontak</title>', false);
});

test('guest calculator page loads', function () {
    $response = $this->get(route('guest.calculator'));
    $response->assertStatus(200);
    $response->assertSee('<title>Kalkulator Zakat</title>', false);
});

test('guest structure page loads', function () {
    $response = $this->get(route('guest.structure'));
    $response->assertStatus(200);
    $response->assertSee('<title>Struktur Organisasi</title>', false);
});

test('guest reports page loads', function () {
    $response = $this->get(route('guest.reports'));
    $response->assertStatus(200);
    $response->assertSee('<title>Laporan Keuangan</title>', false);
});

test('guest campaigns index page loads', function () {
    $response = $this->get(route('guest.campaigns.index'));
    $response->assertStatus(200);
    $response->assertSee('<title>Program Donasi</title>', false);
});
