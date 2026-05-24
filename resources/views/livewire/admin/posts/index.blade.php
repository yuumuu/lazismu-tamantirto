<?php

use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Enums\PostStatus;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $category = '';
    public $status = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => ''],
        'status' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function toggleFeatured(string $id): void
    {
        $post = BlogPost::findOrFail($id);
        $post->update(['is_featured' => !$post->is_featured]);
        $this->dispatch('notify', message: 'Status unggulan diperbarui.', type: 'success');
    }

    public function delete(string $id): void
    {
        BlogPost::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Artikel berhasil dihapus.', type: 'success');
    }

    public function with(): array
    {
        return [
            'posts' => BlogPost::query()
                ->with(['category', 'author'])
                ->when($this->search, fn($q) => $q->where('title', 'like', '%' . $this->search . '%'))
                ->when($this->category, fn($q) => $q->where('category_id', $this->category))
                ->when($this->status, fn($q) => $q->where('status', $this->status))
                ->latest()
                ->paginate(15),
            'categories' => BlogCategory::active()->ordered()->get(),
            'statuses' => PostStatus::cases(),
        ];
    }
} ?>

<div class="p-3 md:p-6 lg:p-10 space-y-6 md:space-y-8">
    <!-- Header -->
    <x-admin.page-header 
        title="Berita & Artikel"
        description="Kelola berita, artikel edukasi, dan inspirasi donasi."
    >
        <x-slot:action>
            <flux:button variant="primary" icon="plus" :href="route('admin.posts.create')" wire:navigate>
                {{ __('Tulis Artikel') }}
            </flux:button>
        </x-slot:action>
    </x-admin.page-header>

    <!-- Toolbar -->
    <div class="flex flex-col md:flex-row gap-4 items-center">
        <div class="w-full md:w-1/3">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari judul artikel..." icon="magnifying-glass" />
        </div>
        <div class="w-full md:w-1/4">
            <flux:select wire:model.live="category" placeholder="Semua Kategori">
                <option value="">{{ __('Semua Kategori') }}</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </flux:select>
        </div>
        <div class="w-full md:w-1/4">
            <flux:select wire:model.live="status" placeholder="Semua Status">
                <option value="">{{ __('Semua Status') }}</option>
                @foreach($statuses as $s)
                    <option value="{{ $s->value }}">{{ $s->label() }}</option>
                @endforeach
            </flux:select>
        </div>
    </div>

    <!-- Table -->
    <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-xs">
        <table class="w-full text-sm text-left">
            <thead class="bg-zinc-50 dark:bg-zinc-900/50 text-zinc-500 font-medium border-b border-zinc-200 dark:border-zinc-800">
                <tr>
                    <th class="px-6 py-3">{{ __('Artikel') }}</th>
                    <th class="px-6 py-3">{{ __('Kategori') }}</th>
                    <th class="px-6 py-3">{{ __('Views') }}</th>
                    <th class="px-6 py-3">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                @forelse($posts as $post)
                <tr class="group hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <img src="{{ $post->featured_image_url ?? '/images/placeholder-post.jpg' }}" class="size-12 rounded-lg object-cover bg-zinc-100">
                            <div class="flex flex-col">
                                <span class="font-medium text-zinc-900 dark:text-white line-clamp-1">{{ $post->title }}</span>
                                <span class="text-[10px] text-zinc-500">{{ $post->author?->name ?? 'Admin' }} • {{ $post->created_at->format('d M Y') }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <flux:badge size="sm" variant="outline">{{ $post->category?->name ?? 'Uncategorized' }}</flux:badge>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-1 text-zinc-500">
                            <flux:icon name="eye" class="size-4" />
                            <span>{{ $post->view_count }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <flux:badge :color="$post->status->color()" size="sm" inset="top">
                                {{ $post->status->label() }}
                            </flux:badge>
                            @if($post->is_featured)
                                <flux:tooltip content="Muncul di Hero/Featured">
                                    <flux:icon name="star" variant="solid" class="size-4 text-amber-500" />
                                </flux:tooltip>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <flux:button variant="ghost" size="sm" icon="pencil-square" :href="route('admin.posts.edit', $post)" wire:navigate />
                            <flux:button variant="ghost" size="sm" icon="trash" wire:click="delete('{{ $post->id }}')" wire:confirm="Hapus artikel ini?" class="text-red-500" />
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-zinc-500">{{ __('Belum ada artikel.') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="p-6 border-t border-zinc-100 dark:border-zinc-800">
            {{ $posts->links() }}
        </div>
    </div>
</div>
