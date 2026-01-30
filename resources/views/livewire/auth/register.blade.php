<x-layouts.auth>
    <div class="flex flex-col gap-6">
        <div class="space-y-2 text-center">
            <h1 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white">
                {{ __('Buat Akun Baru') }}
            </h1>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm">
                {{ __('Lengkapi formulir di bawah ini untuk mendaftar.') }}
            </p>
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />

        <form method="POST" action="{{ route('register.store') }}" class="flex flex-col gap-6">
            @csrf
            <!-- Name -->
            <flux:input
                name="name"
                label="Nama Lengkap"
                :value="old('name')"
                type="text"
                icon="user"
                required
                autofocus
                autocomplete="name"
                placeholder="Nama lengkap anda"
            />

            <!-- Email Address -->
            <flux:input
                name="email"
                label="Alamat Email"
                :value="old('email')"
                type="email"
                icon="envelope"
                required
                autocomplete="email"
                placeholder="nama@email.com"
            />

            <!-- Password -->
            <flux:input
                name="password"
                label="Kata Sandi"
                type="password"
                icon="lock-closed"
                required
                autocomplete="new-password"
                placeholder="Buat kata sandi"
                viewable
            />

            <!-- Confirm Password -->
            <flux:input
                name="password_confirmation"
                label="Konfirmasi Kata Sandi"
                type="password"
                icon="lock-closed"
                required
                autocomplete="new-password"
                placeholder="Ulangi kata sandi"
                viewable
            />

            <flux:button type="submit" variant="primary" class="w-full font-bold shadow-md hover:shadow-lg transition-all" icon="user-plus">
                {{ __('Daftar Sekarang') }}
            </flux:button>
        </form>

        <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400 font-medium">
            <span>{{ __('Sudah punya akun?') }}</span>
            <flux:link :href="route('login')" wire:navigate class="text-primary hover:text-primary-dark">{{ __('Masuk disini') }}</flux:link>
        </div>
    </div>
</x-layouts.auth>
