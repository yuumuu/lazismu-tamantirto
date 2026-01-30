<?php

use App\Models\Donation;
use App\Models\Campaign;
use App\Models\Muzakki;
use App\Enums\CampaignType;
use App\Enums\DonationStatus;
use App\Enums\PaymentMethod;
use App\Services\Donation\DonationService;
use Livewire\Volt\Component;

new class extends Component {
    public $muzakki_id;
    public $campaign_id;
    public $amount;
    public $donation_type;
    public $payment_method;
    public $donor_message;
    public $date;

    public function mount(): void
    {
        $this->donation_type = CampaignType::Infaq->value;
        $this->payment_method = PaymentMethod::BankTransfer->value;
        $this->date = now()->format('Y-m-d');
    }

    public function save(DonationService $donationService): void
    {
        $this->validate([
            'muzakki_id' => 'required|string|exists:muzakkis,id',
            'amount' => 'required|numeric|min:1000',
            'donation_type' => 'required|string',
            'campaign_id' => 'nullable|exists:campaigns,id',
        ]);

        $muzakki = Muzakki::find($this->muzakki_id);

        $donation = Donation::create([
            'transaction_id' => Donation::generateTransactionId(),
            'campaign_id' => $this->campaign_id,
            'muzakki_id' => $this->muzakki_id,
            'donor_name' => $muzakki->name,
            'donor_email' => $muzakki->email,
            'donor_phone' => $muzakki->phone,
            'amount' => $this->amount,
            'donation_type' => $this->donation_type,
            'payment_method' => $this->payment_method,
            'status' => DonationStatus::Verified->value, // Manual donations are usually pre-verified
            'donor_message' => $this->donor_message,
            'verified_at' => now(),
            'verified_by' => auth()->id(),
            'notes' => 'Input Manual oleh Admin',
            'created_at' => $this->date . ' ' . now()->format('H:i:s'),
        ]);

        if ($this->campaign_id) {
            Campaign::find($this->campaign_id)->increment('current_amount', $this->amount);
        }

        $this->dispatch('notify', message: 'Donasi manual berhasil dicatat.', type: 'success');
        $this->redirect(route('admin.donations.index'), navigate: true);
    }

    public function with(): array
    {
        return [
            'campaigns' => Campaign::active()->get(),
            'muzakkis' => Muzakki::active()->orderBy('name')->get(),
            'types' => CampaignType::cases(),
            'methods' => PaymentMethod::cases(),
        ];
    }
}; ?>

<div class="p-3 md:p-6 lg:p-10 space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <flux:button variant="ghost" size="sm" icon="arrow-left" :href="route('admin.donations.index')"
                wire:navigate />
            <div class="space-y-1">
                <h1 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white">
                    {{ __('Catat Donasi Manual') }}</h1>
                <p class="text-zinc-500 dark:text-zinc-400 text-sm">
                    {{ __('Input donasi yang diterima secara offline atau cash.') }}</p>
            </div>
        </div>
    </div>

    <form wire:submit="save" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div
                class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-8 rounded-xl shadow-xs space-y-6">
                <h2 class="text-lg font-bold text-zinc-900 dark:text-white">{{ __('Detail Transaksi') }}</h2>

                <!-- Financials -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input wire:model="amount" type="number" label="Jumlah Donasi (Rp)" prefix="Rp"
                        placeholder="50000" icon="banknotes" />
                    <flux:input wire:model="date" type="date" label="Tanggal Penerimaan" icon="calendar" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:select wire:model="donation_type" label="Jenis Dana">
                        @foreach ($types as $t)
                            <option value="{{ $t->value }}">{{ $t->label() }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model="payment_method" label="Metode Pembayaran">
                        @foreach ($methods as $m)
                            <option value="{{ $m->value }}">{{ $m->label() }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <flux:textarea wire:model="donor_message" label="Keterangan / Pesan"
                    placeholder="Contoh: Infaq titipan hamba Allah..." rows="3" />
            </div>
        </div>

        <!-- Sidebar Selection -->
        <div class="space-y-6">
            <div
                class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 rounded-xl shadow-xs space-y-6">
                <h2 class="text-base font-bold text-zinc-900 dark:text-white">{{ __('Identitas Donatur') }}</h2>

                <div class="space-y-4">
                    <flux:select wire:model="muzakki_id" label="Pilih Muzakki" searchable>
                        <option value="">-- Muzakki Tetap --</option>
                        @foreach ($muzakkis as $m)
                            <option value="{{ $m->id }}">
                                {{ $m->name }}
                                ({{ $m->phone ?? 'No Phone' }})
                            </option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model="campaign_id" label="Program / Campaign">
                        <option value="">-- Dana Umum --</option>
                        @foreach ($campaigns as $c)
                            <option value="{{ $c->id }}">{{ $c->title }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800">
                    <flux:button type="submit" variant="primary" class="w-full" icon="check">
                        {{ __('Simpan Donasi') }}
                    </flux:button>
                </div>
            </div>
        </div>
    </form>
</div>
