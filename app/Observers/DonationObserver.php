<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Donation;
use Illuminate\Support\Facades\Cache;

class DonationObserver
{
    /**
     * Handle the Donation "created" event.
     */
    public function created(Donation $donation): void
    {
        $this->clearStatsCache();
    }

    /**
     * Handle the Donation "updated" event.
     */
    public function updated(Donation $donation): void
    {
        // Clear cache if status changed
        if ($donation->isDirty('status')) {
            $this->clearStatsCache();
        }
    }

    /**
     * Handle the Donation "deleted" event.
     */
    public function deleted(Donation $donation): void
    {
        $this->clearStatsCache();
    }

    /**
     * Clear donation statistics cache.
     */
    private function clearStatsCache(): void
    {
        Cache::forget('guest_stats');
        Cache::forget('donation_stats');
    }
}
