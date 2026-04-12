<?php

declare(strict_types=1);

use App\Models\BlogCategory;
use App\Models\Campaign;
use App\Models\CampaignCategory;
use App\Models\Donation;
use App\Models\Muzakki;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

if (! function_exists('cached_settings')) {
    /**
     * Get a cached setting value.
     */
    function cached_settings(string $key, mixed $default = null): mixed
    {
        return Cache::remember("settings.{$key}", 3600, function () use ($key, $default) {
            return Setting::where('key', $key)->value('value') ?? $default;
        });
    }
}

if (! function_exists('cached_settings_group')) {
    /**
     * Get all cached settings in a group.
     */
    function cached_settings_group(string $group): array
    {
        return Cache::remember("settings.group.{$group}", 3600, function () use ($group) {
            return Setting::where('group_name', $group)
                ->pluck('value', 'key')
                ->toArray();
        });
    }
}

if (! function_exists('cached_campaign_categories')) {
    /**
     * Get all active campaign categories (cached for 1 hour).
     */
    function cached_campaign_categories()
    {
        return Cache::remember('campaign_categories', 3600, function () {
            return CampaignCategory::active()->ordered()->get();
        });
    }
}

if (! function_exists('cached_blog_categories')) {
    /**
     * Get all active blog categories (cached for 1 hour).
     */
    function cached_blog_categories()
    {
        return Cache::remember('blog_categories', 3600, function () {
            return BlogCategory::active()->ordered()->get();
        });
    }
}

if (! function_exists('cached_guest_stats')) {
    /**
     * Get guest homepage stats (cached for 5 minutes).
     */
    function cached_guest_stats(): array
    {
        return Cache::remember('guest_stats', 300, function () {
            return [
                'totalMuzakki' => Muzakki::count(),
                'totalDana' => Donation::verified()->sum('amount'),
                'activePrograms' => Campaign::active()->count(),
            ];
        });
    }
}

if (! function_exists('cached_donation_stats')) {
    /**
     * Get donation statistics (cached for 5 minutes).
     */
    function cached_donation_stats(): array
    {
        return Cache::remember('donation_stats', 300, function () {
            return Donation::query()
                ->selectRaw('
                    COUNT(CASE WHEN status IN ("pending", "pending_manual") THEN 1 END) as pending,
                    COUNT(CASE WHEN is_suspicious = 1 THEN 1 END) as suspicious,
                    COUNT(CASE WHEN status = "verified" THEN 1 END) as verified,
                    COUNT(CASE WHEN status = "rejected" THEN 1 END) as rejected
                ')
                ->first()
                ->toArray();
        });
    }
}

if (! function_exists('clear_settings_cache')) {
    /**
     * Clear all settings cache.
     */
    function clear_settings_cache(): void
    {
        Cache::forget('settings.*');
        Cache::tags(['settings'])->flush();
    }
}

if (! function_exists('clear_categories_cache')) {
    /**
     * Clear all categories cache.
     */
    function clear_categories_cache(): void
    {
        Cache::forget('campaign_categories');
        Cache::forget('blog_categories');
    }
}

if (! function_exists('clear_stats_cache')) {
    /**
     * Clear all statistics cache.
     */
    function clear_stats_cache(): void
    {
        Cache::forget('guest_stats');
        Cache::forget('donation_stats');
    }
}
