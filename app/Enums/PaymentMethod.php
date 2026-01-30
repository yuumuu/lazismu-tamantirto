<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Payment methods for donations.
 */
enum PaymentMethod: string
{
    case Qris = 'qris';
    case BankTransfer = 'bank_transfer';
    case Gopay = 'gopay';
    case Manual = 'manual';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::Qris          => 'QRIS',
            self::BankTransfer  => 'Transfer Bank',
            self::Gopay         => 'GoPay',
            self::Manual        => 'Manual',
        };
    }

    /**
     * Get description.
     */
    public function description(): string
    {
        return match ($this) {
            self::Qris          => 'Scan QR code untuk pembayaran',
            self::BankTransfer  => 'Transfer ke rekening bank',
            self::Gopay         => 'Pembayaran via GoPay',
            self::Manual        => 'Pembayaran langsung ke kantor',
        };
    }

    /**
     * Get icon name for UI display.
     */
    public function icon(): string
    {
        return match ($this) {
            self::Qris          => 'qr-code',
            self::BankTransfer  => 'building-columns',
            self::Gopay         => 'wallet',
            self::Manual        => 'hand-coins',
        };
    }

    /**
     * Check if payment requires proof upload.
     */
    public function requiresProofUpload(): bool
    {
        return in_array($this, [self::Qris, self::BankTransfer, self::Gopay], true);
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
