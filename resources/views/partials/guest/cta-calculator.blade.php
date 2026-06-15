<section class="py-12 md:py-24 px-6 lg:px-8 bg-white dark:bg-zinc-950 overflow-hidden">
    <div class="mx-auto max-w-7xl">
        <div class="relative bg-zinc-900 dark:bg-zinc-900 rounded-[48px] p-8 md:p-16 overflow-hidden shadow-2xl group">
            <!-- Decorative background -->
            <div
                class="absolute -top-24 -right-24 size-96 bg-primary/20 rounded-full blur-[120px] group-hover:bg-primary/30 transition-colors duration-700">
            </div>
            <div
                class="absolute -bottom-24 -left-24 size-96 bg-primary/10 rounded-full blur-[100px] group-hover:bg-primary/20 transition-colors duration-700">
            </div>

            <div class="relative grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-8">
                    <div class="space-y-4">
                        <h4 class="text-primary font-black uppercase text-xs">Zakat Calculator</h4>
                        <h2 class="text-4xl md:text-5xl font-black text-white tracking-tight leading-[1.1]">Sudahkah Anda
                            Berzakat Hari Ini?</h2>
                        <p class="text-zinc-400 text-lg font-medium max-w-md">Hitung kewajiban zakat Anda dengan mudah
                            dan transparan lewat sistem kalkulator otomatis kami.</p>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-4 pt-4">
                        <flux:button href="{{ route('guest.calculator') }}" variant="primary"
                            class="h-14 px-10 rounded-2xl font-black uppercase tracking-widest shadow-xl shadow-primary/20">
                            Coba Kalkulator
                        </flux:button>
                        <flux:button href="{{ route('guest.donate.form', ['type' => 'zakat']) }}" variant="ghost"
                            class="h-14 px-10 rounded-2xl font-black !text-white bg-white/5 hover:!bg-white/10 transition uppercase tracking-widest">
                            Bayar Langsung
                        </flux:button>
                    </div>
                </div>

                <div class="relative lg:block hidden">
                    <div
                        class="bg-white/5 backdrop-blur-sm border border-white/10 p-10 rounded-[64px_0_64px_0] space-y-6">
                        <div class="flex items-center gap-4 border-b border-white/10 pb-6">
                            <div
                                class="size-14 rounded-2xl bg-primary flex items-center justify-center text-white shadow-lg">
                                <flux:icon.calculator class="size-8" />
                            </div>
                            <div class="space-y-1">
                                <h5 class="text-white font-black uppercase tracking-tight">Simulasi Zakat</h5>
                                <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-widest">Sesuai Syariat
                                    & Standar BAZNAS</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            <div class="h-4 w-full bg-white/5 rounded-full overflow-hidden">
                                <div class="h-full w-2/3 bg-primary rounded-full"></div>
                            </div>
                            <div
                                class="flex justify-between items-center text-[10px] font-bold text-zinc-500 uppercase tracking-widest">
                                <span>Pendapatan</span>
                                <span class="text-white font-black">Rp 10.000.000</span>
                            </div>
                            <div
                                class="flex justify-between items-center text-[11px] font-black text-primary uppercase tracking-widest pt-2">
                                <span>Total Zakat</span>
                                <span class="text-2xl tracking-tighter">Rp 250.000</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
