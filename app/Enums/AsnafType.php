<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Asnaf categories for Mustahik benefit recipients.
 */
enum AsnafType: string
{
    case Fakir = 'fakir';
    case Miskin = 'miskin';
    case Amil = 'amil';
    case Mualaf = 'mualaf';
    case Riqab = 'riqab';
    case Gharim = 'gharim';
    case Fisabilillah = 'fisabilillah';
    case IbnuSabil = 'ibnu_sabil';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Fakir => 'Fakir',
            self::Miskin => 'Miskin',
            self::Amil => 'Amil',
            self::Mualaf => 'Mualaf',
            self::Riqab => 'Riqab',
            self::Gharim => 'Gharim',
            self::Fisabilillah => 'Fisabilillah',
            self::IbnuSabil => 'Ibnu Sabil',
        };
    }

    /**
     * Get description for the asnaf.
     */
    public function description(): string
    {
        return match ($this) {
            self::Fakir => 'Orang yang tidak memiliki harta dan pekerjaan.',
            self::Miskin => 'Orang yang memiliki pekerjaan tapi tidak cukup untuk memenuhi kebutuhan.',
            self::Amil => 'Panitia/petugas pengelola zakat.',
            self::Mualaf => 'Orang yang baru masuk Islam.',
            self::Riqab => 'Hamba sahaya atau budak.',
            self::Gharim => 'Orang yang berhutang untuk kebutuhan pokok.',
            self::Fisabilillah => 'Orang yang berjihad di jalan Allah.',
            self::IbnuSabil => 'Orang yang sedang dalam perjalanan (musafir) dan kehabisan biaya.',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->toArray();
    }
}
