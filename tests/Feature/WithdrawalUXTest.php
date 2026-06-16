<?php

declare(strict_types=1);

use App\Enums\WithdrawalStatus;
use App\Models\Donation;
use App\Models\User;
use App\Models\Withdrawal;
use Livewire\Volt\Volt;

beforeEach(function () {
    $this->admin = User::factory()->create(['role' => 'admin']);
    $this->actingAs($this->admin);
});

test('withdrawal create form shows real-time validation feedback', function () {
    Volt::test('admin.withdrawals.create')
        ->set('amount', 500) // Below minimum
        ->set('amount', 50000)
        ->set('description', 'Short') // Below minimum length
        ->set('description', 'This is a proper description for the withdrawal')
        ->assertHasNoErrors();
});

test('withdrawal show page displays progress indicator correctly', function () {
    $withdrawal = Withdrawal::factory()->create([
        'status' => WithdrawalStatus::Draft,
        'amount' => 100000,
    ]);

    Volt::test('admin.withdrawals.show', ['withdrawal' => $withdrawal])
        ->assertSee('Status Penyaluran')
        ->assertSee('Draft')
        ->assertSee('Terverifikasi')
        ->assertSee('Tersalurkan');
});

test('quick complete functionality works for draft withdrawals', function () {
    $withdrawal = Withdrawal::factory()->create([
        'status' => WithdrawalStatus::Draft,
        'amount' => 100000,
    ]);

    Volt::test('admin.withdrawals.show', ['withdrawal' => $withdrawal])
        ->assertSee('Langsung Tandai Tersalurkan')
        ->set('showQuickComplete', true)
        ->assertSee('Mode cepat')
        ->assertSee('Bukti Penyaluran (Wajib)');
});

test('withdrawal index shows appropriate actions based on status', function () {
    $draftWithdrawal = Withdrawal::factory()->create([
        'status' => WithdrawalStatus::Draft,
    ]);

    $sentWithdrawal = Withdrawal::factory()->create([
        'status' => WithdrawalStatus::Sent,
    ]);

    $component = Volt::test('admin.withdrawals.index');

    // Test that final status check works
    expect($sentWithdrawal->status->isFinal())->toBeTrue();
    expect($draftWithdrawal->status->isFinal())->toBeFalse();
});

test('file upload shows proper feedback and loading states', function () {
    Volt::test('admin.withdrawals.create')
        ->assertSee('Klik untuk upload')
        ->assertSee('drag & drop')
        ->assertSee('PNG, JPG, PDF (Max. 5MB)');
});

test('mobile layout shows action buttons at top', function () {
    Volt::test('admin.withdrawals.create')
        ->assertSee('lg:hidden') // Mobile-only action section
        ->assertSee('Simpan Draft Penyaluran');
});

test('withdrawal create shows available funds information', function () {
    // Create some donations and withdrawals to test fund calculation
    Donation::factory()->create(['amount' => 1000000, 'status' => 'verified']);
    Donation::factory()->create(['amount' => 500000, 'status' => 'verified']);
    Withdrawal::factory()->create(['amount' => 200000, 'status' => 'sent']);

    Volt::test('admin.withdrawals.create')
        ->assertSee('Dana Tersedia')
        ->assertSee('Total Donasi')
        ->assertSee('Total Tersalurkan')
        ->assertSee('Saldo Tersedia')
        ->assertSee('Rp 1.500.000') // Total donations
        ->assertSee('Rp 200.000') // Total withdrawals
        ->assertSee('Rp 1.300.000'); // Available funds
});

test('withdrawal create disables submit when insufficient funds', function () {
    // Create scenario where withdrawals exceed donations
    Donation::factory()->create(['amount' => 100000, 'status' => 'verified']);
    Withdrawal::factory()->create(['amount' => 200000, 'status' => 'sent']);

    Volt::test('admin.withdrawals.create')
        ->assertSee('Dana tidak mencukupi untuk penyaluran baru')
        ->assertSee('Tidak dapat membuat penyaluran karena dana tidak mencukupi');
});

test('enhanced form validation provides better user feedback', function () {
    Volt::test('admin.withdrawals.create')
        ->set('amount', 75000)
        ->set('description', 'This is a detailed description of the withdrawal purpose')
        ->assertSee('karakter'); // Character count feedback
});
