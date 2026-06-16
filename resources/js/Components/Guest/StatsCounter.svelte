<script>
    export let stats = {};

    const formatNumber = (value) => {
        if (!value && value !== 0) return "0";
        value = Number(value);

        if (value >= 1000000) {
            return `${(value / 1000000).toFixed(1)}M+`;
        }

        if (value >= 1000) {
            return `${(value / 1000).toFixed(1)}K+`;
        }

        return value.toLocaleString("id-ID");
    };
</script>

<section class="py-24 bg-white dark:bg-zinc-950">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div
            class="relative overflow-hidden rounded-xl border border-primary/10 bg-primary/5 p-8 md:p-16 shadow-xl dark:border-primary/20 dark:bg-primary/10"
        >
            <div
                class="absolute -top-24 -right-24 h-64 w-64 rounded-full bg-primary/10 blur-3xl"
            ></div>
            <div class="grid gap-12 md:grid-cols-3">
                <div class="flex flex-col items-center text-center gap-4">
                    <span class="text-4xl font-black text-primary md:text-5xl"
                        >{formatNumber(stats.totalMuzakki)}</span
                    >
                    <div>
                        <h4
                            class="text-sm font-black uppercase text-zinc-900 dark:text-white"
                        >
                            Muzakki Aktif
                        </h4>
                        <p class="text-xs text-zinc-500 dark:text-zinc-300">
                            Donatur yang mempercayakan amanahnya
                        </p>
                    </div>
                </div>
                <div
                    class="flex flex-col items-center text-center gap-4 border-t border-primary/20 pt-8 md:border-t-0 md:border-x md:pt-0"
                >
                    <span class="text-4xl font-black text-primary md:text-5xl"
                        >{formatNumber(stats.totalDana)}</span
                    >
                    <div>
                        <h4
                            class="text-sm font-black uppercase text-zinc-900 dark:text-white"
                        >
                            Dana Tersalurkan
                        </h4>
                        <p class="text-xs text-zinc-500 dark:text-zinc-300">
                            Membantu sesama di wilayah Tamantirto
                        </p>
                    </div>
                </div>
                <div class="flex flex-col items-center text-center gap-4">
                    <span class="text-4xl font-black text-primary md:text-5xl"
                        >{formatNumber(stats.activePrograms)}</span
                    >
                    <div>
                        <h4
                            class="text-sm font-black uppercase text-zinc-900 dark:text-white"
                        >
                            Program Aktif
                        </h4>
                        <p class="text-xs text-zinc-500 dark:text-zinc-300">
                            Inisiatif pemberdayaan yang sedang berjalan
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
