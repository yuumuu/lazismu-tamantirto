<?php

declare(strict_types=1);

use App\Livewire\Guest\DonateWizard;
use App\Models\Campaign;
use App\Models\CampaignCategory;
use App\Models\Donation;
use App\Models\User;
use Livewire\Livewire;

uses()->group('donation-success');

test('donation wizard redirects to success page after submission', function () {
    $user = User::factory()->create();
    $category = CampaignCategory::factory()->create();
    $campaign = Campaign::factory()
        ->for($category, 'category')
        ->for($user, 'creator')
        ->create(['status' => 'active']);

    $component = Livewire::test(DonateWizard::class)
        ->set('campaign_id', $campaign->id)
        ->set('amount', 100000)
        ->set('donation_type', 'infaq')
        ->set('donor_name', 'John Doe')
        ->set('donor_email', 'john@example.com')
        ->set('donor_phone', '081234567890')
        ->set('payment_method', 'bank_transfer')
        ->set('selected_bank', 0)
        ->set('bank_accounts', [
            ['bank_name' => 'BCA', 'account_number' => '1234567890', 'account_name' => 'Lazismu'],
        ])
        ->call('submit');

    // Should redirect to success page
    $component->assertRedirect();

    // Check donation was created
    $donation = Donation::latest()->first();
    expect($donation)->not->toBeNull()
        ->and($donation->donor_name)->toBe('John Doe')
        ->and((int) $donation->amount)->toBe(100000);
});

test('success page displays donation details correctly', function () {
    $user = User::factory()->create();
    $category = CampaignCategory::factory()->create();
    $campaign = Campaign::factory()
        ->for($category, 'category')
        ->for($user, 'creator')
        ->create(['status' => 'active']);

    $donation = Donation::factory()
        ->for($campaign)
        ->create([
            'donor_name' => 'Jane Doe',
            'donor_email' => 'jane@example.com',
            'donor_phone' => '081234567890',
            'amount' => 250000,
            'donation_type' => 'zakat',
            'payment_method' => 'bank_transfer',
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'status' => 'pending',
            'proof_image' => null, // Belum upload bukti
        ]);

    $response = $this->get(route('guest.donate.success', $donation->id));

    $response->assertSuccessful()
        ->assertSee('Jazakallahu Khairan')
        ->assertSee('Terima kasih')
        ->assertSee('Jane Doe')
        ->assertSee('jane@example.com')
        ->assertSee('081234567890')
        ->assertSee('zakat')
        ->assertSee('BCA')
        ->assertSee('1234567890')
        ->assertSee('Rp 250.000')
        ->assertSee('Upload Bukti Pembayaran');
});

test('success page shows bank transfer instructions', function () {
    $user = User::factory()->create();
    $category = CampaignCategory::factory()->create();
    $campaign = Campaign::factory()
        ->for($category, 'category')
        ->for($user, 'creator')
        ->create(['status' => 'active']);

    $donation = Donation::factory()
        ->for($campaign)
        ->create([
            'payment_method' => 'bank_transfer',
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'amount' => 100000,
        ]);

    $response = $this->get(route('guest.donate.success', $donation->id));

    $response->assertSuccessful()
        ->assertSee('Instruksi Pembayaran')
        ->assertSee('BCA')
        ->assertSee('1234567890')
        ->assertSee('Salin');
});

test('success page shows qris instructions', function () {
    $user = User::factory()->create();
    $category = CampaignCategory::factory()->create();
    $campaign = Campaign::factory()
        ->for($category, 'category')
        ->for($user, 'creator')
        ->create(['status' => 'active']);

    $donation = Donation::factory()
        ->for($campaign)
        ->create([
            'payment_method' => 'qris',
            'amount' => 100000,
        ]);

    $response = $this->get(route('guest.donate.success', $donation->id));

    $response->assertSuccessful()
        ->assertSee('Scan QRIS') // Check for the text as it appears
        ->assertSee('QR Code');
});

