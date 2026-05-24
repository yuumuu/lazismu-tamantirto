<?php

declare(strict_types=1);

use App\Models\Masjid;
use Livewire\Volt\Component;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

new class extends Component {
    public Masjid $masjid;
    public string $name = '';
    public string $slug = '';
    public string $address = '';
    public string $phone = '';
    public string $email = '';
    public bool $is_active = true;

    public function mount(Masjid $masjid): void
    {
        $this->masjid = $masjid;
        $this->name = $masjid->name;
        $this->slug = $masjid->slug;
        $this->address = $masjid->address ?? '';
        $this->phone = $masjid->phone ?? '';
        $this->email = $masjid->email ?? '';
        $this->is_active = (bool) $masjid->is_active;
    }

    public function updatedName($value): void
    {
        if ($this->masjid->id !== 1) {
            $this->slug = Str::slug($value);
        }
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('masjids')->ignore($this->masjid->id)],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        // Protect Pusat slug
        if ($this->masjid->id === 1) {
            unset($validated['slug']);
        }

        $this->masjid->update($validated);

        $this->dispatch('notify', message: 'Data Cabang/Masjid berhasil diperbarui.', type: 'success');
        $this->redirect(route('admin.masjids.index'), navigate: true);
    }
}; ?>

<div>
    <x-admin.page-header 
        title="Edit Cabang: {{ $name }}" 
        description="Perbarui informasi profil dan status operasional unit masjid."
        backRoute="admin.masjids.index"
    />

    <div class="p-3 md:p-6 lg:p-10 max-w-4xl mx-auto space-y-8">
        <form wire:submit="save" class="space-y-6">
            <div class="premium-card p-8 space-y-8">
                <!-- Basic Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input wire:model.live="name" label="Nama Cabang/Masjid" placeholder="Contoh: Lazismu Masjid Nurul Huda" required />
                    <flux:input wire:model="slug" label="Slug URL" placeholder="masjid-nurul-huda" required :disabled="$masjid->id === 1" />
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
                        <flux:switch wire:model="is_active" :disabled="$masjid->id === 1" />
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <flux:button :href="route('admin.masjids.index')" variant="ghost" wire:navigate>Batal</flux:button>
                <flux:button type="submit" variant="primary" class="px-8 font-black uppercase tracking-widest shadow-lg shadow-primary/20">
                    Update Cabang
                </flux:button>
            </div>
        </form>
    </div>
</div>
