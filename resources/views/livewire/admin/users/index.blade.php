<?php

use App\Models\User;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';

    public function with(): array
    {
        return [
            'users' => User::query()
                ->where('id', '!=', auth()->id())
                ->when($this->search, function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                      ->orWhere('email', 'like', '%'.$this->search.'%');
                })
                ->latest()
                ->paginate(10),
            'isSuperAdmin' => auth()->user()->isSuperAdmin(),
        ];
    }

    public function toggleStatus(int $userId): void
    {
        $user = User::withoutGlobalScope('branch')->findOrFail($userId);

        if (! auth()->user()->isSuperAdmin() && $user->branch_id !== auth()->user()->branch_id) {
            abort(403);
        }

        $user->update(['is_active' => ! $user->is_active]);
        $this->dispatch('notify', message: 'Status pengguna berhasil diperbarui.', type: 'success');
    }

    public function delete(int $userId): void
    {
        $user = User::withoutGlobalScope('branch')->findOrFail($userId);

        if (! auth()->user()->isSuperAdmin() && $user->branch_id !== auth()->user()->branch_id) {
            abort(403);
        }

        if ($user->isSuperAdmin()) {
            $this->dispatch('notify', message: 'Tidak dapat menghapus Super Admin.', type: 'error');

            return;
        }

        $user->delete();
        $this->dispatch('notify', message: 'Pengguna berhasil dihapus.', type: 'success');
    }

    public function impersonate(int $userId): void
    {
        if (! auth()->user()->isSuperAdmin()) {
            abort(403, 'Hanya Super Admin yang dapat melakukan impersonasi.');
        }

        $user = User::withoutGlobalScope('branch')->findOrFail($userId);

        $this->redirect(route('admin.impersonate', $user->id), navigate: false);
    }
}; ?>

<div class="p-3 md:p-6 lg:p-10 space-y-6 md:space-y-8">
    <!-- Header -->
    <x-admin.page-header 
        title="Manajemen Pengguna"
        description="Atur akses administrator dan operator sistem untuk menjaga integritas operasional."
    >
        <x-slot:action>
            <flux:button variant="primary" icon="plus" :href="route('admin.users.create')" wire:navigate>
                {{ __('Tambah Pengguna') }}
            </flux:button>
        </x-slot:action>
    </x-admin.page-header>

    <!-- Toolbar -->
    <div class="flex flex-col sm:flex-row gap-4 items-center justify-between">
        <div class="w-full sm:w-72">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama atau email..." icon="magnifying-glass" />
        </div>
    </div>

    <!-- Table -->
    <div class="premium-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-zinc-50 dark:bg-zinc-900/50 text-zinc-500 font-medium border-b border-zinc-200 dark:border-zinc-800 uppercase text-[10px] tracking-widest">
                    <tr>
                        <th class="px-6 py-4">{{ __('Pengguna') }}</th>
                        <th class="px-6 py-4">{{ __('Role / Akses') }}</th>
                        <th class="px-6 py-4">{{ __('Status') }}</th>
                        <th class="px-6 py-4">{{ __('Login Terakhir') }}</th>
                        <th class="px-6 py-4 text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900 font-medium">
                    @forelse($users as $user)
                        <tr class="group hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors" wire:key="{{ $user->id }}">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <flux:avatar initials="{{ $user->initials() }}" class="size-10 rounded-xl" />
                                    <div class="flex flex-col">
                                        <span class="font-bold text-zinc-900 dark:text-white leading-none">{{ $user->name }}</span>
                                        <span class="text-xs text-zinc-500 mt-1">{{ $user->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <span class="px-2 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 text-[10px] font-black uppercase tracking-tighter">
                                    {{ $user->role?->label() ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-5">
                                <flux:switch wire:click="toggleStatus('{{ $user->id }}')" :checked="$user->is_active" />
                            </td>
                            <td class="px-6 py-5">
                                <span class="text-xs text-zinc-500">{{ $user->last_login_at?->diffForHumans() ?? 'Belum pernah' }}</span>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($isSuperAdmin && !$user->isSuperAdmin())
                                        <flux:button icon="user-circle" size="xs" variant="ghost" 
                                            wire:click="impersonate('{{ $user->id }}')" 
                                            onclick="if(!confirm('Masuk sebagai pengguna ini?')) { event.stopImmediatePropagation(); return false; }" 
                                            title="Login sebagai Pengguna" />
                                    @endif
                                    <flux:button icon="pencil-square" size="xs" variant="ghost" :href="route('admin.users.edit', $user)" wire:navigate />
                                    <flux:button icon="trash" size="xs" variant="ghost" class="text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20" wire:click="delete('{{ $user->id }}')" onclick="if(!confirm('Hapus pengguna ini selamanya?')) { event.stopImmediatePropagation(); return false; }" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <flux:icon name="magnifying-glass" class="size-8 text-zinc-300" />
                                    <p class="text-zinc-500 text-sm font-medium">{{ __('Tidak ada pengguna ditemukan.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 p-4">
            {{ $users->links() }}
        </div>
    </div>
</div>
