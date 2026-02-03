<?php

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $activeTab = 'roles';

    public function selectTab($tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function with(): array
    {
        if ($this->activeTab === 'roles') {
            return [
                'roles' => Role::query()
                    ->withCount('permissions')
                    ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                    ->orderBy('name', 'asc')
                    ->paginate(10),
            ];
        }

        return [
            'permissions' => Permission::query()
                ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%'))
                ->orderBy('name', 'asc')
                ->paginate(15),
        ];
    }

    public function deleteRole(string $id): void
    {
        $role = Role::findOrFail($id);
        if ($role->name === 'super-admin') {
             $this->dispatch('notify', message: 'Role Super Admin tidak dapat dihapus.', type: 'error');
             return;
        }
        
        $role->delete();
        $this->dispatch('notify', message: 'Role berhasil dihapus.', type: 'success');
    }

    public function deletePermission(string $id): void
    {
        Permission::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Izin berhasil dihapus.', type: 'success');
    }
}; ?>

@push('head')
    <title>Manajemen Akses - {{ ucwords(config('app.name')) }}</title>
@endpush

<div class="p-3 md:p-6 lg:p-10 space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
        <div class="space-y-2">
             <div class="flex items-center gap-2">
                <div class="h-6 w-1 bg-primary rounded-full"></div>
                <h1 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white">{{ __('Manajemen Akses') }}</h1>
            </div>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm max-w-lg leading-relaxed border-l pl-4 border-zinc-200 dark:border-zinc-800">
                {{ __('Kelola tingkatan akses dan izin fitur untuk menjaga keamanan data dan integritas sistem.') }}
            </p>
        </div>
        @if($activeTab === 'roles')
            <flux:button variant="primary" icon="plus" :href="route('admin.roles.create')" wire:navigate>
                {{ __('Buat Role Baru') }}
            </flux:button>
        @else
            <flux:button variant="primary" icon="plus" :href="route('admin.permissions.create')" wire:navigate>
                {{ __('Daftarkan Izin') }}
            </flux:button>
        @endif
    </div>

    <!-- Tabs -->
    <div class="flex gap-4 border-b border-zinc-200 dark:border-zinc-800">
        <button wire:click="selectTab('roles')" class="px-4 py-2 text-sm font-bold border-b-2 transition-colors {{ $activeTab === 'roles' ? 'border-primary text-primary' : 'border-transparent text-zinc-500 hover:text-zinc-700' }}">
            {{ __('Daftar Role') }}
        </button>
        <button wire:click="selectTab('permissions')" class="px-4 py-2 text-sm font-bold border-b-2 transition-colors {{ $activeTab === 'permissions' ? 'border-primary text-primary' : 'border-transparent text-zinc-500 hover:text-zinc-700' }}">
            {{ __('Daftar Izin (Permissions)') }}
        </button>
    </div>

    <!-- Toolbar -->
    <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
        <div class="w-full sm:w-72">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari..." icon="magnifying-glass" />
        </div>
    </div>

    @if($activeTab === 'roles')
        <!-- Roles Table -->
        <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-xs">
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-zinc-50 dark:bg-zinc-900/50 text-zinc-500 font-medium border-b border-zinc-200 dark:border-zinc-800">
                        <tr>
                            <th class="px-6 py-3">{{ __('Nama Role') }}</th>
                            <th class="px-6 py-3">{{ __('Guard') }}</th>
                            <th class="px-6 py-3">{{ __('Izin Akses') }}</th>
                            <th class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                        @forelse($roles as $role)
                            <tr class="group hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors" wire:key="role-{{ $role->id }}">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="size-8 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-400 font-black text-xs group-hover:bg-primary/10 group-hover:text-primary transition-colors uppercase">
                                            {{ substr($role->name, 0, 1) }}
                                        </div>
                                        <span class="font-bold text-zinc-900 dark:text-white uppercase">{{ $role->name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="font-mono text-xs text-zinc-500 bg-zinc-100 dark:bg-zinc-800 px-2 py-1 rounded">{{ $role->guard_name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <flux:icon name="key" class="size-4 text-zinc-400" />
                                        <span class="text-zinc-900 dark:text-white font-medium">{{ $role->permissions_count }}</span>
                                        <span class="text-zinc-500 text-xs">Permission aktif</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if($role->name !== 'super-admin')
                                            <flux:button icon="pencil-square" size="xs" variant="ghost" :href="route('admin.roles.edit', $role)" wire:navigate />
                                            <flux:button icon="trash" size="xs" variant="ghost" class="text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20" wire:click="deleteRole('{{ $role->id }}')" wire:confirm="Hapus role ini? User dengan role ini akan kehilangan akses." />
                                        @else
                                            <span class="text-xs text-zinc-400 italic pr-2">System Restricted</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-12 text-center text-zinc-500">{{ __('Tidak ada role ditemukan.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 p-4">
                {{ $roles->links() }}
            </div>
        </div>
    @else
        <!-- Permissions Table -->
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
                            <tr class="group hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors" wire:key="perm-{{ $permission->id }}">
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
                                        <flux:button icon="trash" size="xs" variant="ghost" class="text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20" wire:click="deletePermission('{{ $permission->id }}')" wire:confirm="Hapus izin ini?" />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-6 py-12 text-center text-zinc-500">{{ __('Tidak ada izin ditemukan.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 p-4">
                {{ $permissions->links() }}
            </div>
        </div>
    @endif
</div>
