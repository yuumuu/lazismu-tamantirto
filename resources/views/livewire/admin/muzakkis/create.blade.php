<?php

use App\Models\Muzakki;
use Livewire\Volt\Component;

new class extends Component {
    public $name = '';
    public $email = '';
    public $phone = '';
    public $nik = '';
    public $npwp = '';
    public $address = '';
    public $type = 'individu';

    public function save(): void
    {
        $this->validate([
            'name' => 'required|min:3|max:255',
            'email' => 'nullable|email|unique:muzakkis,email',
            'phone' => 'nullable|min:10|max:15',
            'nik' => 'nullable|numeric|digits:16|unique:muzakkis,nik',
            'npwp' => 'nullable|max:25',
            'type' => 'required|in:individu,lembaga',
        ]);

        Muzakki::create([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'nik' => $this->nik,
            'npwp' => $this->npwp,
            'address' => $this->address,
            'type' => $this->type,
        ]);

        $this->dispatch('notify', message: 'Data Muzakki berhasil ditambahkan.', type: 'success');
        $this->redirect(route('admin.muzakkis.index'), navigate: true);
    }
} ?>

<div class="p-3 md:p-6 lg:p-10 space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <flux:button variant="ghost" size="sm" icon="arrow-left" :href="route('admin.muzakkis.index')" wire:navigate />
            <div class="space-y-1">
                <h1 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white">{{ __('Tambah Muzakki') }}</h1>
                <p class="text-zinc-500 dark:text-zinc-400 text-sm">{{ __('Input data pemberi zakat/donasi baru.') }}</p>
            </div>
        </div>
    </div>

    <form wire:submit="save" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-8 rounded-xl shadow-xs space-y-6">
                <h2 class="text-lg font-bold text-zinc-900 dark:text-white">{{ __('Informasi Dasar') }}</h2>
                
                <!-- Basic Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <flux:input wire:model="name" label="Nama Lengkap / Nama Lembaga" placeholder="Contoh: Budi Santoso" icon="user" />
                    </div>
                    <flux:input wire:model="email" type="email" label="Email" placeholder="budi@example.com" icon="envelope" />
                    <flux:input wire:model="phone" label="Nomor Telepon" placeholder="08123456789" icon="phone" />
                </div>

                <!-- Identity -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input wire:model="nik" label="NIK (16 Digit)" placeholder="3404XXXXXXXXXXXX" icon="identification" />
                    <flux:input wire:model="npwp" label="NPWP (Opsional)" placeholder="00.000.000.0-000.000" icon="document-text" />
                </div>

                <flux:textarea wire:model="address" label="Alamat Lengkap" placeholder="Jl. Tamantirto No. 1..." rows="3" />
            </div>
        </div>

        <!-- Sidebar Selection -->
        <div class="space-y-6">
            <div class="border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 p-6 rounded-xl shadow-xs space-y-6">
                <h2 class="text-base font-bold text-zinc-900 dark:text-white">{{ __('Pengaturan') }}</h2>
                
                <flux:radio.group wire:model="type" label="Tipe Muzakki">
                    <flux:radio value="individu" label="Perorangan / Individu" />
                    <flux:radio value="lembaga" label="Instansi / Lembaga" />
                </flux:radio.group>

                <div class="pt-4 border-t border-zinc-200 dark:border-zinc-800">
                    <flux:button type="submit" variant="primary" class="w-full" icon="check">
                        {{ __('Simpan Data Muzakki') }}
                    </flux:button>
                </div>
            </div>
        </div>
    </form>
</div>
