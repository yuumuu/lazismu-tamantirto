<?php

declare(strict_types=1);

use App\Livewire\Guest\DonateWizard;
use Livewire\Livewire;

test('calculator can pass amount and type to donation wizard', function () {
    $amount = 250000;
    $type = 'zakat';
    $subtype = 'profesi';

    $component = Livewire::test(DonateWizard::class, [
        'amount' => $amount,
        'donation_type' => $type,
        'donation_subtype' => $subtype,
        'from_calculator' => true,
    ]);

    $component
        ->assertSet('amount', $amount)
        ->assertSet('donation_type', $type)
        ->assertSet('donation_subtype', $subtype)
        ->assertSet('from_calculator', true)
        ->assertSee('Dari Kalkulator Zakat')
        ->assertSee('Zakat Profesi');
});

test('calculator url parameters are properly parsed', function () {
    $response = $this->get(route('guest.donate.form', [
        'amount' => 500000,
        'type' => 'zakat',
        'subtype' => 'maal',
        'from' => 'calculator',
    ]));

    $response->assertSuccessful()
        ->assertSeeLivewire(DonateWizard::class);
});

test('zakat profesi calculation redirects correctly', function () {
    $response = $this->get(route('guest.calculator'));

    $response->assertSuccessful()
        ->assertSee('Kalkulator Zakat')
        ->assertSee('Zakat Profesi')
        ->assertSee('Tunaikan Sekarang');
});

test('donation wizard shows calculator banner when from calculator', function () {
    $component = Livewire::test(DonateWizard::class)
        ->set('amount', 300000)
        ->set('donation_type', 'zakat')
        ->set('donation_subtype', 'fitrah')
        ->set('from_calculator', true);

    $component
        ->assertSee('Dari Kalkulator Zakat')
        ->assertSee('Zakat Fitrah')
        ->assertSee('Hitung Ulang');
});

test('donation wizard does not show calculator banner when not from calculator', function () {
    $component = Livewire::test(DonateWizard::class)
        ->set('amount', 100000)
        ->set('donation_type', 'infaq')
        ->set('from_calculator', false);

    $component
        ->assertDontSee('Dari Kalkulator Zakat')
        ->assertDontSee('Hitung Ulang');
});

test('user can change amount after coming from calculator', function () {
    $component = Livewire::test(DonateWizard::class)
        ->set('amount', 250000)
        ->set('donation_type', 'zakat')
        ->set('donation_subtype', 'profesi')
        ->set('from_calculator', true);

    // User changes the amount
    $component->set('amount', 500000);

    $component->assertSet('amount', 500000);
});

test('zakat subtype label returns correct values', function () {
    $component = new DonateWizard;

    $component->donation_subtype = 'profesi';
    expect($component->getZakatSubtypeLabel())->toBe('Zakat Profesi');

    $component->donation_subtype = 'maal';
    expect($component->getZakatSubtypeLabel())->toBe('Zakat Maal');

    $component->donation_subtype = 'fitrah';
    expect($component->getZakatSubtypeLabel())->toBe('Zakat Fitrah');

    $component->donation_subtype = null;
    expect($component->getZakatSubtypeLabel())->toBe('Zakat');
});
