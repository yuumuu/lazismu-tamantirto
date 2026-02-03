<?php

use App\Models\MediaLibrary;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $type = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'type' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete($id): void
    {
        $media = MediaLibrary::findOrFail($id);
        // Hard deletion of file would be handled by a service, 
        // for now just DB record for simplicity or call MediaService if available.
        $media->delete();
        $this->dispatch('notify', message: 'File dihapus dari perpustakaan.', type: 'info');
    }

    public function with(): array
    {
        return [
            'assets' => MediaLibrary::query()
                ->when($this->search, fn($q) => $q->where('file_name', 'like', '%' . $this->search . '%'))
                ->when($this->type, fn($q) => $q->where('file_type', 'like', $this->type . '%'))
                ->latest()
                ->paginate(24),
        ];
    }
} ?>

<div>
    <x-admin.page-header 
        title="Galeri Media & Dokumen" 
        description="Gunakan file-file di bawah ini untuk link berita atau kampanye." 
    />

    <div class="p-3 md:p-6 lg:p-10 space-y-8">
        <!-- Filters -->
        <div class="flex flex-col md:flex-row gap-4 items-end">
            <div class="w-full md:w-1/3">
                <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama file..." icon="magnifying-glass" />
            </div>
            <div class="w-full md:w-1/4">
                <flux:select wire:model.live="type" placeholder="Semua Tipe">
                    <option value="">{{ __('Semua Tipe') }}</option>
                    <option value="image">{{ __('Gambar') }}</option>
                    <option value="application">{{ __('Dokumen/PDF') }}</option>
                </flux:select>
            </div>
        </div>

        <!-- Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
            @forelse($assets as $asset)
                <div class="group relative bg-white dark:bg-zinc-900 rounded-2xl border border-zinc-200 dark:border-zinc-800 overflow-hidden shadow-sm aspect-square flex flex-col">
                    <div class="flex-1 overflow-hidden bg-zinc-50 dark:bg-zinc-950 flex items-center justify-center">
                        @if(str_starts_with($asset->file_type, 'image'))
                            <img src="{{ $asset->file_url }}" class="size-full object-cover transition-transform group-hover:scale-110">
                        @else
                            <flux:icon name="document" class="size-12 text-zinc-300" />
                        @endif
                    </div>
                    
                    <div class="p-2 bg-white dark:bg-zinc-900 border-t border-zinc-100 dark:border-zinc-800">
                        <p class="text-[10px] text-zinc-600 dark:text-zinc-400 truncate font-medium">{{ $asset->file_name }}</p>
                        <p class="text-[8px] text-zinc-400 mt-0.5 uppercase">{{ $asset->formatted_size }}</p>
                    </div>

                    <!-- Overlay Actions -->
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                        <flux:button variant="ghost" size="xs" icon="eye" :href="$asset->file_url" target="_blank" class="text-white hover:bg-white/20" />
                        <flux:button variant="ghost" size="xs" icon="trash" wire:click="delete('{{ $asset->id }}')" wire:confirm="Hapus file ini permanen?" class="text-red-400 hover:bg-red-500/20" />
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 text-center text-zinc-500 italic">
                    {{ __('Belum ada file di pustaka media.') }}
                </div>
            @endforelse
        </div>

        <div class="pt-6">
            {{ $assets->links() }}
        </div>
    </div>
</div>
