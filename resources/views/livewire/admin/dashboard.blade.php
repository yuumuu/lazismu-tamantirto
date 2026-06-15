<?php

use App\Models\Campaign;
use App\Models\Donation;
use App\Services\Donation\DonationService;
use Livewire\Volt\Component;

new class extends Component {
    public array $stats = [];
    public $recentDonations = [];
    public $topCampaigns = [];

    public function boot(DonationService $donationService): void
    {
        $this->stats = $donationService->getStatistics();

        // Add more stats
        $this->stats['active_campaigns'] = Campaign::active()->count();
        $this->stats['total_penerima'] = 0; // Placeholder for now

        $this->recentDonations = Donation::query()
            ->with(['campaign'])
            ->latest()
            ->limit(5)
            ->get();

        $this->topCampaigns = Campaign::active()
            ->orderByDesc('current_amount')
            ->limit(3)
            ->get();
    }
} ?>

@push('head')
    <title>Dashboard - {{ ucwords(config('app.name')) }}</title>
@endpush

<div class="p-3 md:p-6 lg:p-10 space-y-10">
    <!-- Header Misi: Minimalist & Clean -->
    <div class="flex flex-col md:flex-row items-start justify-between gap-8">
        <div class="space-y-4 max-w-2xl">
            <div class="flex items-center gap-3">
                <div class="h-8 w-1 bg-primary rounded-full"></div>
                <h1 class="text-3xl md:text-4xl font-black tracking-tight text-zinc-900 dark:text-white leading-tight">
                    {{ __('Memberi Makna pada Rupiah.') }}
                </h1>
            </div>
            <p class="text-zinc-500 text-lg font-medium pl-4 border-l border-zinc-200 dark:border-zinc-800">
                {{ __('Selamat datang, :name. Pantau amanah donatur hari ini.', ['name' => auth()->user()->name]) }}
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <flux:button icon="document-text" :href="route('admin.reports.index')" wire:navigate variant="subtle">
                {{ __('Laporan') }}
            </flux:button>
            <flux:button variant="primary" icon="plus" :href="route('admin.campaigns.create')" wire:navigate>
                {{ __('Campaign Baru') }}
            </flux:button>
        </div>
    </div>

    <!-- Metrik Utama: Minimalist Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6" data-tour="dashboard-stats">
        <div class="premium-card p-6 group hover:border-primary/50 transition-colors">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-zinc-50 dark:bg-zinc-800 rounded-lg text-zinc-400 group-hover:text-primary transition-colors">
                    <flux:icon name="banknotes" class="size-6" />
                </div>
                <flux:badge color="green" size="sm" inset="top bottom">+12%</flux:badge>
            </div>
            <p class="text-sm font-medium text-zinc-500 mb-1">{{ __('Dana Terhimpun') }}</p>
            <h3 class="text-3xl font-black text-zinc-900 dark:text-white tracking-tight">{{ format_rupiah_short($stats['total_verified'] ?? 0) }}</h3>
        </div>

        <div class="premium-card p-6 group hover:border-primary/50 transition-colors">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-zinc-50 dark:bg-zinc-800 rounded-lg text-zinc-400 group-hover:text-primary transition-colors">
                    <flux:icon name="users" class="size-6" />
                </div>
            </div>
            <p class="text-sm font-medium text-zinc-500 mb-1">{{ __('Donatur Aktif') }}</p>
            <h3 class="text-3xl font-black text-zinc-900 dark:text-white tracking-tight">{{ number_format($stats['total_donors'] ?? 0) }}</h3>
        </div>

        <div class="premium-card p-6 group hover:border-primary/50 transition-colors">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-zinc-50 dark:bg-zinc-800 rounded-lg text-zinc-400 group-hover:text-primary transition-colors">
                    <flux:icon name="sparkles" class="size-6" />
                </div>
            </div>
            <p class="text-sm font-medium text-zinc-500 mb-1">{{ __('Program Aktif') }}</p>
            <h3 class="text-3xl font-black text-zinc-900 dark:text-white tracking-tight">{{ $stats['active_campaigns'] }}</h3>
        </div>

        <div class="premium-card p-6 group hover:border-red-200 transition-colors">
            <div class="flex justify-between items-start mb-4">
                <div class="p-2 bg-zinc-50 dark:bg-zinc-800 rounded-lg text-zinc-400 group-hover:text-red-500 transition-colors">
                    <flux:icon name="clock" class="size-6" />
                </div>
                @if(($stats['total_pending'] ?? 0) > 0)
                <flux:badge color="red" size="sm" inset="top bottom">Mendesak</flux:badge>
                @endif
            </div>
            <p class="text-sm font-medium text-zinc-500 mb-1">{{ __('Butuh Verifikasi') }}</p>
            <h3 class="text-3xl font-black {{ ($stats['total_pending'] ?? 0) > 0 ? 'text-red-600' : 'text-zinc-900 dark:text-white' }} tracking-tight">{{ number_format($stats['total_pending'] ?? 0) }}</h3>
        </div>
    </div>

    <!-- Analitik & Aktivitas -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6" data-tour="recent-donations">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-zinc-900 dark:text-white">{{ __('Aktivitas Terakhir') }}</h2>
                <flux:button variant="ghost" size="sm" :href="route('admin.donations.index')" wire:navigate>{{ __('Lihat Semua') }}</flux:button>
            </div>

            <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-xs">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-zinc-50 dark:bg-zinc-900/50 text-zinc-500 font-medium border-b border-zinc-200 dark:border-zinc-800">
                            <tr>
                                <th class="px-6 py-3">{{ __('Donatur') }}</th>
                                <th class="px-6 py-3">{{ __('Program') }}</th>
                                <th class="px-6 py-3">{{ __('Nominal') }}</th>
                                <th class="px-6 py-3 text-right">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                            @forelse($recentDonations as $donation)
                            <tr class="group hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="size-8 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center font-bold text-zinc-500 text-xs group-hover:bg-primary/10 group-hover:text-primary transition-colors">
                                            {{ substr($donation->donor_name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-medium text-zinc-900 dark:text-white">{{ $donation->donor_name }}</div>
                                            <div class="text-xs text-zinc-500">{{ $donation->created_at->diffForHumans() }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-3">
                                    <span class="text-zinc-600 dark:text-zinc-400 text-sm truncate block max-w-[200px]">{{ $donation->campaign?->title ?? 'Dana Umum' }}</span>
                                </td>
                                <td class="px-6 py-3">
                                    <span class="font-semibold text-zinc-900 dark:text-white">{{ $donation->formatted_amount }}</span>
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <flux:badge :color="$donation->status->color()" size="sm" inset="top bottom">{{ $donation->status->label() }}</flux:badge>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-zinc-400 italic">Belum ada aktivitas.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Fokus Program -->
        <div class="space-y-6">
            <h2 class="text-lg font-bold text-zinc-900 dark:text-white">{{ __('Fokus Campaign') }}</h2>

            <div class="flex flex-col gap-4">
                @forelse($topCampaigns as $campaign)
                <div class="bg-white dark:bg-zinc-900 p-5 border border-zinc-200 dark:border-zinc-800 rounded-xl shadow-xs hover:shadow-md transition-all group">
                    <div class="flex justify-between items-start mb-3">
                        <h4 class="font-semibold text-zinc-900 dark:text-white line-clamp-2 text-sm leading-snug">{{ $campaign->title }}</h4>
                        <flux:button icon="chevron-right" size="xs" variant="ghost" :href="route('admin.campaigns.edit', $campaign)" wire:navigate class="-mt-1 -mr-2" />
                    </div>

                    <div class="space-y-2">
                        <div class="flex justify-between text-xs font-medium text-zinc-500 uppercase tracking-wider">
                            <span>{{ $campaign->progress_percentage }}%</span>
                            <span>{{ format_rupiah_short($campaign->current_amount) }}</span>
                        </div>
                        <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-primary h-full rounded-full transition-all duration-1000 ease-out" style="width: {{ $campaign->progress_percentage }}%"></div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="py-12 text-center bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-dashed border-zinc-200 dark:border-zinc-800 text-zinc-400 text-sm">Belum ada campaign aktif.</div>
                @endforelse
            </div>

            <!-- Ringkasan Penyaluran -->
            <div class="bg-zinc-900 dark:bg-zinc-900/50 p-6 rounded-xl border border-zinc-200 dark:border-zinc-800 space-y-3">
                <div class="flex items-center gap-2 text-white font-bold">
                    <flux:icon name="arrow-up-tray" class="size-4 text-primary" />
                    <span>Penyaluran Bulan Ini</span>
                </div>
                <p class="text-2xl font-black text-white tracking-tight">{{ format_rupiah_short(App\Models\Withdrawal::whereMonth('date', now()->month)->sum('amount')) }}</p>
                <flux:button variant="outline" size="sm" :href="route('admin.withdrawals.index')" wire:navigate class="w-full justify-center">{{ __('Kelola Penyaluran') }}</flux:button>
            </div>
        </div>
    </div>
</div>
