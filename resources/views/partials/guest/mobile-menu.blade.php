<!-- Mobile fullscreen drawer -->
<div x-cloak
     x-show="open"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[100] sm:hidden"
     style="display: none;">

    <!-- Backdrop -->
    <div class="fixed inset-0 bg-zinc-950/60 backdrop-blur-sm z-[100]" @click="open = false" aria-hidden="true"></div>

    <!-- Drawer Content -->
    <div x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="-translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="-translate-x-full"
         class="fixed inset-y-0 left-0 w-[80%] max-w-sm bg-white dark:bg-zinc-900 shadow-2xl flex flex-col z-[101]">
        <div class="p-6 flex items-center justify-between border-b border-zinc-100 dark:border-white/5">
            <div class="flex items-center gap-3">
                <div class="flex aspect-square size-9 items-center justify-center rounded-xl bg-transparent text-white shadow-none">
                    <x-app-logo-icon class="size-6 fill-current" />
                </div>
                <div class="flex flex-col leading-tight">
                    <span class="text-zinc-900 dark:text-white font-black text-lg tracking-tighter uppercase">{{ setting('site_name', 'Lazismu') }}</span>
                    <span class="text-primary font-bold text-[9px] tracking-[0.2em] uppercase">{{ setting('site_tagline', 'Tamantirto') }}</span>
                </div>
            </div>
            <button type="button" @click="open = false"
                class="p-2 rounded-xl text-zinc-500 hover:bg-zinc-100 dark:hover:bg-white/5 transition-colors">
                <flux:icon.x-mark class="size-6" />
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-6 space-y-2">
            @foreach([
                ['route' => 'guest.home', 'label' => 'Beranda', 'icon' => 'home'],
                ['route' => 'guest.campaigns.index', 'label' => 'Donasi', 'icon' => 'heart'],
                ['route' => 'guest.posts.index', 'label' => 'Berita', 'icon' => 'newspaper'],
                ['route' => 'guest.about', 'label' => 'Tentang Kami', 'icon' => 'book-open'],
                ['route' => 'guest.contact', 'label' => 'Kontak', 'icon' => 'phone'],
                ['route' => 'guest.calculator', 'label' => 'Kalkulator Zakat', 'icon' => 'calculator'],
            ] as $nav)
                <a
                    href="{{ guest_route($nav['route']) }}"
                    @click="open = false"
                    class="flex items-center gap-4 px-6 py-4 rounded-2xl text-lg font-black tracking-tight transition-all {{ request()->routeIs($nav['route']) || (request()->route('masjid_slug') && request()->routeIs('guest.tenant.'.str_replace('guest.', '', $nav['route']))) ? 'bg-primary/10 text-primary shadow-sm' : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-white/5' }}"
                >
                    <flux:icon name="{{ $nav['icon'] }}" class="size-6 {{ request()->routeIs($nav['route']) || (request()->route('masjid_slug') && request()->routeIs('guest.tenant.'.str_replace('guest.', '', $nav['route']))) ? 'text-primary' : 'text-zinc-400' }}" />
                    {{ $nav['label'] }}
                </a>
            @endforeach
        </div>

        <div class="p-6 border-t border-zinc-100 dark:border-white/5 space-y-4">
            <div class="flex items-center justify-between p-4 bg-zinc-50 dark:bg-white/5 rounded-2xl">
                <span class="text-zinc-600 dark:text-zinc-400 font-medium">Mode Gelap</span>
                <x-guest.partials.toggle-theme />
            </div>
            <flux:button href="{{ route('guest.donate.form') }}" variant="primary"  class="w-full font-bold py-4 rounded-2xl shadow-lg shadow-primary/20">
                Donasi Sekarang
            </flux:button>
        </div>
    </div>
</div>
