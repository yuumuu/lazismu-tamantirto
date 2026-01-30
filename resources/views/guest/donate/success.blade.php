<x-layouts.guest>
    <div class="py-24 bg-white dark:bg-zinc-950 flex flex-col items-center justify-center min-h-[calc(100vh-80px)] text-center px-6">
        <div class="size-32 rounded-[40px] bg-primary/10 flex items-center justify-center text-primary mb-12 shadow-2xl shadow-primary/20 animate-bounce">
            <flux:icon.check-circle class="size-16" />
        </div>
        
        <div class="max-w-xl space-y-6">
            <h1 class="text-4xl md:text-5xl font-black text-zinc-900 dark:text-white tracking-tight">Terima Kasih, Orang Baik!</h1>
            <p class="text-lg text-zinc-500 font-medium leading-relaxed">
                Konfirmasi pembayaran Anda telah kami terima. Tim kami akan segera melakukan verifikasi amanah Anda. Setiap butir kebaikan Anda sangat berarti bagi mereka.
            </p>
        </div>

        <div class="mt-16 flex flex-col md:flex-row gap-4 w-full max-w-md">
            <flux:button href="{{ route('guest.home') }}" variant="primary"  class="flex-1 h-16 rounded-2xl font-black uppercase tracking-widest shadow-xl shadow-primary/30">
                Kembali ke Beranda
            </flux:button>
            <flux:button href="{{ route('guest.campaigns.index') }}" variant="ghost"  class="flex-1 h-16 rounded-2xl font-bold border-zinc-200 dark:border-white/10">
                Lihat Program Lain
            </flux:button>
        </div>

        <div class="mt-24 pt-12 border-t border-zinc-100 dark:border-white/5 w-full max-w-2xl">
            <p class="text-[10px] text-zinc-400 font-black uppercase tracking-[0.2em] mb-8">Bagikan Semangat Kebaikan</p>
            <div class="grid grid-cols-3 gap-6">
                <a href="#" class="flex flex-col items-center gap-3 group">
                    <div class="size-14 rounded-2xl bg-zinc-50 dark:bg-white/5 flex items-center justify-center text-zinc-400 group-hover:bg-[#1877F2] group-hover:text-white transition-all shadow-sm">
                        <flux:icon.facebook class="size-6" />
                    </div>
                    <span class="text-[10px] font-bold text-zinc-500">Facebook</span>
                </a>
                <a href="#" class="flex flex-col items-center gap-3 group">
                    <div class="size-14 rounded-2xl bg-zinc-50 dark:bg-white/5 flex items-center justify-center text-zinc-400 group-hover:bg-[#25D366] group-hover:text-white transition-all shadow-sm">
                        <flux:icon.phone class="size-6" />
                    </div>
                    <span class="text-[10px] font-bold text-zinc-500">WhatsApp</span>
                </a>
                <a href="#" class="flex flex-col items-center gap-3 group">
                    <div class="size-14 rounded-2xl bg-zinc-50 dark:bg-white/5 flex items-center justify-center text-zinc-400 group-hover:bg-[#1DA1F2] group-hover:text-white transition-all shadow-sm">
                        <flux:icon.twitter class="size-6" />
                    </div>
                    <span class="text-[10px] font-bold text-zinc-500">Twitter</span>
                </a>
            </div>
        </div>
    </div>
</x-layouts.guest>
