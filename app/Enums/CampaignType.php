<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Types of donation campaigns.
 * Represents the Islamic donation categories.
 */
enum CampaignType: string
{
    case Zakat = 'zakat';
    case Infaq = 'infaq';
    case Sedekah = 'sedekah';
    case Wakaf = 'wakaf';
    case Fidyah = 'fidyah';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Zakat => 'Zakat',
            self::Infaq => 'Infaq',
            self::Sedekah => 'Sedekah',
            self::Wakaf => 'Wakaf',
            self::Fidyah => 'Fidyah',
        };
    }

    /**
     * Get description for each type.
     */
    public function description(): string
    {
        return match ($this) {
            self::Zakat => 'Kewajiban membersihkan harta bagi muslim yang mampu',
            self::Infaq => 'Sumbangan sukarela untuk kebaikan',
            self::Sedekah => 'Pemberian ikhlas tanpa mengharap imbalan',
            self::Wakaf => 'Menyerahkan harta untuk kepentingan umum',
            self::Fidyah => 'Pengganti puasa bagi yang tidak mampu',
        };
    }

    /**
     * Get icon name for UI display.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Zakat => 'hand-coins',
            self::Infaq => 'heart-handshake',
            self::Sedekah => 'gift',
            self::Wakaf => 'building-2',
            self::Fidyah => 'utensils',
        };
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
