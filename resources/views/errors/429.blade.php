<x-layouts.guest>
    <div class="py-24 bg-white dark:bg-zinc-950 flex flex-col items-center justify-center min-h-[calc(100vh-80px)] text-center px-6">
        <div class="relative mb-12">
            <h1 class="text-[120px] md:text-[200px] font-black text-zinc-100 dark:text-white/5 leading-none select-none italic">429</h1>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="size-24 rounded-3xl bg-amber-500/10 flex items-center justify-center text-amber-500 shadow-2xl shadow-amber-500/20">
                    <flux:icon.hand-raised class="size-12" />
                </div>
            </div>
        </div>
        
        <div class="max-w-xl space-y-6">
            <h2 class="text-3xl md:text-5xl font-black text-zinc-900 dark:text-white tracking-tight leading-tight">Terlalu Banyak Permintaan</h2>
            <p class="text-lg text-zinc-500 font-medium leading-relaxed">
                Wah, sepertinya Anda sedang terburu-buru! Sistem kami mendeteksi terlalu banyak permintaan dari perangkat Anda. Silakan istirahat sejenak dan coba lagi dalam satu menit.
            </p>
        </div>

        <div class="mt-16">
            <flux:button href="{{ route('guest.home') }}" variant="primary" class="px-12 h-16 rounded-2xl font-black uppercase tracking-widest shadow-xl shadow-primary/30">
                Kembali ke Beranda
            </flux:button>
        </div>
    </div>
</x-layouts.guest>
