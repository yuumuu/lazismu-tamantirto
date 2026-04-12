{{-- Stats passed from controller (cached) --}}
<section class="py-24 bg-white dark:bg-zinc-950">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="bg-primary/5 dark:bg-primary/10 rounded-[48px] p-8 md:p-16 border border-primary/10 dark:border-primary/20 relative overflow-hidden group">
            <!-- Decorative circle -->
            <div class="absolute -top-24 -right-24 size-64 bg-primary/10 rounded-full blur-3xl group-hover:scale-110 transition-transform duration-1000"></div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-12 relative z-10">
                <div class="flex flex-col items-center text-center space-y-4">
                    <span class="text-4xl md:text-5xl font-black text-primary tracking-tighter">{{ $stats['totalMuzakki'] > 100 ? number_format($stats['totalMuzakki'] / 1000, 1) . 'K+' : $stats['totalMuzakki'] }}</span>
                    <div class="space-y-1">
                        <h4 class="text-zinc-900 dark:text-white font-black uppercase tracking-widest text-sm">Muzakki Aktif</h4>
                        <p class="text-xs text-zinc-500 font-medium">Donatur yang mempercayakan amanahnya</p>
                    </div>
                </div>

                <div class="flex flex-col items-center text-center space-y-4 border-y md:border-y-0 md:border-x border-primary/20 py-12 md:py-0">
                    <span class="text-4xl md:text-5xl font-black text-primary tracking-tighter">{{ format_rupiah_short($stats['totalDana']) }}</span>
                    <div class="space-y-1">
                        <h4 class="text-zinc-900 dark:text-white font-black uppercase tracking-widest text-sm">Dana Tersalurkan</h4>
                        <p class="text-xs text-zinc-500 font-medium">Membantu sesama di wilayah Tamantirto</p>
                    </div>
                </div>

                <div class="flex flex-col items-center text-center space-y-4">
                    <span class="text-4xl md:text-5xl font-black text-primary tracking-tighter">{{ $stats['activePrograms'] }}</span>
                    <div class="space-y-1">
                        <h4 class="text-zinc-900 dark:text-white font-black uppercase tracking-widest text-sm">Program Aktif</h4>
                        <p class="text-xs text-zinc-500 font-medium">Inisiatif pemberdayaan yang sedang berjalan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
