<?php

use App\Models\Distributor;
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

    public function delete(string $id): void
    {
        Distributor::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Data Distributor berhasil dihapus.', type: 'success');
    }

    public function toggleStatus(string $id): void
    {
        $distributor = Distributor::findOrFail($id);
        $distributor->update(['is_active' => !$distributor->is_active]);
        $this->dispatch('notify', message: 'Status Distributor diperbarui.', type: 'success');
    }

    public function with(): array
    {
        return [
            'distributors' => Distributor::query()
                ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('phone', 'like', '%' . $this->search . '%')
                    ->orWhere('region', 'like', '%' . $this->search . '%'))
                ->when($this->type, fn($q) => $q->where('type', $this->type))
                ->latest()
                ->paginate(10),
        ];
    }
} ?>

@push('head')
    <title>Jaringan Distributor - {{ ucwords(config('app.name')) }}</title>
@endpush

<div class="p-3 md:p-6 lg:p-10 space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
        <div class="space-y-2">
             <div class="flex items-center gap-2">
                <div class="h-6 w-1 bg-primary rounded-full"></div>
                <h1 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white">{{ __('Jaringan Distributor') }}</h1>
            </div>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm max-w-lg border-l pl-4 border-zinc-200 dark:border-zinc-800">
                {{ __('Manajemen sumber daya manusia dan partner penyaluran amanah zakat.') }}
            </p>
        </div>
        <flux:button variant="primary" icon="plus" :href="route('admin.distributors.create')" wire:navigate>
            {{ __('Tambah Penyalur') }}
        </flux:button>
    </div>

    <!-- Toolbar -->
    <div class="flex flex-col md:flex-row gap-4 items-center">
        <div class="w-full md:w-1/3">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama, telepon..." icon="magnifying-glass" />
        </div>
        <div class="w-full md:w-1/4">
            <flux:select wire:model.live="type" placeholder="Semua Tipe">
                <option value="">{{ __('Semua Tipe') }}</option>
                <option value="individu">{{ __('Individu') }}</option>
                <option value="lembaga">{{ __('Lembaga') }}</option>
                <option value="mesjid">{{ __('Mesjid') }}</option>
            </flux:select>
        </div>
    </div>

    <!-- Table -->
    <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-zinc-50 dark:bg-zinc-900/50 text-zinc-500 font-medium border-b border-zinc-200 dark:border-zinc-800">
                    <tr>
                        <th class="px-6 py-3">{{ __('Nama Penyalur') }}</th>
                        <th class="px-6 py-3">{{ __('Kontak') }}</th>
                        <th class="px-6 py-3">{{ __('Wilayah/Alamat') }}</th>
                        <th class="px-6 py-3 text-center">{{ __('Tipe') }}</th>
                        <th class="px-6 py-3 text-center">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                    @forelse($distributors as $distributor)
                        <tr class="group hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors" wire:key="{{ $distributor->id }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="size-10 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-400 font-black text-xs group-hover:bg-primary/10 group-hover:text-primary transition-colors">
                                        {{ substr($distributor->name, 0, 1) }}
                                    </div>
                                    <span class="font-bold text-zinc-900 dark:text-white">{{ $distributor->name }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($distributor->phone)
                                    <div class="flex items-center gap-2 text-zinc-600 dark:text-zinc-400">
                                        <flux:icon name="phone" class="size-3" />
                                        <span class="text-xs font-mono">{{ $distributor->phone }}</span>
                                    </div>
                                @else
                                    <span class="text-zinc-400 text-xs">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-zinc-600 dark:text-zinc-400 text-xs line-clamp-1 max-w-[200px]">{{ $distributor->address ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <flux:badge size="sm" color="zinc" inset="top bottom">{{ ucfirst($distributor->type) }}</flux:badge>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <flux:switch wire:click="toggleStatus('{{ $distributor->id }}')" :checked="$distributor->is_active" />
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button icon="pencil-square" size="xs" variant="ghost" :href="route('admin.distributors.edit', $distributor)" wire:navigate />
                                    <flux:button icon="trash" size="xs" variant="ghost" wire:click="delete('{{ $distributor->id }}')" wire:confirm="Yakin ingin menghapus data ini?" class="text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <flux:icon name="magnifying-glass" class="size-8 text-zinc-300" />
                                    <p class="text-zinc-500 text-sm font-medium">{{ __('Tidak ada penyalur ditemukan.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 p-4">
            {{ $distributors->links() }}
        </div>
    </div>
</div>
