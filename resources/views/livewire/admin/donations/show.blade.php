<?php

use App\Models\Donation;
use App\Enums\DonationStatus;
use App\Services\Donation\DonationVerificationService;
use Livewire\Volt\Component;

new class extends Component {
    public Donation $donation;
    public $notes = '';
    public $action = 'verify'; // verify or reject

    public function mount(Donation $donation): void
    {
        $this->donation = $donation;
    }

    public function process(DonationVerificationService $verificationService): void
    {
        if ($this->action === 'verify') {
            $result = $verificationService->verify($this->donation, auth()->user(), $this->notes);
        } else {
            $this->validate(['notes' => 'required|min:5']);
            $result = $verificationService->reject($this->donation, auth()->user(), $this->notes);
        }

        if ($result->isSuccess()) {
            $this->dispatch('notify', message: $result->getMessage(), type: 'success');
            $this->redirect(route('admin.donations.index'), navigate: true);
        } else {
            $this->dispatch('notify', message: $result->getMessage(), type: 'error');
        }
    }
} ?>

<div class="p-3 md:p-6 lg:p-10 space-y-8">
    <div class="flex items-center gap-4">
        <flux:button variant="ghost" icon="arrow-left" :href="route('admin.donations.index')" wire:navigate />
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ __('Detail Donasi') }}</h1>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm mt-1">{{ __('Verifikasi bukti pembayaran dan status transaksi.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-zinc-900 p-8 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest">{{ __('Informasi Donatur') }}</h3>
                        <div class="space-y-1">
                            <p class="text-lg font-bold text-zinc-900 dark:text-white">{{ $donation->donor_name }}</p>
                            <p class="text-sm text-zinc-500">{{ $donation->donor_email }}</p>
                            <p class="text-sm text-zinc-500">{{ format_phone($donation->donor_phone) }}</p>
                            @if($donation->muzakki_id)
                                <div class="mt-3 flex items-center gap-2">
                                    <flux:badge color="amber" size="sm">Muzakki Terdaftar</flux:badge>
                                    <flux:button variant="ghost" size="xs" icon="user" :href="route('admin.muzakkis.edit', $donation->muzakki_id)" wire:navigate>Profil</flux:button>
                                </div>
                            @endif
                            @if($donation->is_anonymous)
                                <flux:badge color="zinc" size="sm" class="mt-2">Hamba Allah (Anonim)</flux:badge>
                            @endif
                        </div>
                    </div>
                    <div class="space-y-4">
                        <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest">{{ __('Informasi Transaksi') }}</h3>
                        <div class="space-y-1">
                            <p class="text-lg font-bold text-amber-500">{{ $donation->formatted_amount }}</p>
                            <p class="text-sm text-zinc-500">ID: <span class="font-mono">{{ $donation->transaction_id }}</span></p>
                            <p class="text-sm text-zinc-500">{{ $donation->created_at->format('d F Y, H:i') }}</p>
                            <flux:badge :color="$donation->status->color()" class="mt-2 text-xs">
                                {{ $donation->status->label() }}
                            </flux:badge>
                        </div>
                    </div>
                </div>

                <div class="pt-8 border-t border-zinc-100 dark:border-zinc-800 space-y-4">
                    <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest">{{ __('Program / Campaign') }}</h3>
                    <div class="p-4 bg-zinc-50 dark:bg-zinc-950 rounded-2xl border border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <flux:icon name="folder" class="size-5 text-zinc-400" />
                            <span class="font-medium text-zinc-900 dark:text-white">{{ $donation->campaign?->title ?? 'Umum / Infaq Terikat' }}</span>
                        </div>
                        @if($donation->campaign)
                            <flux:button variant="ghost" size="sm" icon="eye" :href="route('admin.campaigns.edit', $donation->campaign)" wire:navigate />
                        @endif
                    </div>
                </div>

                @if($donation->donor_message)
                <div class="pt-8 border-t border-zinc-100 dark:border-zinc-800 space-y-4">
                    <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest">{{ __('Pesan Donatur') }}</h3>
                    <div class="p-4 bg-amber-50 dark:bg-amber-900/10 rounded-2xl text-zinc-700 dark:text-zinc-300 italic">
                        "{{ $donation->donor_message }}"
                    </div>
                </div>
                @endif
            </div>

            <!-- Suspicious Alert -->
            @if($donation->is_suspicious)
            <div class="p-6 bg-red-50 dark:bg-red-900/20 border border-red-100 dark:border-red-900/50 rounded-[24px_0_24px_0] flex gap-4">
                <flux:icon name="exclamation-triangle" class="size-6 text-red-500 flex-shrink-0" />
                <div class="space-y-1">
                    <h4 class="font-bold text-red-700 dark:text-red-400">{{ __('Deteksi Transaksi Mencurigakan') }}</h4>
                    <p class="text-sm text-red-600 dark:text-red-300">{{ $donation->suspicious_reason }}</p>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar Verification -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-zinc-900 p-6 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                <h3 class="font-bold text-zinc-900 dark:text-white">{{ __('Bukti Pembayaran') }}</h3>
                
                <div class="aspect-[3/4] rounded-2xl bg-zinc-100 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 overflow-hidden group relative">
                    <img src="{{ $donation->proof_image_url }}" class="size-full object-contain">
                    <a href="{{ $donation->proof_image_url }}" target="_blank" class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-sm font-bold">
                        {{ __('Buka Gambar Penuh') }}
                    </a>
                </div>

                @if($donation->canBeVerified())
                <div class="pt-6 border-t border-zinc-100 dark:border-zinc-800 space-y-4">
                    <flux:radio.group wire:model="action" label="Tindakan">
                        <flux:radio value="verify" label="Verifikasi (Terima)" color="green" />
                        <flux:radio value="reject" label="Tolak" color="red" />
                    </flux:radio.group>

                    <flux:textarea wire:model="notes" :label="$action === 'verify' ? 'Catatan (Opsional)' : 'Alasan Penolakan'" placeholder="Ketik di sini..." rows="3" />

                    <flux:button wire:click="process" :variant="$action === 'verify' ? 'primary' : 'danger'" class="w-full rounded-[16px_0_16px_0]">
                        {{ $action === 'verify' ? 'Konfirmasi Verifikasi' : 'Proses Penolakan' }}
                    </flux:button>
                </div>
                @else
                <div class="pt-6 border-t border-zinc-100 dark:border-zinc-800 text-center text-zinc-500 text-sm italic">
                    {{ __('Transaksi ini sudah diproses pada ' . ($donation->verified_at?->format('d/m/Y') ?? '-')) }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
