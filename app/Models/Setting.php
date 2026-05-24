<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToMasjid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use BelongsToMasjid, HasFactory;

    protected $fillable = [
        'masjid_id',
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
            'number' => (int) $this->value,
            'json' => json_decode($this->value, true) ?? [],
            'image' => $this->value ? asset('storage/'.$this->value) : null,
            default => $this->value,
        };
    }

    // ==================== STATIC METHODS ====================

    protected static function getCacheKey(string $key): string
    {
        $masjidId = session('active_masjid_id', 1);

        return "masjid_{$masjidId}_setting_{$key}";
    }

    /**
     * Get a setting value by key.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $masjidId = session('active_masjid_id', 1);
        $cacheKey = "masjid_{$masjidId}_setting_{$key}";

        $setting = Cache::remember(
            $cacheKey,
            now()->addHours(24),
            function () use ($key, $masjidId) {
                // Try current masjid first
                $found = static::withoutGlobalScope('masjid')
                    ->where('masjid_id', $masjidId)
                    ->where('key', $key)
                    ->first();

                // Fallback to Pusat (ID 1) if not found and current is not Pusat
                if (! $found && $masjidId !== 1) {
                    $found = static::withoutGlobalScope('masjid')
                        ->where('masjid_id', 1)
                        ->where('key', $key)
                        ->first();
                }

                return $found;
            }
        );

        return $setting?->typed_value ?? $default;
    }

    /**
     * Set a setting value by key.
     */
    public static function setValue(string $key, mixed $value): void
    {
        $masjidId = session('active_masjid_id', 1);

        // Look up existing setting for this masjid, or fallback to Pusat for metadata
        $existing = static::withoutGlobalScope('masjid')
            ->where('key', $key)
            ->where('masjid_id', $masjidId)
            ->first();

        if (! $existing && $masjidId !== 1) {
            $existing = static::withoutGlobalScope('masjid')
                ->where('key', $key)
                ->where('masjid_id', 1)
                ->first();
        }

        $attributes = [
            'value' => is_array($value) ? json_encode($value) : $value,
        ];

        if ($existing) {
            $attributes['type'] = $existing->type;
            $attributes['group_name'] = $existing->group_name;
            $attributes['label'] = $existing->label;
            $attributes['description'] = $existing->description;
            $attributes['is_public'] = $existing->is_public;
        } else {
            // Sensible defaults if Pusat setting hasn't been seeded yet
            $attributes['type'] = is_array($value) ? 'json' : 'text';
            $attributes['group_name'] = 'general';
            $attributes['label'] = ucwords(str_replace('_', ' ', $key));
            $attributes['description'] = null;
            $attributes['is_public'] = true;
        }

        static::withoutGlobalScope('masjid')->updateOrCreate(
            ['masjid_id' => $masjidId, 'key' => $key],
            $attributes
        );

        Cache::forget("masjid_{$masjidId}_setting_{$key}");
        Cache::flush();
    }

    /**
     * Get all settings in a group.
     */
    public static function getGroup(string $group): array
    {
        $masjidId = session('active_masjid_id', 1);

        return Cache::remember(
            "masjid_{$masjidId}_settings_group_{$group}",
            now()->addHours(24),
            function () use ($group, $masjidId) {
                // Get Pusat defaults
                $pusatSettings = static::withoutGlobalScope('masjid')
                    ->where('masjid_id', 1)
                    ->byGroup($group)
                    ->get()
                    ->mapWithKeys(fn ($setting) => [$setting->key => $setting->typed_value])
                    ->toArray();

                // If tenant is not Pusat, get and merge tenant overrides
                if ($masjidId !== 1) {
                    $tenantSettings = static::withoutGlobalScope('masjid')
                        ->where('masjid_id', $masjidId)
                        ->byGroup($group)
                        ->get()
                        ->mapWithKeys(fn ($setting) => [$setting->key => $setting->typed_value])
                        ->toArray();

                    return array_merge($pusatSettings, $tenantSettings);
                }

                return $pusatSettings;
            }
        );
    }

    /**
     * Get all public settings.
     */
    public static function getPublic(): array
    {
        $masjidId = session('active_masjid_id', 1);

        return Cache::remember(
            "masjid_{$masjidId}_settings_public",
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
        $masjidId = session('active_masjid_id', 1);
        $keys = static::query()->pluck('key');

        foreach ($keys as $key) {
            Cache::forget("masjid_{$masjidId}_setting_{$key}");
        }

        $groups = static::distinct()->pluck('group_name');

        foreach ($groups as $group) {
            Cache::forget("masjid_{$masjidId}_settings_group_{$group}");
        }

        Cache::forget("masjid_{$masjidId}_settings_public");
    }
}
