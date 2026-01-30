<?php

use App\Models\Distributor;
use Livewire\Volt\Component;

new class extends Component {
    public Distributor $distributor;

    public $name;
    public $phone;
    public $address;
    public $type;
    public $is_active;

    public function mount(Distributor $distributor): void
    {
        $this->distributor = $distributor;
        $this->name = $distributor->name;
        $this->phone = $distributor->phone;
        $this->address = $distributor->address;
        $this->type = $distributor->type;
        $this->is_active = $distributor->is_active;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|min:3|max:255',
            'phone' => 'nullable|min:10|max:15',
            'type' => 'required|in:volunteer,staff,partner',
        ]);

        $this->distributor->update([
            'name' => $this->name,
            'phone' => $this->phone,
            'address' => $this->address,
            'type' => $this->type,
            'is_active' => $this->is_active,
        ]);

        $this->dispatch('notify', message: 'Data Penyalur berhasil diperbarui.', type: 'success');
        $this->redirect(route('admin.distributors.index'), navigate: true);
    }
} ?>

<div class="p-3 md:p-6 lg:p-10 space-y-8">
    <div class="flex items-center gap-4">
        <flux:button variant="ghost" icon="arrow-left" :href="route('admin.distributors.index')" wire:navigate />
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ __('Edit Penyalur') }}</h1>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm mt-1">{{ __('Perbarui data personil atau partner.') }}</p>
        </div>
    </div>

    <form wire:submit="save" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-zinc-900 p-8 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                <flux:input wire:model="name" label="Nama Lengkap Penyalur" />
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input wire:model="phone" label="Nomor Telepon/WA" />
                    <flux:select wire:model="type" label="Tipe Penyalur">
                        <option value="volunteer">Relawan (Volunteer)</option>
                        <option value="staff">Staff LazisMU</option>
                        <option value="partner">Lembaga Partner</option>
                    </flux:select>
                </div>

                <flux:textarea wire:model="address" label="Alamat / Keterangan" rows="3" />
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white dark:bg-zinc-900 p-6 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                <flux:switch wire:model="is_active" label="Status Aktif" />
                
                <div class="pt-4">
                    <flux:button type="submit" variant="primary" class="w-full" icon="check">
                        {{ __('Simpan Perubahan') }}
                    </flux:button>
                </div>
            </div>
        </div>
    </form>
</div>
