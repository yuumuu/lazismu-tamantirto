<nav
    class="sticky top-0 left-0 w-full bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md z-[99] border-b border-zinc-200 dark:border-white/10">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="relative flex h-20 items-center justify-between">
            <div class="flex flex-1 items-center justify-start sm:items-stretch sm:justify-between">
                <div class="flex shrink-0 items-center">
                    <a href="{{ guest_route('guest.home') }}" class="flex items-center gap-3 group">
                        <div
                            class="flex aspect-square size-10 items-center justify-center rounded-xl bg-transparent text-white shadow-none group-hover:scale-105 transition-transform">
                            <x-app-logo-icon class="size-8 fill-current" />
                        </div>
                        <div class="flex flex-col leading-tight">
                            <span
                                class="text-zinc-900 dark:text-white font-black text-xl tracking-tighter uppercase">{{ setting('site_name', 'Lazismu') }}</span>
                            <span
                                class="text-primary font-bold text-[10px] uppercase">{{ setting('site_tagline', 'Tamantirto') }}</span>
                        </div>
                    </a>
                </div>
                <div class="hidden sm:ml-6 sm:block">
                    <div
                        class="flex space-x-1.5 items-center bg-zinc-100/80 dark:bg-zinc-900/80 p-1.5 rounded-2xl border border-zinc-200/50 dark:border-zinc-700/50 shadow-inner">
                        <x-guest.navigation.top-bar-link route="guest.home">
                            Beranda
                        </x-guest.navigation.top-bar-link>
                        <x-guest.navigation.top-bar-link route="guest.campaigns.index">
                            Donasi
                        </x-guest.navigation.top-bar-link>
                        <x-guest.navigation.top-bar-link route="guest.posts.index">
                            Berita
                        </x-guest.navigation.top-bar-link>
                        <x-guest.navigation.top-bar-link route="guest.about">
                            Tentang
                        </x-guest.navigation.top-bar-link>
                        <x-guest.navigation.top-bar-link route="guest.contact">
                            Kontak
                        </x-guest.navigation.top-bar-link>
                    </div>
                </div>

                <div class="hidden sm:flex items-center gap-3 ml-6">
                    <div class="flex items-center">
                        <x-guest.partials.toggle-theme />
                    </div>
                    @auth
                        <flux:button href="{{ route('dashboard') }}" variant="ghost" class="font-bold">
                            Dashboard
                        </flux:button>
                    @endauth
                    <flux:button href="{{ route('guest.donate.form') }}" variant="primary" class="font-bold">
                        Donasi Sekarang
                    </flux:button>
                </div>
            </div>
            <div class="absolute inset-y-0 right-0 flex items-center sm:hidden">
                <button type="button" @click="open = !open"
                    class="relative inline-flex items-center justify-center rounded-xl p-2.5 text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-white/5 transition-all">
                    <span class="sr-only">Open main menu</span>
                    <flux:icon.bars-3 x-show="!open" class="size-6" variant="outline" />
                    <flux:icon.x-mark x-show="open" class="size-6" variant="outline" />
                </button>
            </div>
        </div>
    </div>
</nav>
