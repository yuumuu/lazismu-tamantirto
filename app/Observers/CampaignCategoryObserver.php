<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\CampaignCategory;
use Illuminate\Support\Facades\Cache;

class CampaignCategoryObserver
{
    /**
     * Handle the CampaignCategory "created" event.
     */
    public function created(CampaignCategory $campaignCategory): void
    {
        $this->clearCache();
    }

    /**
     * Handle the CampaignCategory "updated" event.
     */
    public function updated(CampaignCategory $campaignCategory): void
    {
        $this->clearCache();
    }

    /**
     * Handle the CampaignCategory "deleted" event.
     */
    public function deleted(CampaignCategory $campaignCategory): void
    {
        $this->clearCache();
    }

    /**
     * Clear campaign categories cache.
     */
    private function clearCache(): void
    {
        Cache::forget('campaign_categories');
    }
}
