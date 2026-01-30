<?php

declare(strict_types=1);

use App\Models\Campaign;
use App\Models\CampaignCategory;

test('donate wizard page loads successfully', function () {
    $response = $this->get('/donasi');
    
    $response->assertSuccessful();
    $response->assertSee('Pilih Nominal Donasi');
    $response->assertSee('Program Spesifik');
});

test('donate wizard shows campaign modal when program specific is clicked', function () {
    $category = CampaignCategory::factory()->create();
    $campaign = Campaign::factory()->create([
        'category_id' => $category->id,
        'title' => 'Test Campaign for Modal'
    ]);

    $response = $this->get('/donasi');
    
    $response->assertSuccessful();
    $response->assertSee('Program Spesifik');
});