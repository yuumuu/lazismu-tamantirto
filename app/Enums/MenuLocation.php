<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Navigation menu locations.
 */
enum MenuLocation: string
{
    case Header = 'header';
    case Footer = 'footer';
    case Mobile = 'mobile';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Header => 'Header',
            self::Footer => 'Footer',
            self::Mobile => 'Mobile',
        };
    }

    /**
     * Get description.
     */
    public function description(): string
    {
        return match ($this) {
            self::Header => 'Navigasi utama di bagian atas',
            self::Footer => 'Link di bagian bawah halaman',
            self::Mobile => 'Menu navigasi untuk perangkat mobile',
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
