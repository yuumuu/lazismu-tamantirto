<?php

declare(strict_types=1);

use App\Models\CampaignCategory;
use Livewire\Volt\Component;
use Illuminate\Support\Str;

new class extends Component {
    public string $name = '';
    public string $slug = '';
    public string $description = '';

    public function updatedName($value): void
    {
        $this->slug = Str::slug($value);
    }

    public function save(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255', 'unique:campaign_categories,name'],
            'slug' => ['required', 'string', 'max:255', 'unique:campaign_categories,slug'],
            'description' => ['nullable', 'string'],
        ]);

        CampaignCategory::create($validated);

        $this->dispatch('notify', message: 'Kategori baru berhasil ditambahkan.', type: 'success');
        $this->redirect(route('admin.campaign-categories.index'), navigate: true);
    }
}; ?>

<div class="p-3 md:p-6 lg:p-10 max-w-2xl mx-auto space-y-8">
    <header class="space-y-2">
        <div class="flex items-center gap-4">
            <flux:button icon="arrow-left" variant="ghost" size="sm" :href="route('admin.campaign-categories.index')" wire:navigate />
            <div class="h-6 w-1 bg-primary rounded-full"></div>
            <h1 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white">{{ __('Tambah Kategori Baru') }}</h1>
        </div>
        <p class="text-zinc-500 dark:text-zinc-400 text-sm pl-12">
            {{ __('Klasifikasikan kampanye Anda (misal: Zakat Maal, Infaq Dakwah, dll).') }}
        </p>
    </header>

    <form wire:submit="save" class="space-y-6">
        <div class="premium-card p-8 space-y-6">
            <flux:input wire:model.live="name" label="Nama Kategori" placeholder="Contoh: Zakat Maal" required />
            
            <flux:input wire:model="slug" label="URL Slug" placeholder="zakat-maal" required />
            
            <flux:textarea wire:model="description" label="Deskripsi (Opsional)" placeholder="Jelaskan tujuan kategori ini..." rows="3" />
        </div>

        <div class="flex justify-end gap-3">
            <flux:button :href="route('admin.campaign-categories.index')" variant="ghost" wire:navigate>Batal</flux:button>
            <flux:button type="submit" variant="primary" class="px-8 font-black uppercase tracking-widest shadow-lg shadow-primary/20">
                Simpan Kategori
            </flux:button>
        </div>
    </form>
</div>
