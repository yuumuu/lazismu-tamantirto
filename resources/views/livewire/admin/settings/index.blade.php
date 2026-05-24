<?php

declare(strict_types=1);

use App\Models\Setting;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

new class extends Component {
    use WithFileUploads;

    public array $settings = [];
    public $qris_image_upload;
    public $tab = 'general';

    public function mount(): void
    {
        $this->loadSettings();
    }

    public function loadSettings(): void
    {
        $this->settings = Setting::all()->pluck('value', 'key')->toArray();
    }

    public function save(): void
    {
        foreach ($this->settings as $key => $value) {
            Setting::setValue((string)$key, $value);
        }

        if ($this->qris_image_upload) {
            $path = $this->qris_image_upload->store('settings', 'public');
            Setting::setValue('site_qris', $path);
            $this->qris_image_upload = null;
        }

        Setting::clearCache();
        $this->loadSettings();
        \Flux::toast('Pengaturan berhasil diperbarui.', variant: 'success');
    }

    public function getBankAccounts(): array
    {
        if (isset($this->settings['bank_accounts']) && is_string($this->settings['bank_accounts'])) {
            $accounts = json_decode($this->settings['bank_accounts'], true);
            return is_array($accounts) ? $accounts : [];
        }

        // Use setting helper which handles json decoding automatically
        $accounts = setting('bank_accounts', []);
        return is_array($accounts) ? $accounts : [];
    }

    public function addBankAccount(): void
    {
        $accounts = $this->getBankAccounts();
        $accounts[] = ['bank_name' => '', 'account_number' => '', 'account_name' => ''];
        $this->settings['bank_accounts'] = json_encode($accounts);
    }

    public function removeBankAccount(int $index): void
    {
        $accounts = $this->getBankAccounts();
        unset($accounts[$index]);
        $this->settings['bank_accounts'] = json_encode(array_values($accounts));
    }

    public function updateBankField(int $index, string $field, string $value): void
    {
        $accounts = $this->getBankAccounts();
        $accounts[$index][$field] = $value;
        $this->settings['bank_accounts'] = json_encode($accounts);
    }
}; ?>

@push('head')
    <title>Pengaturan Sistem - {{ ucwords(config('app.name')) }}</title>
@endpush

