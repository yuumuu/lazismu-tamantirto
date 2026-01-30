<?php

use App\Models\Mustahik;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    public function with(): array
    {
        return [
            'mustahiks' => Mustahik::query()
                ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('nik', 'like', '%' . $this->search . '%')
                    ->orWhere('family_card_number', 'like', '%' . $this->search . '%'))
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate(10),
        ];
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function delete(Mustahik $mustahik): void
    {
        $mustahik->delete();
        $this->dispatch('notify', message: 'Data Mustahik berhasil dihapus.', type: 'success');
    }

    public function toggleStatus(Mustahik $mustahik): void
    {
        $mustahik->update(['is_active' => !$mustahik->is_active]);
        $this->dispatch('notify', message: 'Status Mustahik diperbarui.', type: 'success');
    }
}; ?>

<div class="p-3 md:p-6 lg:p-10 space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
        <div class="space-y-2">
             <div class="flex items-center gap-2">
                <div class="h-6 w-1 bg-primary rounded-full"></div>
                <h1 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white">{{ __('Arsip Mustahik') }}</h1>
            </div>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm max-w-lg leading-relaxed border-l pl-4 border-zinc-200 dark:border-zinc-800">
                {{ __('Database penerima manfaat terklasifikasi berdasarkan asnaf.') }}
            </p>
        </div>
        <flux:button variant="primary" icon="plus" :href="route('admin.mustahiks.create')" wire:navigate>
            {{ __('Tambah Mustahik') }}
        </flux:button>
    </div>

    <!-- Toolbar -->
    <div class="flex flex-col sm:flex-row gap-4 justify-between items-center">
        <div class="w-full sm:w-72">
            <flux:input icon="magnifying-glass" wire:model.live.debounce.300ms="search" placeholder="Cari nama, NIK, atau KK..." />
        </div>
        <div class="flex items-center gap-2">
             <flux:button icon="funnel" variant="subtle" size="sm">{{ __('Filter') }}</flux:button>
        </div>
    </div>

    <!-- Table -->
    <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-zinc-50 dark:bg-zinc-900/50 text-zinc-500 font-medium border-b border-zinc-200 dark:border-zinc-800">
                    <tr>
                        <th class="px-6 py-3 cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-300 transition-colors" wire:click="sortBy('name')">
                            <div class="flex items-center gap-1">
                                {{ __('Nama & Identitas') }}
                                <flux:icon name="chevron-up-down" class="size-3 opacity-50" />
                            </div>
                        </th>
                        <th class="px-6 py-3 cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-300 transition-colors" wire:click="sortBy('asnaf_type')">
                            <div class="flex items-center gap-1">
                                {{ __('Asnaf') }}
                                <flux:icon name="chevron-up-down" class="size-3 opacity-50" />
                            </div>
                        </th>
                        <th class="px-6 py-3">{{ __('Wilayah/Alamat') }}</th>
                        <th class="px-6 py-3 text-center">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                    @forelse($mustahiks as $mustahik)
                        <tr class="group hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors" wire:key="{{ $mustahik->id }}">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-zinc-900 dark:text-white">{{ $mustahik->name }}</span>
                                    <div class="flex items-center gap-2 text-xs text-zinc-500 font-mono mt-1">
                                        <span class="bg-zinc-100 dark:bg-zinc-800 px-1 rounded">NIK: {{ $mustahik->nik ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <flux:badge size="sm" color="zinc" inset="top bottom">{{ $mustahik->asnaf_type?->label() ?? '-' }}</flux:badge>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-zinc-600 dark:text-zinc-400 text-sm line-clamp-2 max-w-[200px]">{{ $mustahik->address ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <flux:switch wire:click="toggleStatus('{{ $mustahik->id }}')" :checked="$mustahik->is_active" />
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button icon="pencil-square" size="xs" variant="ghost" :href="route('admin.mustahiks.edit', $mustahik)" wire:navigate />
                                    <flux:button icon="trash" size="xs" variant="ghost" class="text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20" wire:click="delete('{{ $mustahik->id }}')" wire:confirm="Yakin ingin menghapus data ini?" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <flux:icon name="magnifying-glass" class="size-8 text-zinc-300" />
                                    <p class="text-zinc-500 text-sm font-medium">{{ __('Tidak ada data ditemukan.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 p-4">
            {{ $mustahiks->links() }}
        </div>
    </div>
</div>



