<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\BelongsToBranch;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use BelongsToBranch, HasFactory;

    protected $fillable = [
        'branch_id',
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
        $branchId = session('active_branch_id', 1);

        return "branch_{$branchId}_setting_{$key}";
    }

    /**
     * Get a setting value by key.
     */
    public static function getValue(string $key, mixed $default = null): mixed
    {
        $branchId = session('active_branch_id', 1);
        $cacheKey = "branch_{$branchId}_setting_{$key}";

        $setting = Cache::remember(
            $cacheKey,
            now()->addHours(24),
            function () use ($key, $branchId) {
                // Try current branch first
                $found = static::withoutGlobalScope('branch')
                    ->where('branch_id', $branchId)
                    ->where('key', $key)
                    ->first();

                // Fallback to Pusat (ID 1) if not found and current is not Pusat
                if (! $found && $branchId !== 1) {
                    $found = static::withoutGlobalScope('branch')
                        ->where('branch_id', 1)
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
        $branchId = session('active_branch_id', 1);

        // Look up existing setting for this branch, or fallback to Pusat for metadata
        $existing = static::withoutGlobalScope('branch')
            ->where('key', $key)
            ->where('branch_id', $branchId)
            ->first();

        if (! $existing && $branchId !== 1) {
            $existing = static::withoutGlobalScope('branch')
                ->where('key', $key)
                ->where('branch_id', 1)
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

        static::withoutGlobalScope('branch')->updateOrCreate(
            ['branch_id' => $branchId, 'key' => $key],
            $attributes
        );

        Cache::forget("branch_{$branchId}_setting_{$key}");
        Cache::flush();
    }

    /**
     * Get all settings in a group.
     */
    public static function getGroup(string $group): array
    {
        $branchId = session('active_branch_id', 1);

        return Cache::remember(
            "branch_{$branchId}_settings_group_{$group}",
            now()->addHours(24),
            function () use ($group, $branchId) {
                // Get Pusat defaults
                $pusatSettings = static::withoutGlobalScope('branch')
                    ->where('branch_id', 1)
                    ->byGroup($group)
                    ->get()
                    ->mapWithKeys(fn ($setting) => [$setting->key => $setting->typed_value])
                    ->toArray();

                // If tenant is not Pusat, get and merge tenant overrides
                if ($branchId !== 1) {
                    $tenantSettings = static::withoutGlobalScope('branch')
                        ->where('branch_id', $branchId)
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
        $branchId = session('active_branch_id', 1);

        return Cache::remember(
            "branch_{$branchId}_settings_public",
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
        $branchId = session('active_branch_id', 1);
        $keys = static::query()->pluck('key');

        foreach ($keys as $key) {
            Cache::forget("branch_{$branchId}_setting_{$key}");
        }

        $groups = static::distinct()->pluck('group_name');

        foreach ($groups as $group) {
            Cache::forget("branch_{$branchId}_settings_group_{$group}");
        }

        Cache::forget("branch_{$branchId}_settings_public");
    }
}
