<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group_name',
        'label',
        'description',
        'is_public',
    ];

    protected function casts(): array
    {
        return [
            'is_public' => 'boolean',
        ];
    }

    // ==================== SCOPES ====================

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeByGroup($query, string $group)
    {
        return $query->where('group_name', $group);
    }

    // ==================== ACCESSORS ====================

    public function getTypedValueAttribute(): mixed
    {
        return match ($this->type) {
            'boolean' => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            'number'  => (int) $this->value,
            'json'    => json_decode($this->value, true) ?? [],
            'image'   => $this->value ? asset('storage/'.$this->value) : null,
            default   => $this->value,
        };
    }

    // ==================== STATIC METHODS ====================

    /**
     * Get a setting value by key.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $setting = Cache::remember(
            "setting_{$key}",
            now()->addHours(24),
            fn () => static::where('key', $key)->first()
        );

        return $setting?->typed_value ?? $default;
    }

    /**
     * Set a setting value by key.
     */
    public static function setValue(string $key, mixed $value): void
    {
        $setting = static::where('key', $key)->first();

        if ($setting) {
            $setting->update(['value' => is_array($value) ? json_encode($value) : $value]);
            Cache::forget("setting_{$key}");
        }
    }

    /**
     * Get all settings in a group.
     */
    public static function getGroup(string $group): array
    {
        return Cache::remember(
            "settings_group_{$group}",
            now()->addHours(24),
            fn () => static::byGroup($group)
                ->get()
                ->mapWithKeys(fn ($setting) => [$setting->key => $setting->typed_value])
                ->toArray()
        );
    }

    /**
     * Get all public settings.
     */
    public static function getPublic(): array
    {
        return Cache::remember(
            'settings_public',
            now()->addHours(24),
            fn () => static::public()
                ->get()
                ->mapWithKeys(fn ($setting) => [$setting->key => $setting->typed_value])
                ->toArray()
        );
    }

    /**
     * Clear all settings cache.
     */
    public static function clearCache(): void
    {
        $keys = static::pluck('key');

        foreach ($keys as $key) {
            Cache::forget("setting_{$key}");
        }

        $groups = static::distinct()->pluck('group_name');

        foreach ($groups as $group) {
            Cache::forget("settings_group_{$group}");
        }

        Cache::forget('settings_public');
    }
}
