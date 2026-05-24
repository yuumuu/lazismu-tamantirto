<?php

use App\Models\Branch;
use App\Models\Donation;
use App\Models\Campaign;
use function Livewire\Volt\{state, computed, layout, mount};

layout('components.layouts.app');

state(['branch_id' => null]);

mount(function ($branch) {
    if (auth()->user()->branch_id !== 1) {
        abort(403, 'Akses Ditolak: Hanya admin pusat yang dapat mengakses fitur ini.');
    }
    
    $this->branch_id = $branch;
});

$branchData = computed(function () {
    return Branch::findOrFail($this->branch_id);
});

$recentDonations = computed(function () {
    return Donation::where('branch_id', $this->branch_id)
        ->with('campaign')
        ->latest()
        ->take(10)
        ->get();
});

$activeCampaigns = computed(function () {
    return Campaign::where('branch_id', $this->branch_id)
        ->active()
        ->withCount('verifiedDonations')
        ->withSum('verifiedDonations', 'amount')
        ->latest()
        ->take(5)
        ->get();
});

?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <flux:button variant="ghost" icon="arrow-left" href="{{ route('admin.monitoring.index') }}" wire:navigate />
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">
                Detail Monitoring: {{ $this->branchData->name }}
            </h1>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Donations -->
        <flux:card>
            <flux:heading size="lg" class="mb-4">Donasi Terbaru</flux:heading>
            
            <div class="space-y-4">
                @forelse($this->recentDonations as $donation)
                    <div class="flex items-center justify-between p-4 bg-zinc-50 dark:bg-zinc-900/50 rounded-xl border border-zinc-100 dark:border-white/5">
                        <div>
                            <p class="font-bold text-zinc-900 dark:text-white">{{ $donation->is_anonymous ? 'Hamba Allah' : $donation->donor_name }}</p>
                            <p class="text-sm text-zinc-500">{{ $donation->campaign->title ?? 'Donasi Umum' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-primary">Rp {{ number_format($donation->amount, 0, ',', '.') }}</p>
                            <p class="text-xs text-zinc-500">{{ $donation->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <p class="text-zinc-500 text-center py-4">Belum ada donasi.</p>
                @endforelse
            </div>
        </flux:card>

        <!-- Active Campaigns -->
        <flux:card>
            <flux:heading size="lg" class="mb-4">Campaign Aktif</flux:heading>
            
            <div class="space-y-4">
                @forelse($this->activeCampaigns as $campaign)
                    <div class="flex items-center justify-between p-4 bg-zinc-50 dark:bg-zinc-900/50 rounded-xl border border-zinc-100 dark:border-white/5">
                        <div class="flex-1">
                            <p class="font-bold text-zinc-900 dark:text-white line-clamp-1">{{ $campaign->title }}</p>
                            <div class="flex items-center gap-4 mt-1">
                                <span class="text-xs text-zinc-500">{{ number_format($campaign->verified_donations_count) }} Donatur</span>
                                <span class="text-xs text-green-600 font-medium">Terkumpul: Rp {{ number_format($campaign->verified_donations_sum_amount ?? 0, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <p class="text-zinc-500 text-center py-4">Tidak ada campaign aktif.</p>
                @endforelse
            </div>
        </flux:card>
    </div>
</div>
