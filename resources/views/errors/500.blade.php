<x-layouts.guest>
    <div class="py-24 bg-white dark:bg-zinc-950 flex flex-col items-center justify-center min-h-[calc(100vh-80px)] text-center px-6">
        <div class="relative mb-12">
            <h1 class="text-[120px] md:text-[200px] font-black text-primary/10 italic leading-none select-none">500</h1>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="size-24 rounded-3xl bg-red-500/10 flex items-center justify-center text-red-500 shadow-2xl shadow-red-500/20">
                    <flux:icon.bolt class="size-12" />
                </div>
            </div>
        </div>
        
        <div class="max-w-xl space-y-6">
            <h2 class="text-3xl md:text-5xl font-black text-zinc-900 dark:text-white tracking-tight leading-tight">Terjadi Gangguan Teknis</h2>
            <p class="text-lg text-zinc-500 font-medium leading-relaxed">
                Mohon maaf, server kami sedang mengalami gangguan. Tim teknis kami sedang berupaya memperbaikinya secepat mungkin. Silakan coba kembali beberapa saat lagi.
            </p>
        </div>

        <div class="mt-16">
            <flux:button wire:click="window.location.reload()" variant="primary"  class="px-12 h-16 rounded-2xl font-black uppercase tracking-widest shadow-xl shadow-primary/30">
                Muat Ulang Halaman
            </flux:button>
        </div>
    </div>
</x-layouts.guest>
