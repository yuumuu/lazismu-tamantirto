<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';

    public function with(): array
    {
        return [
            'users' => User::query()
                ->with('roles')
                ->where('id', '!=', auth()->id())
                ->when($this->search, function($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
                })
                ->latest()
                ->paginate(10),
        ];
    }

    public function toggleStatus(User $user): void
    {
        $user->update(['is_active' => !$user->is_active]);
        $this->dispatch('notify', message: 'Status pengguna berhasil diperbarui.', type: 'success');
    }

    public function delete(User $user): void
    {
        if ($user->hasRole('super_admin')) {
            $this->dispatch('notify', message: 'Tidak dapat menghapus Super Admin.', type: 'error');
            return;
        }
        
        $user->delete();
        $this->dispatch('notify', message: 'Pengguna berhasil dihapus.', type: 'success');
    }
}; ?>

<div class="p-3 md:p-6 lg:p-10 space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
        <div class="space-y-2">
            <div class="flex items-center gap-2">
                <div class="h-6 w-1 bg-primary rounded-full"></div>
                <h1 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white">{{ __('Manajemen Pengguna') }}</h1>
            </div>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm max-w-lg leading-relaxed border-l pl-4 border-zinc-200 dark:border-zinc-800">
                {{ __('Atur akses administrator dan operator sistem untuk menjaga integritas operasional.') }}
            </p>
        </div>
        <flux:button variant="primary" icon="plus" :href="route('admin.users.create')" wire:navigate>
            {{ __('Tambah Pengguna') }}
        </flux:button>
    </div>

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
                                <div class="flex flex-wrap gap-1">
                                    @foreach($user->roles as $role)
                                        <span class="px-2 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 text-[10px] font-black uppercase tracking-tighter">
                                            {{ str_replace('_', ' ', $role->name) }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <flux:switch wire:click="toggleStatus('{{ $user->id }}')" :checked="$user->is_active" />
                            </td>
                            <td class="px-6 py-5">
                                <span class="text-xs text-zinc-500">{{ $user->last_login_at?->diffForHumans() ?? 'Belum pernah' }}</span>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button icon="pencil-square" size="xs" variant="ghost" :href="route('admin.users.edit', $user)" wire:navigate />
                                    <flux:button icon="trash" size="xs" variant="ghost" class="text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20" wire:click="delete('{{ $user->id }}')" wire:confirm="Hapus pengguna ini selamanya?" />
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
