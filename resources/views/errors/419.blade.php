<x-layouts.guest>
    <div class="py-24 bg-white dark:bg-zinc-950 flex flex-col items-center justify-center min-h-[calc(100vh-80px)] text-center px-6">
        <div class="relative mb-12">
            <h1 class="text-[120px] md:text-[200px] font-black text-primary/10 italic leading-none select-none">419</h1>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="size-24 rounded-3xl bg-primary/10 flex items-center justify-center text-primary shadow-2xl shadow-primary/20">
                    <flux:icon.clock class="size-12" />
                </div>
            </div>
        </div>
        
        <div class="max-w-xl space-y-6">
            <h2 class="text-3xl md:text-5xl font-black text-zinc-900 dark:text-white tracking-tight leading-tight">Sesi Kedaluwarsa</h2>
            <p class="text-lg text-zinc-500 font-medium">
                Halaman ini telah terbuka terlalu lama sehingga sesi keamanan Anda berakhir. Jangan khawatir, silakan muat ulang halaman untuk melanjutkan aktivitas Anda.
            </p>
        </div>

        <div class="mt-16">
            <flux:button onclick="window.location.reload()" variant="primary" class="px-12 h-16 rounded-2xl font-black uppercase tracking-widest shadow-xl shadow-primary/30">
                Muat Ulang Halaman
            </flux:button>
        </div>
    </div>
</x-layouts.guest>
