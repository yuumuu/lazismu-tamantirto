<?php

use App\Models\Mustahik;
use App\Enums\AsnafType;
use Livewire\Volt\Component;

new class extends Component {
    public $name = '';
    public $address = '';
    public $phone = '';
    public $asnaf_type = '';
    public $nik = '';
    public $family_card_number = '';
    public $occupation = '';
    public $income_range = '';
    public $resides_at = 'owned';
    public $notes = '';

    public function mount(): void
    {
        $this->asnaf_type = AsnafType::Miskin->value;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|min:3|max:255',
            'asnaf_type' => 'required|string',
            'nik' => 'nullable|numeric|digits:16|unique:mustahiks,nik',
            'family_card_number' => 'nullable|numeric|digits:16',
            'phone' => 'nullable|min:10|max:15',
        ]);

        Mustahik::create([
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
        ]);

        $this->dispatch('notify', message: 'Data Mustahik berhasil ditambahkan.', type: 'success');
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
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ __('Tambah Mustahik') }}</h1>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm mt-1">{{ __('Input data penerima manfaat (calon penerima bantuan).') }}</p>
        </div>
    </div>

    <form wire:submit="save" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-zinc-900 p-8 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                <!-- Basic Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <flux:input wire:model="name" label="Nama Lengkap Mustahik" placeholder="Contoh: Ahmad Subardjo" />
                    </div>
                    <flux:input wire:model="phone" label="Nomor Telepon/WA" placeholder="08XXXXXXXXXX" />
                    <flux:select wire:model="asnaf_type" label="Kriteria Asnaf">
                        @foreach($asnafTypes as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <!-- Identity -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    <flux:input wire:model="nik" label="NIK (Sesuai KTP)" placeholder="340XXXXXXXXXXXXX" />
                    <flux:input wire:model="family_card_number" label="Nomor Kartu Keluarga" placeholder="340XXXXXXXXXXXXX" />
                </div>

                <flux:textarea wire:model="address" label="Alamat / Domisili" placeholder="Dusun..., Desa..., Kecamatan..." rows="3" />
            </div>

            <!-- Socio Economic -->
            <div class="bg-white dark:bg-zinc-900 p-8 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                <h3 class="font-bold text-zinc-900 dark:text-white">{{ __('Kondisi Sosio-Ekonomi') }}</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input wire:model="occupation" label="Pekerjaan" placeholder="Buruh, Pedagang, dsb." />
                    <flux:input wire:model="income_range" label="Estimasi Penghasilan Bulanan" placeholder="Rp 500.000 - Rp 1.000.000" />
                    <flux:select wire:model="resides_at" label="Kepemilikan Rumah">
                        <option value="owned">Milik Sendiri</option>
                        <option value="rented">Sewa / Kontrak</option>
                        <option value="family">Numpang Keluarga</option>
                        <option value="none">Tidak Tetap</option>
                    </flux:select>
                </div>

                <flux:textarea wire:model="notes" label="Catatan Tambahan (Hasil Survei)" placeholder="Ketik detail hasil survei di sini..." rows="4" />
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white dark:bg-zinc-900 p-6 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <div class="space-y-4">
                    <p class="text-sm text-zinc-500">{{ __('Pastikan data yang diinput sesuai dengan hasil survei lapangan untuk menjaga akuntabilitas LazisMU.') }}</p>
                    
                    <div class="pt-4">
                        <flux:button type="submit" variant="primary" class="w-full" icon="check">
                            {{ __('Simpan Data Mustahik') }}
                        </flux:button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
