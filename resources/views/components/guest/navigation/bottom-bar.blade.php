<div x-data x-cloak x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-4 scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 scale-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
    x-transition:leave-end="opacity-0 translate-y-4 scale-95"
    class="absolute -bottom-1 left-1/2 -translate-x-1/2 pointer-events-auto">
    <div x-show="$store.ui.bottomBarOpen" x-transition
        class="bg-white dark:bg-neutral-800 rounded-full shadow-xl border border-slate-100 dark:border-neutral-700 p-2 flex items-center gap-x-2">
        <x-guest.navigation.bottom-bar-item route="home" icon="home">
            Beranda
        </x-guest.navigation.bottom-bar-item>

        <x-guest.navigation.bottom-bar-item route="donate" icon="hand-heart">
            Donasi
        </x-guest.navigation.bottom-bar-item>

        <x-guest.navigation.bottom-bar-item route="calc" icon="calculator">
            Kalkulator
        </x-guest.navigation.bottom-bar-item>
    </div>
</div>

<button @click="$store.ui.toggleBottomBarVisibility()"
    class="absolute bottom-0 right-4 pointer-events-auto flex items-center justify-center w-14 h-14 rounded-full bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 
    shadow-lg hover:scale-105 active:scale-95 transition-all duration-300 z-50 backdrop-blur-sm">

    <div x-show="$store.ui.bottomBarOpen" class="absolute">
        <i data-lucide="X" class="w-6 h-6"></i>
    </div>

    <div x-show="!$store.ui.bottomBarOpen" class="absolute">
        <i data-lucide="plus" class="w-6 h-6"></i>
    </div>

</button>
