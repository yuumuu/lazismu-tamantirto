<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Static page publication status.
 */
enum PageStatus: string
{
    case Draft = 'draft';
    case Published = 'published';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Draft     => 'Draft',
            self::Published => 'Dipublikasikan',
        };
    }

    /**
     * Get color for UI badge.
     */
    public function color(): string
    {
        return match ($this) {
            self::Draft     => 'zinc',
            self::Published => 'lime',
        };
    }

    /**
     * Check if page is visible to public.
     */
    public function isPublic(): bool
    {
        return $this === self::Published;
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
