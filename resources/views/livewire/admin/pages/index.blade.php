<?php

use App\Models\Page;
use App\Enums\PageStatus;
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

    public function toggleStatus(string $id): void
    {
        $page = Page::findOrFail($id);
        if ($page->status === PageStatus::Published) {
            $page->unpublish();
            $this->dispatch('notify', message: 'Halaman diarsipkan (Draft).', type: 'info');
        } else {
            $page->publish();
            $this->dispatch('notify', message: 'Halaman dipublikasikan.', type: 'success');
        }
    }

    public function delete(string $id): void
    {
        $page = Page::findOrFail($id);
        
        if ($page->is_homepage) {
            $this->dispatch('notify', message: 'Halaman Beranda tidak dapat dihapus.', type: 'error');
            return;
        }

        $page->delete();
        $this->dispatch('notify', message: 'Halaman berhasil dihapus.', type: 'success');
    }

    public function with(): array
    {
        return [
            'pages' => Page::query()
                ->when($this->search, fn($q) => $q->where('title', 'like', '%' . $this->search . '%'))
                ->ordered()
                ->paginate(15),
        ];
    }
} ?>

<div class="p-3 md:p-6 lg:p-10 space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
        <div class="space-y-2">
            <div class="flex items-center gap-2">
                <div class="h-6 w-1 bg-primary rounded-full"></div>
                <h1 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white">{{ __('Halaman Statis') }}</h1>
            </div>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm max-w-lg leading-relaxed border-l pl-4 border-zinc-200 dark:border-zinc-800">
                {{ __('Kelola konten halaman seperti About, Visi Misi, Prosedur, dsb.') }}
            </p>
        </div>
        <flux:button variant="primary" icon="plus" :href="route('admin.pages.create')" wire:navigate>
            {{ __('Buat Halaman') }}
        </flux:button>
    </div>

    <!-- Toolbar -->
    <div class="w-full md:w-72">
        <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari judul halaman..." icon="magnifying-glass" />
    </div>

    <!-- Table -->
    <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-xs">
        <table class="w-full text-sm text-left">
            <thead class="bg-zinc-50 dark:bg-zinc-900/50 text-zinc-500 font-medium border-b border-zinc-200 dark:border-zinc-800">
                <tr>
                    <th class="px-6 py-3 font-medium">{{ __('Judul / URL') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('Layout / Template') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('Status') }}</th>
                    <th class="px-6 py-3 font-medium">{{ __('Terakhir Update') }}</th>
                    <th class="px-6 py-3 font-medium text-right">{{ __('Aksi') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                @forelse($pages as $page)
                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-medium text-zinc-900 dark:text-white">{{ $page->title }}</span>
                            <span class="text-[10px] text-zinc-500">/{{ $page->slug }}</span>
                            @if($page->is_homepage)
                                <flux:badge size="sm" color="blue" variant="outline" class="w-fit mt-1">Homepage</flux:badge>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-zinc-600 dark:text-zinc-400 font-mono text-xs">{{ $page->template }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <flux:badge :color="$page->status === PageStatus::Published ? 'green' : 'zinc'" size="sm" inset="top">
                            {{ $page->status->value }}
                        </flux:badge>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-zinc-500 dark:text-zinc-400">{{ $page->updated_at->diffForHumans() }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <flux:button variant="ghost" size="sm" icon="eye" :href="'/pages/' . $page->slug" target="_blank" />
                            <flux:button variant="ghost" size="sm" icon="pencil-square" :href="route('admin.pages.edit', $page)" wire:navigate />
                            <flux:button variant="ghost" size="sm" icon="trash" wire:click="delete('{{ $page->id }}')" wire:confirm="Hapus halaman ini?" class="text-red-500" />
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-zinc-500">{{ __('Belum ada halaman.') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="p-6 border-t border-zinc-100 dark:border-zinc-800">
            {{ $pages->links() }}
        </div>
    </div>
</div>
