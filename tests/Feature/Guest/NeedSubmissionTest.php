<?php

declare(strict_types=1);

use App\Models\Branch;
use App\Models\Need;
use Illuminate\Http\UploadedFile;

beforeEach(function () {
    Branch::factory()->create(['id' => 1, 'name' => 'Pusat', 'slug' => 'pusat']);
    session(['active_branch_id' => 1]);
});

test('need create page renders', function () {
    $this->withServerVariables(['HTTP_HOST' => env('APP_DOMAIN', 'lazismu.test')])
        ->get('/pengajuan')
        ->assertSuccessful();
});

test('need store with valid data creates need and shows token', function () {
    $response = $this->withServerVariables(['HTTP_HOST' => env('APP_DOMAIN', 'lazismu.test')])
        ->post('/pengajuan', [
            'applicant_name' => 'Test User',
            'applicant_phone' => '081234567890',
            'applicant_address' => 'Jl. Test No. 123',
            'title' => 'Bantuan Biaya Pengobatan',
            'description' => 'Saya membutuhkan bantuan untuk biaya pengobatan.',
            'category' => 'health',
            'amount_requested' => 500000,
        ]);

    $response->assertSuccessful();

    $need = Need::where('applicant_phone', '081234567890')->first();
    expect($need)->not->toBeNull();
    expect($need->tracking_token)->not->toBeEmpty();
    expect(strlen($need->tracking_token))->toBe(8);
});

test('need store requires required fields', function () {
    $response = $this->withServerVariables(['HTTP_HOST' => env('APP_DOMAIN', 'lazismu.test')])
        ->post('/pengajuan', []);

    $response->assertSessionHasErrors(['applicant_name', 'applicant_phone', 'applicant_address', 'title', 'description', 'category', 'amount_requested']);
});

test('need check status shows result with valid token', function () {
    $need = Need::factory()->create([
        'applicant_phone' => '081234567890',
        'status' => 'pending',
    ]);

    $response = $this->withServerVariables(['HTTP_HOST' => env('APP_DOMAIN', 'lazismu.test')])
        ->post('/cek-status', [
            'phone' => '081234567890',
            'token' => $need->tracking_token,
        ]);

    $response->assertSuccessful();
    $response->assertSee($need->title);
});

test('need check status returns error with invalid token', function () {
    $response = $this->withServerVariables(['HTTP_HOST' => env('APP_DOMAIN', 'lazismu.test')])
        ->post('/cek-status', [
            'phone' => '081234567890',
            'token' => 'INVALID1',
        ]);

    $response->assertSessionHasErrors('not_found');
});

test('need store with file attachment', function () {
    $file = UploadedFile::fake()->image('ktp.jpg', 200, 200);

    $response = $this->withServerVariables(['HTTP_HOST' => env('APP_DOMAIN', 'lazismu.test')])
        ->post('/pengajuan', [
            'applicant_name' => 'Test With File',
            'applicant_phone' => '087654321098',
            'applicant_address' => 'Jl. File Upload No. 45',
            'title' => 'Bantuan Untuk File',
            'description' => 'Testing with file upload.',
            'category' => 'education',
            'amount_requested' => 250000,
            'attachment' => $file,
        ]);

    $response->assertSuccessful();

    $need = Need::where('applicant_phone', '087654321098')->first();
    expect($need)->not->toBeNull();
    expect($need->attachment)->not->toBeNull();
});
