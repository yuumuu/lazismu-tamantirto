<?php

use App\Models\CampaignCategory;
use Livewire\Volt\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;

new class extends Component {
    use WithPagination;

    public $search = '';

    public function with(): array
    {
        return [
            'categories' => CampaignCategory::query()
                ->withCount('campaigns')
                ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                ->latest()
                ->paginate(10),
        ];
    }

    public function delete(CampaignCategory $category): void
    {
        if ($category->campaigns()->exists()) {
            $this->dispatch('notify', message: 'Tidak dapat menghapus kategori yang masih memiliki kampanye.', type: 'error');
            return;
        }

        $category->delete();
        $this->dispatch('notify', message: 'Kategori berhasil dihapus.', type: 'success');
    }
}; ?>

@push('head')
    <title>Kategori Kampanye - {{ ucwords(config('app.name')) }}</title>
@endpush

<div class="p-3 md:p-6 lg:p-10 space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
        <div class="space-y-2">
            <div class="flex items-center gap-2">
                <div class="h-6 w-1 bg-primary rounded-full"></div>
                <h1 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white">{{ __('Kategori Kampanye') }}</h1>
            </div>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm max-w-lg border-l pl-4 border-zinc-200 dark:border-zinc-800">
                {{ __('Kelola kategori untuk mengklasifikasikan program kampanye (Zakat, Infaq, Sedekah, dsb).') }}
            </p>
        </div>
        <flux:button variant="primary" icon="plus" :href="route('admin.campaign-categories.create')" wire:navigate>
            {{ __('Tambah Kategori') }}
        </flux:button>
    </div>

    <!-- Toolbar -->
    <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
        <div class="w-full sm:w-72">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari kategori..." icon="magnifying-glass" />
        </div>
    </div>

    <!-- Table -->
    <div class="premium-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-zinc-50 dark:bg-zinc-900/50 text-zinc-500 font-medium border-b border-zinc-200 dark:border-zinc-800 uppercase text-[10px] tracking-widest">
                    <tr>
                        <th class="px-6 py-4">{{ __('Nama Kategori') }}</th>
                        <th class="px-6 py-4">{{ __('Slug') }}</th>
                        <th class="px-6 py-4">{{ __('Jumlah Kampanye') }}</th>
                        <th class="px-6 py-4 text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900 font-medium">
                    @forelse($categories as $category)
                        <tr class="group hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors" wire:key="{{ $category->id }}">
                            <td class="px-6 py-5">
                                <span class="font-bold text-zinc-900 dark:text-white uppercase">{{ $category->name }}</span>
                            </td>
                            <td class="px-6 py-5 font-mono text-xs text-zinc-500">
                                {{ $category->slug }}
                            </td>
                            <td class="px-6 py-5">
                                <span class="px-3 py-1 rounded-full bg-primary/10 text-primary text-xs font-black">
                                    {{ $category->campaigns_count }} Program
                                </span>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button icon="pencil-square" size="xs" variant="ghost" :href="route('admin.campaign-categories.edit', $category)" wire:navigate />
                                    <flux:button icon="trash" size="xs" variant="ghost" class="text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20" wire:click="delete('{{ $category->id }}')" wire:confirm="Hapus kategori ini?" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <flux:icon name="magnifying-glass" class="size-8 text-zinc-300" />
                                    <p class="text-zinc-500 text-sm font-medium">{{ __('Tidak ada kategori ditemukan.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 p-4">
            {{ $categories->links() }}
        </div>
    </div>
</div>
