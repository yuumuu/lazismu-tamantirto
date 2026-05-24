<?php

declare(strict_types=1);

use App\Models\Masjid;
use App\Models\Setting;
use Livewire\Volt\Component;
use Illuminate\Support\Str;

new class extends Component {
    public string $name = '';
    public string $slug = '';
    public string $address = '';
    public string $phone = '';
    public string $email = '';
    public bool $is_active = true;

    public function updatedName($value): void
    {
        $this->slug = Str::slug($value);
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:masjids,slug'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        $masjid = Masjid::create($validated);

        // Seed default settings for the new masjid
        $this->seedMasjidSettings($masjid->id);

        $this->dispatch('notify', message: 'Cabang/Masjid baru berhasil ditambahkan.', type: 'success');
        $this->redirect(route('admin.masjids.index'), navigate: true);
    }

    protected function seedMasjidSettings(int $masjidId): void
    {
        // Copy basic settings from Pusat (id 1) to the new masjid
        $defaultSettings = Setting::withoutGlobalScope(\App\Traits\BelongsToMasjid::class)
            ->where('masjid_id', 1)
            ->get();

        foreach ($defaultSettings as $setting) {
            Setting::create([
                'masjid_id' => $masjidId,
                'key' => $setting->key,
                'value' => $setting->value,
                'type' => $setting->type,
                'group_name' => $setting->group_name,
                'label' => $setting->label,
                'description' => $setting->description,
                'is_public' => $setting->is_public,
            ]);
        }
    }
}; ?>

<div>
    <x-admin.page-header 
        title="Tambah Cabang Baru" 
        description="Daftarkan unit masjid atau cabang baru ke dalam jaringan Lazismu."
        backRoute="admin.masjids.index"
    />

    <div class="p-3 md:p-6 lg:p-10 max-w-4xl mx-auto space-y-8">
        <form wire:submit="save" class="space-y-6">
            <div class="premium-card p-8 space-y-8">
                <!-- Basic Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input wire:model.live="name" label="Nama Cabang/Masjid" placeholder="Contoh: Lazismu Masjid Nurul Huda" required />
                    <flux:input wire:model="slug" label="Slug URL" placeholder="masjid-nurul-huda" required />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input wire:model="email" type="email" label="Email Cabang" placeholder="kontak@masjid.com" />
                    <flux:input wire:model="phone" label="Nomor Telepon" placeholder="08123456789" />
                </div>

                <flux:textarea wire:model="address" label="Alamat Lengkap" placeholder="Masukkan alamat lengkap..." rows="3" />

                <!-- Status -->
                <div class="pt-6 border-t border-zinc-100 dark:border-zinc-800">
                    <div class="p-6 rounded-2xl bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-zinc-900 dark:text-white">Status Aktif</span>
                            <span class="text-[10px] text-zinc-500">Jika dinonaktifkan, cabang ini tidak dapat diakses secara publik.</span>
                        </div>
                        <flux:switch wire:model="is_active" />
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <flux:button :href="route('admin.masjids.index')" variant="ghost" wire:navigate>Batal</flux:button>
                <flux:button type="submit" variant="primary" class="px-8 font-black uppercase tracking-widest shadow-lg shadow-primary/20">
                    Simpan Cabang
                </flux:button>
            </div>
        </form>
    </div>
</div>
