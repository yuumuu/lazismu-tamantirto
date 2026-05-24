<?php

declare(strict_types=1);

use App\Models\Masjid;
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
            'masjids' => Masjid::query()
                ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('slug', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%'))
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

    public function delete(Masjid $masjid): void
    {
        if ($masjid->id === 1) {
            $this->dispatch('notify', message: 'Cabang Pusat tidak dapat dihapus.', type: 'error');
            return;
        }

        $masjid->delete();
        $this->dispatch('notify', message: 'Data Cabang/Masjid berhasil dihapus.', type: 'success');
    }

    public function toggleStatus(Masjid $masjid): void
    {
        $masjid->update(['is_active' => !$masjid->is_active]);
        $this->dispatch('notify', message: 'Status Cabang/Masjid diperbarui.', type: 'success');
    }

    public function impersonateAdmin(Masjid $masjid): void
    {
        $admin = \App\Models\User::withoutGlobalScope('masjid')
            ->where('masjid_id', $masjid->id)
            ->role('admin')
            ->first();

        if (! $admin) {
            $this->dispatch('notify', message: 'Tidak ada admin ditemukan untuk cabang ini.', type: 'error');
            return;
        }

        $this->redirect(route('admin.impersonate', $admin), navigate: false);
    }
}; ?>

@push('head')
    <title>Manajemen Cabang & Masjid - {{ ucwords(config('app.name')) }}</title>
@endpush

<div class="p-3 md:p-6 lg:p-10 space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
        <div class="space-y-2">
            <div class="flex items-center gap-2">
                <div class="h-6 w-1 bg-primary rounded-full"></div>
                <h1 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white">{{ __('Manajemen Cabang & Masjid') }}</h1>
            </div>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm max-w-lg leading-relaxed border-l pl-4 border-zinc-200 dark:border-zinc-800">
                {{ __('Kelola unit-unit masjid dan cabang Lazismu di bawah koordinasi Pusat.') }}
            </p>
        </div>
        <flux:button variant="primary" icon="plus" :href="route('admin.masjids.create')" wire:navigate>
            {{ __('Tambah Cabang') }}
        </flux:button>
    </div>

    <!-- Toolbar -->
    <div class="flex flex-col sm:flex-row gap-4 justify-between items-center">
        <div class="w-full sm:w-72">
            <flux:input icon="magnifying-glass" wire:model.live.debounce.300ms="search" placeholder="Cari nama, slug, atau email..." />
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
                                {{ __('Nama & Slug') }}
                                <flux:icon name="chevron-up-down" class="size-3 opacity-50" />
                            </div>
                        </th>
                        <th class="px-6 py-3">{{ __('Kontak') }}</th>
                        <th class="px-6 py-3 cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-300 transition-colors" wire:click="sortBy('email')">
                             <div class="flex items-center gap-1">
                                {{ __('Email') }}
                                <flux:icon name="chevron-up-down" class="size-3 opacity-50" />
                            </div>
                        </th>
                        <th class="px-6 py-3 text-center">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                    @forelse($masjids as $masjid)
                        <tr class="group hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors" wire:key="{{ $masjid->id }}">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-zinc-900 dark:text-white">{{ $masjid->name }}</span>
                                    <span class="text-xs text-zinc-500 font-mono">{{ $masjid->slug }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    @if($masjid->phone)
                                        <div class="flex items-center gap-2 text-zinc-600 dark:text-zinc-400">
                                            <flux:icon name="phone" class="size-3" />
                                            <span class="text-xs">{{ $masjid->phone }}</span>
                                        </div>
                                    @endif
                                    @if($masjid->address)
                                        <div class="flex items-center gap-2 text-zinc-600 dark:text-zinc-400">
                                            <flux:icon name="map-pin" class="size-3" />
                                            <span class="text-xs truncate max-w-[200px]">{{ $masjid->address }}</span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs text-zinc-600 dark:text-zinc-400">{{ $masjid->email ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <flux:switch wire:click="toggleStatus('{{ $masjid->id }}')" :checked="$masjid->is_active" :disabled="$masjid->id === 1" />
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($masjid->id !== 1)
                                        <flux:button icon="arrow-right-end-on-rectangle" size="xs" variant="ghost" class="text-zinc-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20" 
                                            wire:click="impersonateAdmin('{{ $masjid->id }}')" 
                                            onclick="if(!confirm('Masuk sebagai admin cabang ini?')) { event.stopImmediatePropagation(); return false; }" 
                                            title="Login sebagai Admin" />
                                    @endif
                                    <flux:button icon="pencil-square" size="xs" variant="ghost" :href="route('admin.masjids.edit', $masjid)" wire:navigate />
                                    <flux:button icon="trash" size="xs" variant="ghost" class="text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20" 
                                        wire:click="delete('{{ $masjid->id }}')" 
                                        onclick="if(!confirm('Yakin ingin menghapus cabang ini? Semua data terkait akan tetap ada namun tidak bisa diakses.')) { event.stopImmediatePropagation(); return false; }" 
                                        :disabled="$masjid->id === 1" />
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
            {{ $masjids->links() }}
        </div>
    </div>
</div>
