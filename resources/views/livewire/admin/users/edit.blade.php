<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\User;
use App\Models\Branch;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

new class extends Component {
    public User $user;
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $role = '';
    public bool $is_active = true;
    public ?int $branch_id = null;

    public function mount(User $user): void
    {
        if (! auth()->user()->isSuperAdmin() && $user->branch_id !== auth()->user()->branch_id) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit pengguna ini.');
        }

        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role?->value ?? '';
        $this->is_active = (bool) $user->is_active;
        $this->branch_id = $user->branch_id;
    }

    public function with(): array
    {
        $isSuperAdmin = auth()->user()->isSuperAdmin();

        return [
            'availableRoles' => $isSuperAdmin
                ? UserRole::options()
                : collect(UserRole::options())->except('super_admin')->toArray(),
            'branches' => $isSuperAdmin ? Branch::all() : [],
            'isSuperAdmin' => $isSuperAdmin,
        ];
    }

    public function save(): void
    {
        if (! auth()->user()->isSuperAdmin()) {
            $this->branch_id = auth()->user()->branch_id;

            if ($this->role === 'super_admin') {
                abort(403, 'Anda tidak diizinkan memberikan akses Super Admin.');
            }
        }

        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($this->user->id)],
            'password' => ['nullable', 'string', 'confirmed', Password::defaults()],
            'role' => ['required', 'string', 'in:'.implode(',', UserRole::values())],
            'is_active' => ['boolean'],
            'branch_id' => ['required', 'exists:branches,id'],
        ]);

        $updateData = [
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role,
            'is_active' => $this->is_active,
            'branch_id' => $this->branch_id,
        ];

        if ($this->password) {
            $updateData['password'] = Hash::make($this->password);
        }

        $this->user->update($updateData);

        $this->dispatch('notify', message: 'Data pengguna berhasil diperbarui.', type: 'success');
        $this->redirect(route('admin.users.index'), navigate: true);
    }
}; ?>

<div class="p-3 md:p-6 lg:p-10 max-w-4xl mx-auto space-y-8">
    <header class="space-y-2">
        <div class="flex items-center gap-4">
            <flux:button icon="arrow-left" variant="ghost" size="sm" :href="route('admin.users.index')" wire:navigate />
            <div class="h-6 w-1 bg-primary rounded-full"></div>
            <h1 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white">{{ __('Edit Pengguna') }}</h1>
        </div>
        <p class="text-zinc-500 dark:text-zinc-400 text-sm pl-12">
            {{ __('Perbarui informasi profil dan hak akses pengguna.') }}
        </p>
    </header>

    <form wire:submit="save" class="space-y-6">
        <div class="premium-card p-8 space-y-8">
            <!-- Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input wire:model="name" label="Nama Lengkap" placeholder="Masukkan nama lengkap..." required />
                <flux:input wire:model="email" type="email" label="Alamat Email" placeholder="email@contoh.com" required />
            </div>

            <!-- Password -->
            <div class="space-y-4 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                <div class="flex items-center gap-2">
                    <flux:icon name="key" class="size-4 text-zinc-400" />
                    <flux:label>Ganti Kata Sandi (Opsional)</flux:label>
                </div>
                <p class="text-[10px] text-zinc-500 italic">Kosongkan jika tidak ingin mengubah kata sandi.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input wire:model="password" type="password" label="Kata Sandi Baru" placeholder="••••••••" />
                    <flux:input wire:model="password_confirmation" type="password" label="Konfirmasi Kata Sandi Baru" placeholder="••••••••" />
                </div>
            </div>

            <!-- Role & Status -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-6 border-t border-zinc-100 dark:border-zinc-800">
                <div class="space-y-4">
                    <flux:select wire:model="role" label="Hak Akses (Role)" placeholder="Pilih role...">
                        @foreach($availableRoles as $value => $label)
                            <flux:select.option value="{{ $value }}">{{ $label }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    @error('role') <p class="text-xs text-red-500 font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-4">
                    <flux:label>Pengaturan Akun</flux:label>
                    <div class="p-6 rounded-2xl bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-100 dark:border-zinc-800 space-y-6">
                        @if($isSuperAdmin)
                            <flux:select wire:model="branch_id" label="Cabang / Branch" placeholder="Pilih Cabang...">
                                @foreach($branches as $branch)
                                    <flux:select.option value="{{ $branch->id }}">{{ $branch->name }}</flux:select.option>
                                @endforeach
                            </flux:select>
                        @endif

                        <div class="flex items-center justify-between pt-4 border-t border-zinc-200 dark:border-zinc-800">
                            <div class="flex flex-col">
                                <span class="text-sm font-bold text-zinc-900 dark:text-white">Status Aktif</span>
                                <span class="text-[10px] text-zinc-500">Izinkan pengguna ini masuk ke sistem.</span>
                            </div>
                            <flux:switch wire:model="is_active" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end gap-3">
            <flux:button :href="route('admin.users.index')" variant="ghost" wire:navigate>Batal</flux:button>
            <flux:button type="submit" variant="primary" class="px-8 font-black uppercase tracking-widest shadow-lg shadow-primary/20">
                Update Pengguna
            </flux:button>
        </div>
    </form>
</div>
