<?php

declare(strict_types=1);

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;

test('admin pages have correct titles', function () {
    $this->seed(RolePermissionSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('super_admin'); // Changed from 'admin' to 'super_admin'
    $this->actingAs($user);

    // Test posts index page
    $response = $this->get(route('admin.posts.index'));
    $response->assertStatus(200);
    $response->assertSee('<title>Berita &amp; Artikel - Lazismu</title>', false);

    // Test users index page
    $response = $this->get(route('admin.users.index'));
    $response->assertStatus(200);
    $response->assertSee('<title>Manajemen Pengguna - Lazismu</title>', false);

    // Test campaigns index page
    $response = $this->get(route('admin.campaigns.index'));
    $response->assertStatus(200);
    $response->assertSee('<title>Program Campaign - Lazismu</title>', false);
});

test('dashboard has correct title', function () {
    $this->seed(RolePermissionSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');
    $this->actingAs($user);

    $response = $this->get(route('dashboard'));
    $response->assertStatus(200);
    $response->assertSee('<title>Dashboard - Lazismu</title>', false);
});

test('additional admin pages have correct titles', function () {
    $this->seed(RolePermissionSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');
    $this->actingAs($user);

    // Test reports page
    $response = $this->get(route('admin.reports.index'));
    $response->assertStatus(200);
    $response->assertSee('<title>Laporan Keuangan - Lazismu</title>', false);

    // Test donations page
    $response = $this->get(route('admin.donations.index'));
    $response->assertStatus(200);
    $response->assertSee('<title>Donasi Masuk - Lazismu</title>', false);

    // Test withdrawals page
    $response = $this->get(route('admin.withdrawals.index'));
    $response->assertStatus(200);
    $response->assertSee('<title>Penyaluran Dana - Lazismu</title>', false);

    // Test banners page
    $response = $this->get(route('admin.banners.index'));
    $response->assertStatus(200);
    $response->assertSee('<title>Hero Banners - Lazismu</title>', false);
});

test('management pages have correct titles', function () {
    $this->seed(RolePermissionSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('admin');
    $this->actingAs($user);

    // Test media page
    $response = $this->get(route('admin.media.index'));
    $response->assertStatus(200);
    $response->assertSee('<title>Galeri Media &amp; Dokumen - Lazismu</title>', false);

    // Test muzakkis page
    $response = $this->get(route('admin.muzakkis.index'));
    $response->assertStatus(200);
    $response->assertSee('<title>Database Muzakki - Lazismu</title>', false);

    // Test mustahiks page
    $response = $this->get(route('admin.mustahiks.index'));
    $response->assertStatus(200);
    $response->assertSee('<title>Arsip Mustahik - Lazismu</title>', false);

    // Test distributors page
    $response = $this->get(route('admin.distributors.index'));
    $response->assertStatus(200);
    $response->assertSee('<title>Jaringan Distributor - Lazismu</title>', false);

    // Test pages page
    $response = $this->get(route('admin.pages.index'));
    $response->assertStatus(200);
    $response->assertSee('<title>Halaman Statis - Lazismu</title>', false);

    // Test structure page
    $response = $this->get(route('admin.structure.index'));
    $response->assertStatus(200);
    $response->assertSee('<title>Struktur Organisasi &amp; Tim - Lazismu</title>', false);

    // Test campaign categories page
    $response = $this->get(route('admin.campaign-categories.index'));
    $response->assertStatus(200);
    $response->assertSee('<title>Kategori Kampanye - Lazismu</title>', false);
});

test('super admin pages have correct titles', function () {
    $this->seed(RolePermissionSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('super_admin');
    $this->actingAs($user);

    // Test settings page
    $response = $this->get(route('admin.settings.index'));
    $response->assertStatus(200);
    $response->assertSee('<title>Pengaturan Sistem - Lazismu</title>', false);

    // Test roles page
    $response = $this->get(route('admin.roles.index'));
    $response->assertStatus(200);
    $response->assertSee('<title>Manajemen Akses - Lazismu</title>', false);
});

test('create and edit pages have correct titles', function () {
    $this->seed(RolePermissionSeeder::class);
    $user = User::factory()->create();
    $user->assignRole('super_admin');
    $this->actingAs($user);

    // Test campaign create page
    $response = $this->get(route('admin.campaigns.create'));
    $response->assertStatus(200);
    $response->assertSee('<title>Buat Campaign Baru - Lazismu</title>', false);

    // Test posts create page
    $response = $this->get(route('admin.posts.create'));
    $response->assertStatus(200);
    $response->assertSee('<title>Tulis Artikel - Lazismu</title>', false);

    // Test users create page
    $response = $this->get(route('admin.users.create'));
    $response->assertStatus(200);
    $response->assertSee('<title>Tambah Pengguna Baru - Lazismu</title>', false);
});

test('guest pages have correct titles', function () {
    // Test home page
    $response = $this->get(route('guest.home'));
    $response->assertStatus(200);
    $response->assertSee('<title>Beranda</title>', false);

    // Test about page
    $response = $this->get(route('guest.about'));
    $response->assertStatus(200);
    $response->assertSee('<title>Tentang Kami</title>', false);

    // Test contact page
    $response = $this->get(route('guest.contact'));
    $response->assertStatus(200);
    $response->assertSee('<title>Kontak</title>', false);

    // Test calculator page
    $response = $this->get(route('guest.calculator'));
    $response->assertStatus(200);
    $response->assertSee('<title>Kalkulator Zakat</title>', false);

    // Test campaigns index page
    $response = $this->get(route('guest.campaigns.index'));
    $response->assertStatus(200);
    $response->assertSee('<title>Program Donasi</title>', false);

    // Test posts index page
    $response = $this->get(route('guest.posts.index'));
    $response->assertStatus(200);
    $response->assertSee('<title>Berita &amp; Artikel</title>', false);
});
