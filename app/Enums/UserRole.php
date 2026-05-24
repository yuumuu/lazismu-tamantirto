<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * User roles for RBAC.
 */
enum UserRole: string
{
    case SuperAdmin = 'super_admin';
    case Admin = 'admin';
    case Editor = 'editor';
    case Viewer = 'viewer';

    /**
     * Get human-readable label.
     */
    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::Admin => 'Admin',
            self::Editor => 'Editor',
            self::Viewer => 'Viewer',
        };
    }

    /**
     * Get description of role capabilities.
     */
    public function description(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Akses penuh ke seluruh sistem',
            self::Admin => 'Kelola konten, donasi, dan pengguna',
            self::Editor => 'Kelola konten dan artikel',
            self::Viewer => 'Lihat data tanpa bisa mengedit',
        };
    }

    /**
     * Get color for UI badge.
     */
    public function color(): string
    {
        return match ($this) {
            self::SuperAdmin => 'red',
            self::Admin => 'violet',
            self::Editor => 'sky',
            self::Viewer => 'zinc',
        };
    }

    /**
     * Get role hierarchy level (lower = more powerful).
     */
    public function level(): int
    {
        return match ($this) {
            self::SuperAdmin => 1,
            self::Admin => 2,
            self::Editor => 3,
            self::Viewer => 4,
        };
    }

    /**
     * Check if role can manage users.
     */
    public function canManageUsers(): bool
    {
        return in_array($this, [self::SuperAdmin, self::Admin], true);
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
