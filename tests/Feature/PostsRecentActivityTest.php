<?php

declare(strict_types=1);

use App\Enums\DonationStatus;
use App\Models\AuditLog;
use App\Models\Campaign;
use App\Models\Donation;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create(['role' => 'admin']);
    $this->campaign = Campaign::factory()->create();
});

test('posts index shows recent activities section', function () {
    $this->actingAs($this->user);

    $response = $this->get(route('admin.posts.index'));

    $response->assertSee('Aktivitas Terbaru');
    $response->assertSee('Semua aktivitas donasi dan verifikasi terbaru');
});

test('recent activities shows verified donations with correct status', function () {
    // Create verified donation
    $verifiedDonation = Donation::factory()->create([
        'campaign_id' => $this->campaign->id,
        'status' => DonationStatus::Verified,
        'donor_name' => 'John Doe',
        'amount' => 100000,
        'verified_by' => $this->user->id,
        'verified_at' => now(),
    ]);

    $this->actingAs($this->user);

    $response = $this->get(route('admin.posts.index'));

    $response->assertSee('Donasi dari John Doe');
    $response->assertSee('Terverifikasi');
    $response->assertSee('Rp 100.000');
});

test('recent activities shows pending donations with correct status', function () {
    // Create pending donation
    $pendingDonation = Donation::factory()->create([
        'campaign_id' => $this->campaign->id,
        'status' => DonationStatus::Pending,
        'donor_name' => 'Jane Smith',
        'amount' => 50000,
    ]);

    $this->actingAs($this->user);

    $response = $this->get(route('admin.posts.index'));

    $response->assertSee('Donasi dari Jane Smith');
    $response->assertSee('Menunggu');
    $response->assertSee('Rp 50.000');
});

test('recent activities shows suspicious donations with correct status', function () {
    // Create suspicious donation
    $suspiciousDonation = Donation::factory()->create([
        'campaign_id' => $this->campaign->id,
        'status' => DonationStatus::PendingManual,
        'donor_name' => 'Suspicious User',
        'amount' => 1000000,
        'is_suspicious' => true,
        'suspicious_reason' => 'Large amount from new donor',
    ]);

    $this->actingAs($this->user);

    $response = $this->get(route('admin.posts.index'));

    $response->assertSee('Donasi dari Suspicious User');
    $response->assertSee('Mencurigakan');
    $response->assertSee('Rp 1.000.000');
});

test('recent activities shows anonymous donations correctly', function () {
    // Create anonymous donation
    $anonymousDonation = Donation::factory()->create([
        'campaign_id' => $this->campaign->id,
        'status' => DonationStatus::Verified,
        'donor_name' => 'Anonymous Donor',
        'is_anonymous' => true,
        'amount' => 75000,
    ]);

    $this->actingAs($this->user);

    $response = $this->get(route('admin.posts.index'));

    $response->assertSee('Donasi dari Hamba Allah');
    $response->assertSee('Terverifikasi');
});

test('recent activities shows verification audit logs', function () {
    // Create audit log for verification
    AuditLog::create([
        'user_id' => $this->user->id,
        'action' => 'verify',
        'model' => Donation::class,
        'model_id' => '123',
        'changes' => json_encode([
            'transaction_id' => 'LZM260130ABC123',
            'amount' => 200000,
            'status' => 'verified',
        ]),
        'ip_address' => '127.0.0.1',
        'user_agent' => 'Test Browser',
        'created_at' => now(),
    ]);

    $this->actingAs($this->user);

    $response = $this->get(route('admin.posts.index'));

    $response->assertSee('Verify donasi');
    $response->assertSee('ID Transaksi: LZM260130ABC123');
    $response->assertSee('Diverifikasi');
});

test('recent activities shows rejection audit logs', function () {
    // Create audit log for rejection
    AuditLog::create([
        'user_id' => $this->user->id,
        'action' => 'reject',
        'model' => Donation::class,
        'model_id' => '456',
        'changes' => json_encode([
            'transaction_id' => 'LZM260130XYZ456',
            'amount' => 150000,
            'status' => 'rejected',
        ]),
        'ip_address' => '127.0.0.1',
        'user_agent' => 'Test Browser',
        'created_at' => now(),
    ]);

    $this->actingAs($this->user);

    $response = $this->get(route('admin.posts.index'));

    $response->assertSee('Reject donasi');
    $response->assertSee('ID Transaksi: LZM260130XYZ456');
    $response->assertSee('Ditolak');
});

test('recent activities are sorted by created_at desc', function () {
    // Create donations with different timestamps
    $oldDonation = Donation::factory()->create([
        'campaign_id' => $this->campaign->id,
        'donor_name' => 'Old Donor',
        'created_at' => now()->subHours(2),
    ]);

    $newDonation = Donation::factory()->create([
        'campaign_id' => $this->campaign->id,
        'donor_name' => 'New Donor',
        'created_at' => now()->subMinutes(30),
    ]);

    $this->actingAs($this->user);

    $response = $this->get(route('admin.posts.index'));
    $content = $response->getContent();

    // New donation should appear before old donation
    $newPos = strpos($content, 'New Donor');
    $oldPos = strpos($content, 'Old Donor');

    expect($newPos)->toBeLessThan($oldPos);
});

test('recent activities limits to 15 items', function () {
    // Create 20 donations
    Donation::factory()->count(20)->create([
        'campaign_id' => $this->campaign->id,
    ]);

    $this->actingAs($this->user);

    $response = $this->get(route('admin.posts.index'));

    // Should show "Lihat Semua Aktivitas" button when there are 15+ activities
    $response->assertSee('Lihat Semua Aktivitas');
});
