<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Lifecycle of a fund withdrawal/distribution.
 */
enum WithdrawalStatus: string
{
    case Draft = 'draft';
    case Verified = 'verified';
    case Sent = 'sent';
    case Cancelled = 'cancelled';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Verified => 'Terverifikasi',
            self::Sent => 'Tersalurkan',
            self::Cancelled => 'Dibatalkan',
        };
    }

    /**
     * Get color for UI badge.
     */
    public function color(): string
    {
        return match ($this) {
            self::Draft => 'zinc',
            self::Verified => 'blue',
            self::Sent => 'green',
            self::Cancelled => 'red',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::Sent, self::Cancelled], true);
    }
}
