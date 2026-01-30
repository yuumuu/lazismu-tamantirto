<?php

use App\Models\Distributor;
use Livewire\Volt\Component;

new class extends Component {
    public $name = '';
    public $phone = '';
    public $address = '';
    public $type = 'volunteer';

    public function save(): void
    {
        $this->validate([
            'name' => 'required|min:3|max:255',
            'phone' => 'nullable|min:10|max:15',
            'type' => 'required|in:volunteer,staff,partner',
        ]);

        Distributor::create([
            'name' => $this->name,
            'phone' => $this->phone,
            'address' => $this->address,
            'type' => $this->type,
        ]);

        $this->dispatch('notify', message: 'Data Penyalur berhasil ditambahkan.', type: 'success');
        $this->redirect(route('admin.distributors.index'), navigate: true);
    }
} ?>

<div class="p-3 md:p-6 lg:p-10 space-y-8">
    <div class="flex items-center gap-4">
        <flux:button variant="ghost" icon="arrow-left" :href="route('admin.distributors.index')" wire:navigate />
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ __('Tambah Penyalur') }}</h1>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm mt-1">{{ __('Input data personil atau partner penyaluran.') }}</p>
        </div>
    </div>

    <form wire:submit="save" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-zinc-900 p-8 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                <flux:input wire:model="name" label="Nama Lengkap Penyalur" placeholder="Contoh: Heri Relawan" />
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input wire:model="phone" label="Nomor Telepon/WA" placeholder="08XXXXXXXXXX" />
                    <flux:select wire:model="type" label="Tipe Penyalur">
                        <option value="volunteer">Relawan (Volunteer)</option>
                        <option value="staff">Staff LazisMU</option>
                        <option value="partner">Lembaga Partner</option>
                    </flux:select>
                </div>

                <flux:textarea wire:model="address" label="Alamat / Keterangan" placeholder="Alamat tinggal atau asal instansi..." rows="3" />
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white dark:bg-zinc-900 p-6 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <div class="pt-4">
                    <flux:button type="submit" variant="primary" class="w-full" icon="check">
                        {{ __('Simpan Data Penyalur') }}
                    </flux:button>
                </div>
            </div>
        </div>
    </form>
</div>
