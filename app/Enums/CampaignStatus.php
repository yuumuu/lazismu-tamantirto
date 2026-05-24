<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Campaign lifecycle status.
 */
enum CampaignStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Completed = 'completed';
    case Paused = 'paused';
    case Cancelled = 'cancelled';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Active => 'Aktif',
            self::Completed => 'Selesai',
            self::Paused => 'Dijeda',
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
            self::Active => 'lime',
            self::Completed => 'sky',
            self::Paused => 'amber',
            self::Cancelled => 'red',
        };
    }

    /**
     * Check if campaign can receive donations.
     */
    public function canReceiveDonations(): bool
    {
        return $this === self::Active;
    }

    /**
     * Check if campaign can be edited.
     */
    public function canBeEdited(): bool
    {
        return in_array($this, [self::Draft, self::Active, self::Paused], true);
    }

    /**
     * Check if campaign can be published.
     */
    public function canBePublished(): bool
    {
        return $this === self::Draft;
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