<div class="p-3 md:p-6 lg:p-10 space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
        <div class="space-y-2">
            <div class="flex items-center gap-2">
                <div class="h-6 w-1 bg-primary rounded-full"></div>
                <h1 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white">{{ __('Pengaturan Sistem') }}</h1>
            </div>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm max-w-lg leading-relaxed border-l pl-4 border-zinc-200 dark:border-zinc-800">
                {{ __('Konfigurasi informasi dasar, kontak, media sosial, dan standar perhitungan zakat.') }}
            </p>
        </div>
        <flux:button variant="primary" icon="check" wire:click="save">
            {{ __('Simpan Perubahan') }}
        </flux:button>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Sidebar Tabs -->
        <div class="lg:col-span-1">
            <nav class="flex flex-col gap-1">
                @foreach([
                    ['id' => 'general', 'label' => 'Informasi Umum', 'icon' => 'globe-alt'],
                    ['id' => 'contact', 'label' => 'Kontak & Alamat', 'icon' => 'map-pin'],
                    ['id' => 'social', 'label' => 'Media Sosial', 'icon' => 'hashtag'],
                    ['id' => 'donations', 'label' => 'Metode Donasi', 'icon' => 'credit-card'],
                    ['id' => 'zakat', 'label' => 'Standar Zakat', 'icon' => 'calculator'],
                ] as $item)
                    <button 
                        wire:click="$set('tab', '{{ $item['id'] }}')"
                        class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-bold transition-all {{ $tab === $item['id'] ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-zinc-500 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}"
                    >
                        <flux:icon name="{{ $item['icon'] }}" class="size-5" />
                        {{ $item['label'] }}
                    </button>
                @endforeach
            </nav>
        </div>

        <!-- Form Content -->
        <div class="lg:col-span-3">
            <div class="premium-card p-8">
                <form wire:submit.prevent="save" class="space-y-8">
                    
                    @if($tab === 'general')
                        <div class="space-y-6">
                            <h3 class="text-lg font-black tracking-tight text-zinc-900 dark:text-white border-b border-zinc-100 dark:border-zinc-800 pb-4">Identitas Situs</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <flux:input wire:model="settings.site_name" label="Nama Lembaga" />
                                <flux:input wire:model="settings.site_tagline" label="Tagline / Slogan" />
                            </div>
                            <flux:textarea wire:model="settings.footer_text" label="Teks Kaki (Footer)" rows="3" />
                        </div>

                    @elseif($tab === 'contact')
                        <div class="space-y-6">
                            <h3 class="text-lg font-black tracking-tight text-zinc-900 dark:text-white border-b border-zinc-100 dark:border-zinc-800 pb-4">Kontak Resmi</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <flux:input wire:model="settings.contact_email" label="Email" icon="envelope" />
                                <flux:input wire:model="settings.contact_phone" label="No. Telepon" icon="phone" />
                                <flux:input wire:model="settings.contact_whatsapp" label="WhatsApp" icon="chat-bubble-left-right" />
                                <flux:input wire:model="settings.contact_maps_url" label="Link Google Maps" icon="map" />
                            </div>
                            <flux:textarea wire:model="settings.contact_address" label="Alamat Kantor" rows="3" />
                        </div>

                    @elseif($tab === 'social')
                        <div class="space-y-6">
                            <h3 class="text-lg font-black tracking-tight text-zinc-900 dark:text-white border-b border-zinc-100 dark:border-zinc-800 pb-4">Media Sosial</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <flux:input wire:model="settings.social_facebook" label="Facebook URL"  />
                                <flux:input wire:model="settings.social_instagram" label="Instagram URL" />
                                <flux:input wire:model="settings.social_twitter" label="Twitter URL" />
                                <flux:input wire:model="settings.social_youtube" label="YouTube URL" />
                            </div>
                        </div>

                    @elseif($tab === 'donations')
                        <div class="space-y-8">
                            <div class="space-y-6">
                                <h3 class="text-lg font-black tracking-tight text-zinc-900 dark:text-white border-b border-zinc-100 dark:border-zinc-800 pb-4">QRIS Pembayaran</h3>
                                <div class="flex items-start gap-8">
                                    <div class="size-48 rounded-3xl border-4 border-dashed border-zinc-200 dark:border-zinc-800 overflow-hidden bg-zinc-50 dark:bg-zinc-950 flex items-center justify-center shrink-0">
                                        @if($qris_image_upload)
                                            <img src="{{ $qris_image_upload->temporaryUrl() }}" class="size-full object-cover">
                                        @elseif($settings['site_qris'] ?? null)
                                            <img src="{{ asset('storage/' . $settings['site_qris']) }}" class="size-full object-cover">
                                        @else
                                            <flux:icon name="qr-code" class="size-12 text-zinc-300" />
                                        @endif
                                    </div>
                                    <div class="space-y-4 flex-1">
                                        <flux:input type="file" accept="image/png,image/jpeg" wire:model="qris_image_upload" label="Upload QRIS Baru" />
                                        <p class="text-xs text-zinc-500">Gunakan gambar QRIS standar (JPG/PNG, Max 2MB). Gambar ini akan muncul di halaman instruksi pembayaran.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-4">
                                    <h3 class="text-lg font-black tracking-tight text-zinc-900 dark:text-white">Rekening Bank</h3>
                                    <flux:button variant="ghost" size="sm" icon="plus" wire:click="addBankAccount">Tambah Rekening</flux:button>
                                </div>
                                <div class="space-y-4">
                                    @foreach($this->getBankAccounts() as $index => $account)
                                        <div class="p-6 rounded-2xl bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-100 dark:border-zinc-800 grid grid-cols-1 md:grid-cols-3 gap-4 relative group">
                                            <flux:input 
                                                label="Nama Bank" 
                                                value="{{ $account['bank_name'] ?? '' }}" 
                                                wire:change="updateBankField({{ $index }}, 'bank_name', $event.target.value)" 
                                            />
                                            <flux:input 
                                                label="No. Rekening" 
                                                value="{{ $account['account_number'] ?? '' }}" 
                                                wire:change="updateBankField({{ $index }}, 'account_number', $event.target.value)" 
                                            />
                                            <flux:input 
                                                label="Nama Pemilik" 
                                                value="{{ $account['account_name'] ?? '' }}" 
                                                wire:change="updateBankField({{ $index }}, 'account_name', $event.target.value)" 
                                            />
                                            <button 
                                                type="button"
                                                wire:click="removeBankAccount({{ $index }})"
                                                class="absolute -top-2 -right-2 size-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow-sm"
                                            >
                                                <flux:icon name="x-mark" class="size-4" />
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    @elseif($tab === 'zakat')
                        <div class="space-y-6">
                            <h3 class="text-lg font-black tracking-tight text-zinc-900 dark:text-white border-b border-zinc-100 dark:border-zinc-800 pb-4">Konfigurasi Zakat</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <flux:input wire:model="settings.zakat_gold_nisab" label="Nisab Emas (Gram)" type="number" />
                                <flux:input wire:model="settings.zakat_gold_price" label="Harga Emas Per Gram" type="number" icon="banknotes" />
                                <flux:input wire:model="settings.zakat_silver_nisab" label="Nisab Perak (Gram)" type="number" />
                            </div>
                            <p class="text-xs text-zinc-500 italic">Nilai ini digunakan sebagai dasar perhitungan otomatis pada kalkulator zakat.</p>
                        </div>
                    @endif

                    <div class="pt-8 border-t border-zinc-100 dark:border-zinc-800 flex justify-end gap-2">
                        <flux:button type="button" variant="ghost" wire:click="loadSettings">Reset</flux:button>
                        <flux:button type="submit" variant="primary" class="px-12 font-black shadow-lg shadow-primary/20">
                            Simpan Semua Perubahan
                        </flux:button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
