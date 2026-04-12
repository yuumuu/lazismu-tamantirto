<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\CampaignCategory;
use App\Models\Donation;
use App\Models\Setting;
use App\Models\User;
use App\Observers\CampaignCategoryObserver;
use App\Observers\DonationObserver;
use App\Observers\SettingObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register observers
        User::observe(UserObserver::class);
        Setting::observe(SettingObserver::class);
        CampaignCategory::observe(CampaignCategoryObserver::class);
        Donation::observe(DonationObserver::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
