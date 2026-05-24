<?php

use App\Models\Masjid;
use App\Models\Donation;
use App\Models\Campaign;
use App\Models\Withdrawal;
use function Livewire\Volt\{state, computed, layout, mount};

layout('components.layouts.app');

mount(function () {
    if (auth()->user()->masjid_id !== 1) {
        abort(403, 'Akses Ditolak: Hanya admin pusat yang dapat mengakses fitur ini.');
    }
});

$masjids = computed(function () {
    return Masjid::withCount(['campaigns' => fn($q) => $q->active()])
        ->withSum(['donations' => fn($q) => $q->verified()], 'amount')
        ->withSum(['withdrawals' => fn($q) => $q->where('status', 'approved')], 'amount')
        ->orderBy('id')
        ->get();
});

$totalDonations = computed(fn () => Donation::verified()->sum('amount'));
$totalWithdrawals = computed(fn () => Withdrawal::where('status', 'approved')->sum('amount'));
$totalCampaigns = computed(fn () => Campaign::active()->count());

?>

<div class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Monitoring Cabang</h1>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <flux:card>
            <div class="flex items-center gap-4">
                <div class="p-3 bg-primary/10 text-primary rounded-xl">
                    <flux:icon name="banknotes" class="size-6" />
                </div>
                <div>
                    <p class="text-sm font-medium text-zinc-500">Total Donasi Keseluruhan</p>
                    <p class="text-2xl font-bold">Rp {{ number_format($this->totalDonations, 0, ',', '.') }}</p>
                </div>
            </div>
        </flux:card>
        
        <flux:card>
            <div class="flex items-center gap-4">
                <div class="p-3 bg-green-500/10 text-green-500 rounded-xl">
                    <flux:icon name="arrow-up-tray" class="size-6" />
                </div>
                <div>
                    <p class="text-sm font-medium text-zinc-500">Total Penyaluran Keseluruhan</p>
                    <p class="text-2xl font-bold">Rp {{ number_format($this->totalWithdrawals, 0, ',', '.') }}</p>
                </div>
            </div>
        </flux:card>

        <flux:card>
            <div class="flex items-center gap-4">
                <div class="p-3 bg-blue-500/10 text-blue-500 rounded-xl">
                    <flux:icon name="megaphone" class="size-6" />
                </div>
                <div>
                    <p class="text-sm font-medium text-zinc-500">Total Campaign Aktif</p>
                    <p class="text-2xl font-bold">{{ number_format($this->totalCampaigns) }}</p>
                </div>
            </div>
        </flux:card>
    </div>

    <!-- Table -->
    <flux:card>
        <div class="overflow-x-auto">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>Nama Cabang</flux:table.column>
                    <flux:table.column>Status</flux:table.column>
                    <flux:table.column>Campaign Aktif</flux:table.column>
                    <flux:table.column>Total Donasi</flux:table.column>
                    <flux:table.column>Total Penyaluran</flux:table.column>
                    <flux:table.column>Aksi</flux:table.column>
                </flux:table.columns>
                <flux:table.rows>
                    @foreach($this->masjids as $masjid)
                        <flux:table.row>
                            <flux:table.cell class="font-medium text-zinc-900 dark:text-white">
                                {{ $masjid->name }}
                                @if($masjid->id === 1)
                                    <flux:badge size="sm" color="primary" class="ml-2">Pusat</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($masjid->is_active)
                                    <flux:badge size="sm" color="success">Aktif</flux:badge>
                                @else
                                    <flux:badge size="sm" color="danger">Nonaktif</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>{{ number_format($masjid->campaigns_count) }}</flux:table.cell>
                            <flux:table.cell>Rp {{ number_format($masjid->donations_sum_amount ?? 0, 0, ',', '.') }}</flux:table.cell>
                            <flux:table.cell>Rp {{ number_format($masjid->withdrawals_sum_amount ?? 0, 0, ',', '.') }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:button size="sm" variant="subtle" href="{{ route('admin.monitoring.show', $masjid->id) }}" wire:navigate>
                                    View
                                </flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
        </div>
    </flux:card>
</div>
