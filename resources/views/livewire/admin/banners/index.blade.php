<?php

use App\Models\Banner;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(Banner $banner): void
    {
        $banner->delete();
        $this->dispatch('notify', message: 'Banner berhasil dihapus.', type: 'success');
    }

    public function toggleStatus(Banner $banner): void
    {
        $banner->update(['is_active' => !$banner->is_active]);
        $this->dispatch('notify', message: 'Status banner diperbarui.', type: 'success');
    }

    public function with(): array
    {
        return [
            'banners' => Banner::query()
                ->when($this->search, fn($q) => $q->where('title', 'like', '%' . $this->search . '%'))
                ->orderBy('order')
                ->paginate(10),
        ];
    }
} ?>

<div class="p-3 md:p-6 lg:p-10 space-y-6 md:space-y-8">
    <!-- Header -->
    <x-admin.page-header 
        title="Hero Banners"
        description="Kelola banner yang akan tampil di halaman utama website publik."
    >
        <x-slot:action>
            <flux:button variant="primary" icon="plus" :href="route('admin.banners.create')" wire:navigate>
                {{ __('Tambah Banner') }}
            </flux:button>
        </x-slot:action>
    </x-admin.page-header>

    <!-- Toolbar -->
    <div class="flex flex-col md:flex-row gap-4 items-center">
        <div class="w-full md:w-1/3">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari judul banner..." icon="magnifying-glass" />
        </div>
    </div>

    <!-- Table -->
    <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-zinc-50 dark:bg-zinc-900/50 text-zinc-500 font-medium border-b border-zinc-200 dark:border-zinc-800">
                    <tr>
                        <th class="px-6 py-3 w-48">{{ __('Preview') }}</th>
                        <th class="px-6 py-3">{{ __('Informasi Banner') }}</th>
                        <th class="px-6 py-3 text-center">{{ __('Urutan') }}</th>
                        <th class="px-6 py-3 text-center">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                    @forelse($banners as $banner)
                        <tr class="group hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors" wire:key="{{ $banner->id }}">
                            <td class="px-6 py-4">
                                @if($banner->image_path)
                                    <img src="{{ $banner->image_path }}" alt="{{ $banner->title }}" class="h-20 w-32 object-cover rounded-lg border border-zinc-200 dark:border-zinc-800 shadow-sm" />
                                @else
                                    <div class="h-20 w-32 bg-zinc-100 dark:bg-zinc-800 rounded-lg flex items-center justify-center text-zinc-400">
                                        <flux:icon.photo class="size-6" />
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="font-bold text-zinc-900 dark:text-white line-clamp-1 truncate max-w-sm">{{ $banner->title ?? 'Tanpa Judul' }}</span>
                                    <span class="text-xs text-zinc-500 line-clamp-1 truncate max-w-sm">{{ $banner->subtitle }}</span>
                                    @if($banner->button_link)
                                        <span class="text-[10px] text-primary font-mono uppercase tracking-widest mt-1">{{ $banner->button_link }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="font-mono text-zinc-600 dark:text-zinc-400 font-bold bg-zinc-100 dark:bg-zinc-800 px-2.5 py-1 rounded-md text-xs">
                                    {{ $banner->order }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button wire:click="toggleStatus({{ $banner->id }})" class="focus:outline-none">
                                    <flux:badge size="sm" :color="$banner->is_active ? 'success' : 'zinc'" inset="top bottom" class="cursor-pointer hover:opacity-80 transition-opacity">
                                        {{ $banner->is_active ? __('Aktif') : __('Nonaktif') }}
                                    </flux:badge>
                                </button>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button icon="pencil-square" size="xs" variant="ghost" :href="route('admin.banners.edit', $banner)" wire:navigate />
                                    <flux:button icon="trash" size="xs" variant="ghost" class="text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20" wire:click="delete({{ $banner->id }})" wire:confirm="Yakin ingin menghapus banner ini?" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <flux:icon.magnifying-glass class="size-8 text-zinc-300" />
                                    <p class="text-zinc-500 text-sm font-medium">{{ __('Tidak ada banner ditemukan.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($banners->hasPages())
        <div class="border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 p-4">
            {{ $banners->links() }}
        </div>
        @endif
    </div>
</div>
