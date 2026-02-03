<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.app.head', ['title' => $title])
</head>

<body class="min-h-screen bg-zinc-50 dark:bg-zinc-950 antialiased font-sans p-3">
    <flux:sidebar sticky stashable
        class="w-72 border border-zinc-300 bg-white dark:border-zinc-800 dark:bg-zinc-900 transition-all duration-300 rounded-xl">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <!-- Brand Identity: Minimalist -->
        <div class="px-6 py-4">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group" wire:navigate>
                <img src="/favicon.png" class="size-8 rounded-lg group-hover:scale-105 transition-transform" />
                <div class="flex flex-col">
                    <span
                        class="text-lg font-bold text-zinc-900 dark:text-white leading-none tracking-tight">LAZISMU</span>
                    <span class="text-[10px] font-medium text-zinc-500 leading-none mt-1">Tamantirto</span>
                </div>
            </a>
        </div>

        <flux:navlist variant="outline" class="space-y-1" data-tour="navigation-menu">
            <flux:navlist.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                wire:navigate>
                {{ __('Dashboard') }}
            </flux:navlist.item>

            <flux:navlist.group :heading="__('Data Master')" expandable class="mt-4">
                <flux:navlist.item icon="user" :href="route('admin.muzakkis.index')"
                    :current="request()->routeIs('admin.muzakkis.*')" wire:navigate>
                    {{ __('Database Muzakki') }}
                </flux:navlist.item>
                <flux:navlist.item icon="user-group" :href="route('admin.mustahiks.index')"
                    :current="request()->routeIs('admin.mustahiks.*')" wire:navigate>
                    {{ __('Database Mustahik') }}
                </flux:navlist.item>
                <flux:navlist.item icon="truck" :href="route('admin.distributors.index')"
                    :current="request()->routeIs('admin.distributors.*')" wire:navigate>
                    {{ __('Jaringan Relawan') }}
                </flux:navlist.item>
            </flux:navlist.group>

            <flux:navlist.group :heading="__('Keuangan')" expandable class="mt-4">
                <flux:navlist.item icon="arrow-down-tray" :href="route('admin.donations.index')"
                    :current="request()->routeIs('admin.donations.*')" wire:navigate>
                    {{ __('Donasi Masuk') }}
                </flux:navlist.item>
                <flux:navlist.item icon="arrow-up-tray" :href="route('admin.withdrawals.index')"
                    :current="request()->routeIs('admin.withdrawals.*')" wire:navigate>
                    {{ __('Penyaluran Dana') }}
                </flux:navlist.item>
                <flux:navlist.item icon="document-chart-bar" :href="route('admin.reports.index')"
                    :current="request()->routeIs('admin.reports.*')" wire:navigate>
                    {{ __('Laporan Keuangan') }}
                </flux:navlist.item>
            </flux:navlist.group>

            <flux:navlist.group :heading="__('Publikasi')" expandable class="mt-4">
                <flux:navlist.item icon="newspaper" :href="route('admin.posts.index')"
                    :current="request()->routeIs('admin.posts.*')" wire:navigate>
                    {{ __('Berita & Artikel') }}
                </flux:navlist.item>
                <flux:navlist.item icon="document-text" :href="route('admin.pages.index')"
                    :current="request()->routeIs('admin.pages.*')" wire:navigate>
                    {{ __('Halaman Statis') }}
                </flux:navlist.item>
                <flux:navlist.item icon="identification" :href="route('admin.structure.index')"
                    :current="request()->routeIs('admin.structure.*')" wire:navigate>
                    {{ __('Struktur Organisasi') }}
                </flux:navlist.item>
                <flux:navlist.item icon="photo" :href="route('admin.banners.index')"
                    :current="request()->routeIs('admin.banners.*')" wire:navigate>
                    {{ __('Hero Banners') }}
                </flux:navlist.item>
            </flux:navlist.group>

            <flux:navlist.group :heading="__('Operasional')" expandable class="mt-4">
                <flux:navlist.item icon="folder-open" :href="route('admin.campaigns.index')"
                    :current="request()->routeIs('admin.campaigns.*')" wire:navigate>
                    {{ __('Program Kampanye') }}
                </flux:navlist.item>
                <flux:navlist.item icon="tag" :href="route('admin.campaign-categories.index')"
                    :current="request()->routeIs('admin.campaign-categories.*')" wire:navigate>
                    {{ __('Kategori Kampanye') }}
                </flux:navlist.item>
                <flux:navlist.item icon="photo" :href="route('admin.media.index')"
                    :current="request()->routeIs('admin.media.*')" wire:navigate>
                    {{ __('Galeri Media') }}
                </flux:navlist.item>
            </flux:navlist.group>

            <flux:navlist.group :heading="__('Akses Sistem')" expandable class="mt-4">
                <flux:navlist.item icon="users" :href="route('admin.users.index')"
                    :current="request()->routeIs('admin.users.*')" wire:navigate>
                    {{ __('Manajemen Pengguna') }}
                </flux:navlist.item>
                <flux:navlist.item icon="shield-check" :href="route('admin.roles.index')"
                    :current="request()->routeIs('admin.roles.*')" wire:navigate>
                    {{ __('Manajemen Akses') }}
                </flux:navlist.item>
                <flux:navlist.item icon="cog-8-tooth" :href="route('admin.settings.index')"
                    :current="request()->routeIs('admin.settings.*')" wire:navigate>
                    {{ __('Pengaturan Sistem') }}
                </flux:navlist.item>
            </flux:navlist.group>
        </flux:navlist>

        <flux:spacer />

        <flux:navlist variant="outline">
            <flux:navlist.item icon="cog-6-tooth" :href="route('profile.edit')"
                :current="request()->routeIs('profile.edit')" wire:navigate>
                {{ __('Keamanan & Akun') }}
            </flux:navlist.item>
        </flux:navlist>

        <!-- Tutorial Menu -->
        <div class="px-2 py-2">
            <livewire:admin.tutorial-menu />
        </div>

        <!-- Bottom User context: Minimalist -->
        <div class="py-3 border-t border-zinc-100 dark:border-zinc-800 mx-2">
            <flux:dropdown position="bottom" align="start">
                <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()"
                    icon:trailing="chevrons-up-down"
                    class="w-full hover:bg-zinc-50 dark:hover:bg-zinc-800 rounded-lg transition-colors text-zinc-900 dark:text-white p-2" />

                <flux:menu class="w-56">
                    <flux:menu.item icon="arrow-right-start-on-rectangle" wire:click="logout">
                        {{ __('Keluar Sesi') }}
                    </flux:menu.item>
                    <form method="POST" action="{{ route('logout') }}" id="logout-form" class="hidden">
                        @csrf
                    </form>
                </flux:menu>
            </flux:dropdown>
        </div>
    </flux:sidebar>

    <!-- Mobile Header -->
    <flux:header
        class="lg:hidden bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md border-b border-zinc-200 dark:border-zinc-800 sticky top-0 z-10 px-2">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
        <flux:spacer />
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <span class="font-black text-xl tracking-tighter text-amber-500">LAZISMU</span>
        </a>
        <flux:spacer />
        
        <!-- Tutorial Menu for Mobile -->
        <livewire:admin.tutorial-menu />
        
        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" />
            <flux:menu>
                <flux:menu.item :href="route('profile.edit')" icon="cog">{{ __('Pengaturan') }}</flux:menu.item>
                <flux:menu.separator />
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle">
                        {{ __('Keluar') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    <flux:main>
        {{ $slot }}
    </flux:main>

    @fluxScripts
</body>

</html>
