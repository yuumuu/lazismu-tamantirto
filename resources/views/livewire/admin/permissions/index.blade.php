<?php

use Spatie\Permission\Models\Permission;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';

    public function with(): array
    {
        return [
            'permissions' => Permission::query()
                ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                ->orderBy('name', 'asc')
                ->paginate(15),
        ];
    }

    public function delete(string $id): void
    {
        Permission::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Izin berhasil dihapus.', type: 'success');
    }
}; ?>

<div class="p-3 md:p-6 lg:p-10 space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
        <div class="space-y-2">
             <div class="flex items-center gap-2">
                <div class="h-6 w-1 bg-primary rounded-full"></div>
                <h1 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white">{{ __('Manajemen Izin') }}</h1>
            </div>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm max-w-lg leading-relaxed border-l pl-4 border-zinc-200 dark:border-zinc-800">
                {{ __('Definisikan hak akses fitur sistem untuk kontrol keamanan.') }}
            </p>
        </div>
        <flux:button variant="primary" icon="plus" :href="route('admin.permissions.create')" wire:navigate>
            {{ __('Daftarkan Izin') }}
        </flux:button>
    </div>

    <!-- Toolbar -->
    <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
        <div class="w-full sm:w-72">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari izin..." icon="magnifying-glass" />
        </div>
    </div>

    <!-- Table -->
    <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-zinc-50 dark:bg-zinc-900/50 text-zinc-500 font-medium border-b border-zinc-200 dark:border-zinc-800">
                    <tr>
                        <th class="px-6 py-3">{{ __('Nama Izin') }}</th>
                        <th class="px-6 py-3">{{ __('Guard') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                    @forelse($permissions as $permission)
                        <tr class="group hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors" wire:key="{{ $permission->id }}">
                            <td class="px-6 py-4">
                                <span class="font-mono font-bold text-zinc-700 dark:text-zinc-300 bg-zinc-100 dark:bg-zinc-800 px-2 py-1 rounded text-xs">
                                    {{ $permission->name }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-mono text-[10px] uppercase text-zinc-400">{{ $permission->guard_name }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button icon="pencil-square" size="xs" variant="ghost" :href="route('admin.permissions.edit', $permission)" wire:navigate />
                                    <flux:button icon="trash" size="xs" variant="ghost" class="text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20" wire:click="delete('{{ $permission->id }}')" wire:confirm="Hapus izin ini?" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <flux:icon name="magnifying-glass" class="size-8 text-zinc-300" />
                                    <p class="text-zinc-500 text-sm font-medium">{{ __('Tidak ada izin ditemukan.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 p-4">
            {{ $permissions->links() }}
        </div>
    </div>
</div>
