<?php

declare(strict_types=1);

use App\Models\Branch;
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
            'branches' => Branch::query()
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

    public function delete(Branch $branch): void
    {
        if ($branch->id === 1) {
            $this->dispatch('notify', message: 'Cabang Pusat tidak dapat dihapus.', type: 'error');
            return;
        }

        $branch->delete();
        $this->dispatch('notify', message: 'Data Cabang berhasil dihapus.', type: 'success');
    }

    public function toggleStatus(Branch $branch): void
    {
        $branch->update(['is_active' => !$branch->is_active]);
        $this->dispatch('notify', message: 'Status Cabang diperbarui.', type: 'success');
    }

    public function impersonateAdmin(Branch $branch): void
    {
        $admin = \App\Models\User::withoutGlobalScope('branch')
            ->where('branch_id', $branch->id)
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
    <title>Manajemen Cabang - {{ ucwords(config('app.name')) }}</title>
@endpush

<div class="p-3 md:p-6 lg:p-10 space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
        <div class="space-y-2">
            <div class="flex items-center gap-2">
                <div class="h-6 w-1 bg-primary rounded-full"></div>
                <h1 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white">{{ __('Manajemen Cabang') }}</h1>
            </div>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm max-w-lg border-l pl-4 border-zinc-200 dark:border-zinc-800">
                {{ __('Kelola unit-unit cabang, masjid, dan lembaga Lazismu di bawah koordinasi Pusat.') }}
            </p>
        </div>
        <flux:button variant="primary" icon="plus" :href="route('admin.branches.create')" wire:navigate>
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
                        <th class="px-6 py-3">{{ __('Tipe') }}</th>
                        <th class="px-6 py-3">{{ __('Kontak') }}</th>
                        <th class="px-6 py-3 text-center">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                    @forelse($branches as $branch)
                        <tr class="group hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors" wire:key="{{ $branch->id }}">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-zinc-900 dark:text-white">{{ $branch->name }}</span>
                                    <span class="text-xs text-zinc-500 font-mono">{{ $branch->slug }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <flux:badge size="sm" variant="pill">{{ ucwords(str_replace('_', ' ', $branch->type)) }}</flux:badge>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    @if($branch->phone)
                                        <div class="flex items-center gap-2 text-zinc-600 dark:text-zinc-400">
                                            <flux:icon name="phone" class="size-3" />
                                            <span class="text-xs">{{ $branch->phone }}</span>
                                        </div>
                                    @endif
                                    @if($branch->email)
                                        <div class="flex items-center gap-2 text-zinc-600 dark:text-zinc-400">
                                            <flux:icon name="envelope" class="size-3" />
                                            <span class="text-xs">{{ $branch->email }}</span>
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <flux:switch wire:click="toggleStatus('{{ $branch->id }}')" :checked="$branch->is_active" :disabled="$branch->id === 1" />
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($branch->id !== 1)
                                        <flux:button icon="arrow-right-end-on-rectangle" size="xs" variant="ghost" class="text-zinc-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20" 
                                            wire:click="impersonateAdmin('{{ $branch->id }}')" 
                                            onclick="if(!confirm('Masuk sebagai admin cabang ini?')) { event.stopImmediatePropagation(); return false; }" 
                                            title="Login sebagai Admin" />
                                    @endif
                                    <flux:button icon="pencil-square" size="xs" variant="ghost" :href="route('admin.branches.edit', $branch)" wire:navigate />
                                    <flux:button icon="trash" size="xs" variant="ghost" class="text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20" 
                                        wire:click="delete('{{ $branch->id }}')" 
                                        onclick="if(!confirm('Yakin ingin menghapus cabang ini? Semua data terkait akan tetap ada namun tidak bisa diakses.')) { event.stopImmediatePropagation(); return false; }" 
                                        :disabled="$branch->id === 1" />
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
            {{ $branches->links() }}
        </div>
    </div>
</div>
