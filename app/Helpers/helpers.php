<?php

declare(strict_types=1);

use App\Models\Setting;

if (! function_exists('setting')) {
    /**
     * Get a setting value by key.
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return Setting::getValue($key, $default);
    }
}

if (! function_exists('settings')) {
    /**
     * Get all settings in a group.
     */
    function settings(string $group): array
    {
        return Setting::getGroup($group);
    }
}

if (! function_exists('format_phone')) {
    /**
     * Format phone number for display.
     */
    function format_phone(string $phone): string
    {
        // Remove non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Format as Indonesian number
        if (str_starts_with($phone, '62')) {
            $phone = '0'.substr($phone, 2);
        } elseif (str_starts_with($phone, '+62')) {
            $phone = '0'.substr($phone, 3);
        }

        // Add dashes for readability
        if (strlen($phone) >= 10) {
            return substr($phone, 0, 4).'-'.substr($phone, 4, 4).'-'.substr($phone, 8);
        }

        return $phone;
    }
}

if (! function_exists('generate_whatsapp_url')) {
    /**
     * Generate WhatsApp API URL.
     */
    function generate_whatsapp_url(string $phone, string $message = ''): string
    {
        // Normalize phone number
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '0')) {
            $phone = '62'.substr($phone, 1);
        }

        $url = "https://wa.me/{$phone}";

        if ($message) {
            $url .= '?text='.urlencode($message);
        }

        return $url;
    }
}

if (! function_exists('format_rupiah')) {
    /**
     * Format number as Indonesian Rupiah.
     */
    function format_rupiah(int|float|string $amount, bool $withPrefix = true): string
    {
        if (is_numeric($amount)) {
            $formatted = number_format((float) $amount, 0, ',', '.');
        } else {
            $formatted = (string) $amount;
        }

        return $withPrefix ? 'Rp ' . $formatted : $formatted;
    }
}


if (! function_exists('format_rupiah_short')) {

    /**
     * Format number as short Indonesian Rupiah (Rp 1.2K, Rp 3.5M, Rp 1B)
     */
    function format_rupiah_short(int|float $amount, $prefix = true): string
    {
        $format = fn ($v) =>
            rtrim(rtrim(number_format($v, 2, '.', ''), '0'), '.');

        if ($amount >= 1_000_000_000) {
            return format_rupiah($format($amount / 1_000_000_000) . 'B', $prefix);
        }

        if ($amount >= 1_000_000) {
            return format_rupiah($format($amount / 1_000_000) . 'M', $prefix);
        }

        if ($amount >= 1_000) {
            return format_rupiah($format($amount / 1_000) . 'K', $prefix);
        }

        return format_rupiah($amount);
    }
}
