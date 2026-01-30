<?php

declare(strict_types=1);

use App\Models\Donation;
use App\Models\User;
use App\Enums\PaymentMethod;

test('upload proof page loads without errors', function () {
    // Seed the database to ensure settings are available
    $this->seed();
    
    // Create a donation with bank transfer method
    $donation = Donation::factory()->create([
        'payment_method' => PaymentMethod::BankTransfer,
        'bank_name' => 'Bank Syariah Indonesia',
        'account_number' => '7123456789'
    ]);

    $response = $this->get("/donasi/status/{$donation->id}");
    
    $response->assertStatus(200);
    // Should show bank accounts from settings, not from donation record
    $response->assertSee('Ban'); // First 3 chars of Bank Syariah Indonesia
    $response->assertSee('Bank Syariah Indonesia'); // Full bank name
    $response->assertSee('LazisMU Tamantirto'); // Account name
});

test('bank accounts display correctly in upload proof', function () {
    $donation = Donation::factory()->create([
        'payment_method' => PaymentMethod::BankTransfer
    ]);

    $response = $this->get("/donasi/status/{$donation->id}");
    
    $response->assertStatus(200);
    // Should not throw "Undefined array key 'bank'" error
    $response->assertDontSee('Undefined array key');
});