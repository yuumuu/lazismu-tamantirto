<section class="relative -mt-16 z-30 px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-8">
            <!-- Shortcut 1 -->
            <a href="{{ route('guest.donate.form', ['type' => 'zakat']) }}" class="group bg-white dark:bg-zinc-900 p-6 md:p-8 rounded-3xl border border-zinc-200 dark:border-white/5 shadow-xl hover:shadow-2xl hover:-translate-y-2 transition-all">
                <div class="flex flex-col items-center text-center space-y-4">
                    <div class="size-16 rounded-2xl bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors duration-500 shadow-inner">
                        <flux:icon.calculator class="size-8" />
                    </div>
                    <div class="space-y-1">
                        <h4 class="font-black text-zinc-900 dark:text-white uppercase tracking-wider text-sm">Zakat</h4>
                        <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-[0.2em]">Hitung & Bayar</p>
                    </div>
                </div>
            </a>

            <!-- Shortcut 2 -->
            <a href="{{ route('guest.donate.form', ['type' => 'infaq']) }}" class="group bg-white dark:bg-zinc-900 p-6 md:p-8 rounded-3xl border border-zinc-200 dark:border-white/5 shadow-xl hover:shadow-2xl hover:-translate-y-2 transition-all">
                <div class="flex flex-col items-center text-center space-y-4">
                    <div class="size-16 rounded-2xl bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors duration-500 shadow-inner">
                        <flux:icon.heart class="size-8" />
                    </div>
                    <div class="space-y-1">
                        <h4 class="font-black text-zinc-900 dark:text-white uppercase tracking-wider text-sm">Infaq</h4>
                        <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-[0.2em]">Sedekah Rutin</p>
                    </div>
                </div>
            </a>

            <!-- Shortcut 3 -->
            <a href="{{ route('guest.campaigns.index') }}" class="group bg-white dark:bg-zinc-900 p-6 md:p-8 rounded-3xl border border-zinc-200 dark:border-white/5 shadow-xl hover:shadow-2xl hover:-translate-y-2 transition-all">
                <div class="flex flex-col items-center text-center space-y-4">
                    <div class="size-16 rounded-2xl bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors duration-500 shadow-inner">
                        <flux:icon.globe-asia-australia class="size-8" />
                    </div>
                    <div class="space-y-1">
                        <h4 class="font-black text-zinc-900 dark:text-white uppercase tracking-wider text-sm">Kemanusiaan</h4>
                        <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-[0.2em]">Bantu Sesama</p>
                    </div>
                </div>
            </a>

            <!-- Shortcut 4 -->
            <a href="{{ route('guest.reports') }}" class="group bg-white dark:bg-zinc-900 p-6 md:p-8 rounded-3xl border border-zinc-200 dark:border-white/5 shadow-xl hover:shadow-2xl hover:-translate-y-2 transition-all">
                <div class="flex flex-col items-center text-center space-y-4">
                    <div class="size-16 rounded-2xl bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors duration-500 shadow-inner">
                        <flux:icon.document-chart-bar class="size-8" />
                    </div>
                    <div class="space-y-1">
                        <h4 class="font-black text-zinc-900 dark:text-white uppercase tracking-wider text-sm">Laporan</h4>
                        <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-[0.2em]">Transparansi</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</section>
