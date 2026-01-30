<?php

use App\Models\Muzakki;
use Livewire\Volt\Component;

new class extends Component {
    public Muzakki $muzakki;

    public $name;
    public $email;
    public $phone;
    public $nik;
    public $npwp;
    public $address;
    public $type;
    public $is_active;

    public function mount(Muzakki $muzakki): void
    {
        $this->muzakki = $muzakki;
        $this->name = $muzakki->name;
        $this->email = $muzakki->email;
        $this->phone = $muzakki->phone;
        $this->nik = $muzakki->nik;
        $this->npwp = $muzakki->npwp;
        $this->address = $muzakki->address;
        $this->type = $muzakki->type;
        $this->is_active = $muzakki->is_active;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|min:3|max:255',
            'email' => 'nullable|email|unique:muzakkis,email,' . $this->muzakki->id,
            'phone' => 'nullable|min:10|max:15',
            'nik' => 'nullable|numeric|digits:16|unique:muzakkis,nik,' . $this->muzakki->id,
            'npwp' => 'nullable|max:25',
            'type' => 'required|in:individu,lembaga',
        ]);

        $this->muzakki->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'nik' => $this->nik,
            'npwp' => $this->npwp,
            'address' => $this->address,
            'type' => $this->type,
            'is_active' => $this->is_active,
        ]);

        $this->dispatch('notify', message: 'Data Muzakki berhasil diperbarui.', type: 'success');
        $this->redirect(route('admin.muzakkis.index'), navigate: true);
    }
} ?>

<div class="p-3 md:p-6 lg:p-10 space-y-8">
    <div class="flex items-center gap-4">
        <flux:button variant="ghost" icon="arrow-left" :href="route('admin.muzakkis.index')" wire:navigate />
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ __('Edit Muzakki') }}</h1>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm mt-1">{{ __('Perbarui data pemberi zakat/donasi.') }}</p>
        </div>
    </div>

    <form wire:submit="save" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-zinc-900 p-8 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                <!-- Basic Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <flux:input wire:model="name" label="Nama Lengkap / Nama Lembaga" />
                    </div>
                    <flux:input wire:model="email" type="email" label="Email" />
                    <flux:input wire:model="phone" label="Nomor Telepon" />
                </div>

                <!-- Identity -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input wire:model="nik" label="NIK (16 Digit)" />
                    <flux:input wire:model="npwp" label="NPWP (Opsional)" />
                </div>

                <flux:textarea wire:model="address" label="Alamat Lengkap" rows="3" />
            </div>
        </div>

        <!-- Sidebar Selection -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-zinc-900 p-6 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                <flux:radio.group wire:model="type" label="Tipe Muzakki">
                    <flux:radio value="individu" label="Perorangan" />
                    <flux:radio value="lembaga" label="Lembaga" />
                </flux:radio.group>

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
