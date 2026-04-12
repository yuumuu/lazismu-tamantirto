<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingObserver
{
    /**
     * Handle the Setting "created" event.
     */
    public function created(Setting $setting): void
    {
        $this->clearCache($setting);
    }

    /**
     * Handle the Setting "updated" event.
     */
    public function updated(Setting $setting): void
    {
        $this->clearCache($setting);
    }

    /**
     * Handle the Setting "deleted" event.
     */
    public function deleted(Setting $setting): void
    {
        $this->clearCache($setting);
    }

    /**
     * Clear settings cache.
     */
    private function clearCache(Setting $setting): void
    {
        // Clear specific setting cache
        Cache::forget("settings.{$setting->key}");

        // Clear group cache if exists
        if ($setting->group_name) {
            Cache::forget("settings.group.{$setting->group_name}");
        }
    }
}
