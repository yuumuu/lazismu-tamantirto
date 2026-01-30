<div class="sm:hidden fixed bottom-0 left-0 right-0 z-[100] px-4 pb-4 pointer-events-none">
    <div class="mx-auto max-w-md w-full bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl border border-zinc-200 dark:border-white/10 rounded-2xl shadow-2xl pointer-events-auto flex items-center justify-around p-2">
        <a href="{{ route('guest.home') }}" class="flex flex-col items-center gap-1 p-2 rounded-xl transition-colors {{ request()->routeIs('guest.home') ? 'text-primary' : 'text-zinc-500 dark:text-zinc-400' }}">
            <flux:icon.home class="size-6" variant="{{ request()->routeIs('guest.home') ? 'solid' : 'outline' }}" />
            <span class="text-[10px] font-bold uppercase tracking-wider">Beranda</span>
        </a>

        <a href="{{ route('guest.campaigns.index') }}" class="flex flex-col items-center gap-1 p-2 rounded-xl transition-colors {{ request()->routeIs('guest.campaigns.*') ? 'text-primary' : 'text-zinc-500 dark:text-zinc-400' }}">
            <flux:icon.heart class="size-6" variant="{{ request()->routeIs('guest.campaigns.*') ? 'solid' : 'outline' }}" />
            <span class="text-[10px] font-bold uppercase tracking-wider">Donasi</span>
        </a>

        <!-- Floating Action Button -->
        <div class="relative -top-6">
            <a href="{{ route('guest.donate.form') }}" class="flex items-center justify-center size-14 rounded-2xl bg-primary text-white shadow-xl shadow-primary/40 hover:scale-105 active:scale-95 transition-all">
                <flux:icon.plus class="size-8" />
            </a>
        </div>

        <a href="{{ route('guest.calculator') }}" class="flex flex-col items-center gap-1 p-2 rounded-xl transition-colors {{ request()->routeIs('guest.calculator') ? 'text-primary' : 'text-zinc-500 dark:text-zinc-400' }}">
            <flux:icon.calculator class="size-6" variant="{{ request()->routeIs('guest.calculator') ? 'solid' : 'outline' }}" />
            <span class="text-[10px] font-bold uppercase tracking-wider">Zakat</span>
        </a>

        <a href="{{ route('guest.about') }}" class="flex flex-col items-center gap-1 p-2 rounded-xl transition-colors {{ request()->routeIs('guest.about') ? 'text-primary' : 'text-zinc-500 dark:text-zinc-400' }}">
            <flux:icon.book-open class="size-6" variant="{{ request()->routeIs('guest.about') ? 'solid' : 'outline' }}" />
            <span class="text-[10px] font-bold uppercase tracking-wider">Tentang</span>
        </a>
    </div>
</div>
