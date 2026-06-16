<script>
    import Calculator from "lucide-svelte/icons/calculator";
    import { Link } from "@inertiajs/svelte";
</script>

<section
    class="py-12 md:py-24 px-6 lg:px-8 bg-white dark:bg-zinc-950 overflow-hidden"
>
    <div class="mx-auto max-w-7xl">
        <div
            class="group relative overflow-hidden rounded-[40px] border border-white/10 bg-zinc-900 p-8 shadow-2xl transition hover:border-primary/20 dark:border-white/10 dark:bg-zinc-950 md:p-16"
        >
            <div
                class="absolute -top-24 -right-24 h-72 w-72 rounded-full bg-primary/20 blur-3xl transition-colors duration-700 group-hover:bg-primary/30"
            ></div>
            <div
                class="absolute -bottom-24 -left-24 h-72 w-72 rounded-full bg-primary/10 blur-3xl transition-colors duration-700 group-hover:bg-primary/30"
            ></div>
            <div class="relative grid gap-12 lg:grid-cols-2 lg:items-center">
                <div class="space-y-6">
                    <p class="text-xs font-black uppercase text-primary">
                        Zakat Calculator
                    </p>
                    <h2
                        class="text-4xl font-black leading-tight tracking-tight text-white sm:text-5xl"
                    >
                        Sudahkah Anda Berzakat Hari Ini?
                    </h2>
                    <p class="max-w-xl text-base text-zinc-300">
                        Hitung kewajiban zakat Anda dengan mudah dan transparan
                        lewat sistem kalkulator otomatis kami.
                    </p>
                    <div class="flex flex-wrap gap-4 pt-4">
                        <Link
                            href="/zakat-kalkulator"
                            class="inline-flex items-center justify-center rounded-2xl bg-primary px-10 py-4 text-sm font-black uppercase text-white shadow-xl shadow-primary/30 transition hover:bg-primary/90"
                        >
                            Coba Kalkulator
                        </Link>
                        <Link
                            href="/donasi?type=zakat"
                            class="inline-flex items-center justify-center rounded-2xl border border-white/20 bg-white/5 px-10 py-4 text-sm font-black uppercase text-white transition hover:bg-white/10"
                        >
                            Bayar Langsung
                        </Link>
                    </div>
                </div>

                <div
                    class="hidden rounded-[64px_0_64px_0] border border-white/10 bg-white/5 p-10 text-white backdrop-blur-xl lg:block"
                >
                    <div
                        class="flex items-center gap-4 border-b border-white/10 pb-6"
                    >
                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-xl bg-primary text-white shadow-lg"
                        >
                            <Calculator class="h-7 w-7" />
                        </div>
                        <div>
                            <p class="text-sm uppercase text-white/70">
                                Simulasi Zakat
                            </p>
                            <p
                                class="text-base font-black uppercase text-white"
                            >
                                Sesuai Syariat & Standar BAZNAS
                            </p>
                        </div>
                    </div>
                    <div class="mt-8 space-y-5">
                        <div class="space-y-3">
                            <div
                                class="h-4 w-full overflow-hidden rounded-full bg-white/10"
                            >
                                <div
                                    class="h-full w-2/3 rounded-full bg-primary"
                                ></div>
                            </div>
                            <div
                                class="flex items-center justify-between text-xs uppercase text-white/60"
                            >
                                <span>Pendapatan</span>
                                <span class="font-black text-white"
                                    >Rp 10.000.000</span
                                >
                            </div>
                        </div>
                        <div class="rounded-[32px] bg-white/10 p-5">
                            <p class="text-[10px] uppercase text-primary">
                                Total Zakat
                            </p>
                            <p class="mt-3 text-3xl font-black text-white">
                                Rp 250.000
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
