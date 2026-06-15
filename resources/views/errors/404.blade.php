<x-layouts.guest>
    <div class="py-24 bg-white dark:bg-zinc-950 flex flex-col items-center justify-center min-h-[calc(100vh-80px)] text-center px-6">
        <div class="relative mb-12">
            <h1 class="text-[120px] md:text-[200px] font-black text-zinc-100 dark:text-white/5 leading-none select-none italic">404</h1>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="size-24 rounded-3xl bg-primary/10 flex items-center justify-center text-primary shadow-2xl shadow-primary/20 animate-pulse">
                    <flux:icon.exclamation-triangle class="size-12" />
                </div>
            </div>
        </div>
        
        <div class="max-w-xl space-y-6">
            <h2 class="text-3xl md:text-5xl font-black text-zinc-900 dark:text-white tracking-tight leading-tight">Ups! Halaman Terputus</h2>
            <p class="text-lg text-zinc-500 font-medium">
                Halaman yang Anda cari tidak dapat kami temukan. Mungkin telah dipindahkan atau tautan sudah kedaluwarsa. Mari kembali menebar kebaikan!
            </p>
        </div>

        <div class="mt-16 flex flex-col md:flex-row gap-4 w-full max-w-md">
            <flux:button href="{{ route('guest.home') }}" variant="primary"  class="flex-1 h-16 rounded-2xl font-black uppercase tracking-widest shadow-xl shadow-primary/30">
                Kembali ke Beranda
            </flux:button>
            <flux:button href="{{ route('guest.contact') }}" variant="ghost"  class="flex-1 h-16 rounded-2xl font-bold border-zinc-200 dark:border-white/10">
                Bantuan Konten
            </flux:button>
        </div>
    </div>
</x-layouts.guest>
