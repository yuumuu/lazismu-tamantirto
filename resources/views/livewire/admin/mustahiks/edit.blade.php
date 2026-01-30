<?php

use App\Models\Mustahik;
use App\Enums\AsnafType;
use Livewire\Volt\Component;

new class extends Component {
    public Mustahik $mustahik;

    public $name;
    public $address;
    public $phone;
    public $asnaf_type;
    public $nik;
    public $family_card_number;
    public $occupation;
    public $income_range;
    public $resides_at;
    public $notes;
    public $is_active;

    public function mount(Mustahik $mustahik): void
    {
        $this->mustahik = $mustahik;
        $this->name = $mustahik->name;
        $this->address = $mustahik->address;
        $this->phone = $mustahik->phone;
        $this->asnaf_type = $mustahik->asnaf_type->value;
        $this->nik = $mustahik->nik;
        $this->family_card_number = $mustahik->family_card_number;
        $this->occupation = $mustahik->occupation;
        $this->income_range = $mustahik->income_range;
        $this->resides_at = $mustahik->resides_at;
        $this->notes = $mustahik->notes;
        $this->is_active = $mustahik->is_active;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|min:3|max:255',
            'asnaf_type' => 'required|string',
            'nik' => 'nullable|numeric|digits:16|unique:mustahiks,nik,' . $this->mustahik->id,
            'family_card_number' => 'nullable|numeric|digits:16',
            'phone' => 'nullable|min:10|max:15',
        ]);

        $this->mustahik->update([
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
            'asnaf_type' => $this->asnaf_type,
            'nik' => $this->nik,
            'family_card_number' => $this->family_card_number,
            'occupation' => $this->occupation,
            'income_range' => $this->income_range,
            'resides_at' => $this->resides_at,
            'notes' => $this->notes,
            'is_active' => $this->is_active,
        ]);

        $this->dispatch('notify', message: 'Data Mustahik berhasil diperbarui.', type: 'success');
        $this->redirect(route('admin.mustahiks.index'), navigate: true);
    }

    public function with(): array
    {
        return [
            'asnafTypes' => AsnafType::cases(),
        ];
    }
} ?>

<div class="p-3 md:p-6 lg:p-10 space-y-8">
    <div class="flex items-center gap-4">
        <flux:button variant="ghost" icon="arrow-left" :href="route('admin.mustahiks.index')" wire:navigate />
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ __('Edit Mustahik') }}</h1>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm mt-1">{{ __('Perbarui informasi penerima manfaat.') }}</p>
        </div>
    </div>

    <form wire:submit="save" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-zinc-900 p-8 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                <!-- Basic Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <flux:input wire:model="name" label="Nama Lengkap Mustahik" />
                    </div>
                    <flux:input wire:model="phone" label="Nomor Telepon/WA" />
                    <flux:select wire:model="asnaf_type" label="Kriteria Asnaf">
                        @foreach($asnafTypes as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <!-- Identity -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    <flux:input wire:model="nik" label="NIK" />
                    <flux:input wire:model="family_card_number" label="Nomor KK" />
                </div>

                <flux:textarea wire:model="address" label="Alamat / Domisili" rows="3" />
            </div>

            <!-- Socio Economic -->
            <div class="bg-white dark:bg-zinc-900 p-8 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                <h3 class="font-bold text-zinc-900 dark:text-white">{{ __('Kondisi Sosio-Ekonomi') }}</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input wire:model="occupation" label="Pekerjaan" />
                    <flux:input wire:model="income_range" label="Estimasi Penghasilan" />
                    <flux:select wire:model="resides_at" label="Kepemilikan Rumah">
                        <option value="owned">Milik Sendiri</option>
                        <option value="rented">Sewa / Kontrak</option>
                        <option value="family">Numpang Keluarga</option>
                        <option value="none">Tidak Tetap</option>
                    </flux:select>
                </div>

                <flux:textarea wire:model="notes" label="Catatan Tambahan (Hasil Survei)" rows="4" />
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