test('success page shows anonymous donor correctly', function () {
    $user = User::factory()->create();
    $category = CampaignCategory::factory()->create();
    $campaign = Campaign::factory()
        ->for($category, 'category')
        ->for($user, 'creator')
        ->create(['status' => 'active']);

    $donation = Donation::factory()
        ->for($campaign)
        ->create([
            'donor_name' => 'John Doe',
            'is_anonymous' => true,
            'amount' => 100000,
        ]);

    $response = $this->get(route('guest.donate.success', $donation->id));

    $response->assertSuccessful()
        ->assertSee('Hamba Allah')
        ->assertDontSee('John Doe'); // Should not show real name
});

test('success page shows campaign info when donation is for specific campaign', function () {
    $user = User::factory()->create();
    $category = CampaignCategory::factory()->create();
    $campaign = Campaign::factory()
        ->for($category, 'category')
        ->for($user, 'creator')
        ->create([
            'title' => 'Bantu Anak Yatim',
            'status' => 'active',
        ]);

    $donation = Donation::factory()
        ->for($campaign)
        ->create(['amount' => 100000]);

    $response = $this->get(route('guest.donate.success', $donation->id));

    $response->assertSuccessful()
        ->assertSee('Program')
        ->assertSee('Bantu Anak Yatim');
});

test('success page shows donor message when provided', function () {
    $user = User::factory()->create();
    $category = CampaignCategory::factory()->create();
    $campaign = Campaign::factory()
        ->for($category, 'category')
        ->for($user, 'creator')
        ->create(['status' => 'active']);

    $donation = Donation::factory()
        ->for($campaign)
        ->create([
            'donor_message' => 'Semoga bermanfaat untuk saudara-saudara kita',
            'amount' => 100000,
        ]);

    $response = $this->get(route('guest.donate.success', $donation->id));

    $response->assertSuccessful()
        ->assertSee('Pesan') // Check for "Pesan" which is part of "Pesan & Doa"
        ->assertSee('Doa')
        ->assertSee('Semoga bermanfaat untuk saudara-saudara kita');
});

test('success page has link to upload proof when proof not uploaded yet', function () {
    $user = User::factory()->create();
    $category = CampaignCategory::factory()->create();
    $campaign = Campaign::factory()
        ->for($category, 'category')
        ->for($user, 'creator')
        ->create(['status' => 'active']);

    $donation = Donation::factory()
        ->for($campaign)
        ->create([
            'amount' => 100000,
            'proof_image' => null, // Belum upload bukti
        ]);

    $response = $this->get(route('guest.donate.success', $donation->id));

    $response->assertSuccessful()
        ->assertSee('Upload Bukti Pembayaran')
        ->assertSee(route('guest.donate.status', $donation->id));
});

test('success page hides upload proof button when proof already uploaded', function () {
    $user = User::factory()->create();
    $category = CampaignCategory::factory()->create();
    $campaign = Campaign::factory()
        ->for($category, 'category')
        ->for($user, 'creator')
        ->create(['status' => 'active']);

    $donation = Donation::factory()
        ->for($campaign)
        ->create([
            'amount' => 100000,
            'proof_image' => 'donations/proof.jpg', // Sudah upload bukti
        ]);

    $response = $this->get(route('guest.donate.success', $donation->id));

    $response->assertSuccessful()
        ->assertDontSee('Upload Bukti Pembayaran')
        ->assertSee('Bukti Transfer Sudah Diupload')
        ->assertSee('Menunggu Verifikasi');
});

test('success page has link to home', function () {
    $user = User::factory()->create();
    $category = CampaignCategory::factory()->create();
    $campaign = Campaign::factory()
        ->for($category, 'category')
        ->for($user, 'creator')
        ->create(['status' => 'active']);

    $donation = Donation::factory()
        ->for($campaign)
        ->create(['amount' => 100000]);

    $response = $this->get(route('guest.donate.success', $donation->id));

    $response->assertSuccessful()
        ->assertSee('Kembali ke Beranda')
        ->assertSee(route('guest.home'));
});
