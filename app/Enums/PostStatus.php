<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Blog post publication status.
 */
enum PostStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Scheduled = 'scheduled';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Draft     => 'Draft',
            self::Published => 'Dipublikasikan',
            self::Scheduled => 'Dijadwalkan',
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
            self::Scheduled => 'sky',
        };
    }

    /**
     * Check if post is visible to public.
     */
    public function isPublic(): bool
    {
        return $this === self::Published;
    }

    /**
     * Check if post can be published now.
     */
    public function canPublishNow(): bool
    {
        return in_array($this, [self::Draft, self::Scheduled], true);
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
