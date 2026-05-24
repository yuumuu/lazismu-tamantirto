<?php

declare(strict_types=1);

use App\Models\Branch;
use Livewire\Volt\Component;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

new class extends Component {
    public Branch $branch;
    public string $name = '';
    public string $slug = '';
    public string $type = 'masjid';
    public string $address = '';
    public string $phone = '';
    public string $email = '';
    public bool $is_active = true;

    public function mount(Branch $branch): void
    {
        $this->branch = $branch;
        $this->name = $branch->name;
        $this->slug = $branch->slug;
        $this->type = $branch->type ?? 'masjid';
        $this->address = $branch->address ?? '';
        $this->phone = $branch->phone ?? '';
        $this->email = $branch->email ?? '';
        $this->is_active = (bool) $branch->is_active;
    }

    public function updatedName($value): void
    {
        if ($this->branch->id !== 1) {
            $this->slug = Str::slug($value);
        }
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('branches')->ignore($this->branch->id)],
            'type' => ['required', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        // Protect Pusat slug
        if ($this->branch->id === 1) {
            unset($validated['slug']);
        }

        $this->branch->update($validated);

        $this->dispatch('notify', message: 'Data Cabang berhasil diperbarui.', type: 'success');
        $this->redirect(route('admin.branches.index'), navigate: true);
    }
}; ?>

<div>
    <x-admin.page-header 
        title="Edit Cabang: {{ $name }}" 
        description="Perbarui informasi profil dan status operasional unit cabang."
        backRoute="admin.branches.index"
    />

    <div class="p-3 md:p-6 lg:p-10 max-w-4xl mx-auto space-y-8">
        <form wire:submit="save" class="space-y-6">
            <div class="premium-card p-8 space-y-8">
                <!-- Basic Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input wire:model.live="name" label="Nama Cabang" placeholder="Contoh: Lazismu Ranting Tamantirto" required />
                    <flux:input wire:model="slug" label="Slug URL" placeholder="ranting-tamantirto" required :disabled="$branch->id === 1" />
                </div>

                <!-- Type -->
                <flux:select wire:model="type" label="Tipe Cabang" required>
                    <flux:select.option value="masjid">Masjid</flux:select.option>
                    <flux:select.option value="cabang_muhammadiyah">Cabang Muhammadiyah</flux:select.option>
                    <flux:select.option value="ranting_muhammadiyah">Ranting Muhammadiyah</flux:select.option>
                    <flux:select.option value="lembaga">Lembaga</flux:select.option>
                    <flux:select.option value="mitra">Mitra</flux:select.option>
                </flux:select>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input wire:model="email" type="email" label="Email Cabang" placeholder="kontak@cabang.com" />
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
                        <flux:switch wire:model="is_active" :disabled="$branch->id === 1" />
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <flux:button :href="route('admin.branches.index')" variant="ghost" wire:navigate>Batal</flux:button>
                <flux:button type="submit" variant="primary" class="px-8 font-black uppercase tracking-widest shadow-lg shadow-primary/20">
                    Update Cabang
                </flux:button>
            </div>
        </form>
    </div>
</div>
