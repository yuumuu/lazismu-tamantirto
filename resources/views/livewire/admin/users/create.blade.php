<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Masjid;
use Spatie\Permission\Models\Role;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

new class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public array $roles = [];
    public bool $is_active = true;
    public ?int $masjid_id = null;

    public function mount()
    {
        $this->masjid_id = session('active_masjid_id');
    }

    public function with(): array
    {
        $isSuperAdmin = auth()->user()->isSuperAdmin();

        return [
            'availableRoles' => Role::when(! $isSuperAdmin, fn ($q) => $q->where('name', '!=', 'super_admin'))->get(),
            'masjids' => $isSuperAdmin ? Masjid::all() : [],
            'isSuperAdmin' => $isSuperAdmin,
        ];
    }

    public function save(): void
    {
        if (! auth()->user()->isSuperAdmin()) {
            $this->masjid_id = auth()->user()->masjid_id;
        }

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'confirmed', Password::defaults()],
            'roles' => ['required', 'array', 'min:1'],
            'is_active' => ['boolean'],
            'masjid_id' => ['required', 'exists:masjids,id'],
        ]);

        // Security check: ensure non-super-admin can't assign super_admin role
        if (! auth()->user()->isSuperAdmin() && in_array('super_admin', $this->roles)) {
            abort(403, 'Anda tidak diizinkan membuat Super Admin.');
        }

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'is_active' => $this->is_active,
            'masjid_id' => $this->masjid_id,
            'email_verified_at' => now(), // Auto-verify for admin-created users
        ]);

        $user->syncRoles($this->roles);

        $this->dispatch('notify', message: 'Pengguna baru berhasil ditambahkan.', type: 'success');
        $this->redirect(route('admin.users.index'), navigate: true);
    }
}; ?>

<div>
    <x-admin.page-header
        title="Tambah Pengguna Baru"
        description="Daftarkan administrator atau operator baru ke dalam sistem."
        backRoute="admin.users.index"
    />

    <div class="p-3 md:p-6 lg:p-10 max-w-4xl mx-auto space-y-8">

    <form wire:submit="save" class="space-y-6">
        <div class="premium-card p-8 space-y-8">
            <!-- Basic Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input wire:model="name" label="Nama Lengkap" placeholder="Masukkan nama lengkap..." required />
                <flux:input wire:model="email" type="email" label="Alamat Email" placeholder="email@contoh.com" required />
            </div>

            <!-- Password -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input wire:model="password" type="password" label="Kata Sandi" placeholder="••••••••" required />
                <flux:input wire:model="password_confirmation" type="password" label="Konfirmasi Kata Sandi" placeholder="••••••••" required />
            </div>

            <!-- Roles & Status -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-6 border-t border-zinc-100 dark:border-zinc-800">
                <div class="space-y-4">
                    <flux:label>Hak Akses (Roles)</flux:label>
                    <div class="grid grid-cols-1 gap-2">
                        @foreach($availableRoles as $role)
                            <label class="flex items-center gap-3 p-3 rounded-xl border border-zinc-100 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors cursor-pointer group">
                                <flux:checkbox wire:model="roles" value="{{ $role->name }}" />
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-zinc-900 dark:text-white uppercase tracking-tight group-hover:text-primary transition-colors">{{ str_replace('_', ' ', $role->name) }}</span>
                                    <span class="text-[10px] text-zinc-500">Akses level: {{ $role->guard_name }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('roles') <p class="text-xs text-red-500 font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-4">
                    <flux:label>Pengaturan Akun</flux:label>
                    <div class="p-6 rounded-2xl bg-zinc-50 dark:bg-zinc-900/50 border border-zinc-100 dark:border-zinc-800 space-y-6">
                        @if($isSuperAdmin)
                            <!-- Masjid Selection -->
                            <flux:select wire:model="masjid_id" label="Cabang / Masjid" placeholder="Pilih Cabang...">
                                @foreach($masjids as $masjid)
                                    <flux:select.option value="{{ $masjid->id }}">{{ $masjid->name }}</flux:select.option>
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
                Simpan Pengguna
            </flux:button>
        </div>
    </form>
</div>
