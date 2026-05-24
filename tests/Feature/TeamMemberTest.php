<?php

use App\Models\TeamMember;
use Illuminate\Support\Carbon;

it('can calculate years of service', function () {
    $member = new TeamMember([
        'joined_date' => '2020-01-01',
    ]);

    // Use a fixed date for now to ensure consistency
    Carbon::setTestNow('2026-01-01');

    expect($member->years_of_service)->toBe(6);

    Carbon::setTestNow(); // Reset
});

it('returns 0 if joined_date is null', function () {
    $member = new TeamMember([
        'joined_date' => null,
    ]);

    expect($member->years_of_service)->toBe(0);
});
