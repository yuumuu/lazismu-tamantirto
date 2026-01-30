<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Donation verification status.
 */
enum DonationStatus: string
{
    case Pending       = 'pending';
    case Verified      = 'verified';
    case Rejected      = 'rejected';
    case Expired       = 'expired';
    case PendingManual = 'pending_manual';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Pending       => 'Menunggu Verifikasi',
            self::Verified      => 'Terverifikasi',
            self::Rejected      => 'Ditolak',
            self::Expired       => 'Kedaluwarsa',
            self::PendingManual => 'Perlu Verifikasi Manual',
        };
    }

    /**
     * Get color for UI badge.
     */
    public function color(): string
    {
        return match ($this) {
            self::Pending       => 'amber',
            self::Verified      => 'lime',
            self::Rejected      => 'red',
            self::Expired       => 'zinc',
            self::PendingManual => 'orange',
        };
    }

    /**
     * Check if donation can be verified.
     */
    public function canBeVerified(): bool
    {
        return in_array($this, [self::Pending, self::PendingManual], true);
    }

    /**
     * Check if donation can be rejected.
     */
    public function canBeRejected(): bool
    {
        return in_array($this, [self::Pending, self::PendingManual], true);
    }

    /**
     * Check if donation is final (cannot be changed).
     */
    public function isFinal(): bool
    {
        return in_array($this, [self::Verified, self::Rejected, self::Expired], true);
    }

    /**
     * Check if donation counts toward campaign total.
     */
    public function countsTowardTotal(): bool
    {
        return $this === self::Verified;
    }

    /**
     * Get all values as array.
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get options for form select.
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->toArray();
    }
}
