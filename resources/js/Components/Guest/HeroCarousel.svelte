<script>
    import { ArrowLeft, ArrowRight } from "lucide-svelte";
    import { onMount } from "svelte";
    import { Link } from "@inertiajs/svelte";

    export let banners = [];

    let active = 0;
    let interval = null;

    const next = () => {
        if (banners.length === 0) return;
        active = (active + 1) % banners.length;
    };

    const previous = () => {
        if (banners.length === 0) return;
        active = (active - 1 + banners.length) % banners.length;
    };

    onMount(() => {
        if (banners.length <= 1) return;

        interval = setInterval(next, 7000);

        return () => {
            clearInterval(interval);
        };
    });
</script>

<section class="relative overflow-hidden bg-zinc-950 text-white">
    {#if banners.length > 0}
        <div class="overflow-hidden">
            <div
                class="flex transition-transform duration-700 ease-out"
                style="width: {banners.length *
                    100}% ; transform: translateX(-{active *
                    (100 / banners.length)}%);"
            >
                {#each banners as banner}
                    <div class="min-w-full relative h-[720px] min-h-[520px]">
                        <div class="absolute inset-0">
                            <img
                                src={banner.image_path ||
                                    banner.image ||
                                    "/images/hero-fallback.jpg"}
                                alt={banner.title || "Hero banner"}
                                class="w-full h-full object-cover"
                            />
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-zinc-950/95 via-zinc-950/70 to-transparent"
                            ></div>
                            <div
                                class="absolute inset-x-0 bottom-0 h-64 bg-gradient-to-t from-zinc-950/90 to-transparent"
                            ></div>
                        </div>

                        <div
                            class="relative z-10 mx-auto flex h-full max-w-7xl flex-col justify-center px-6 lg:px-8"
                        >
                            <div class="max-w-2xl space-y-6">
                                <span
                                    class="inline-flex rounded-full border border-primary/30 bg-primary/10 px-4 py-2 text-xs font-black uppercase text-primary"
                                >
                                    {banner.subtitle || "Kebaikan Untuk Semua"}
                                </span>
                                <h1
                                    class="text-4xl font-black leading-tight tracking-tight text-white sm:text-5xl lg:text-6xl"
                                >
                                    {banner.title ||
                                        "Aksi Nyata, Manfaat Besar untuk Komunitas"}
                                </h1>
                                <p
                                    class="max-w-xl text-base text-zinc-200 sm:text-lg lg:text-xl"
                                >
                                    {banner.description ||
                                        banner.excerpt ||
                                        "Donasi yang tersalurkan dengan transparansi, amanah, dan dampak nyata untuk keluarga dan program sosial."}
                                </p>
                                <div class="flex flex-wrap gap-4">
                                    <Link
                                        href={banner.button_link || "/donasi"}
                                        class="inline-flex items-center justify-center rounded-2xl bg-primary px-8 py-4 text-sm font-black uppercase tracking-widest text-white shadow-2xl shadow-primary/30 transition hover:bg-primary/90"
                                    >
                                        {banner.button_text ||
                                            "Donasi Sekarang"}
                                    </Link>
                                    <Link
                                        href="/program"
                                        class="inline-flex items-center justify-center rounded-2xl border border-white/20 bg-white/10 px-8 py-4 text-sm font-bold uppercase tracking-widest text-white transition hover:border-white/30 hover:bg-white/15"
                                    >
                                        Jelajahi Program
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                {/each}
            </div>
        </div>

        <div
            class="pointer-events-none absolute inset-x-0 bottom-10 flex justify-center gap-3"
        >
            {#each banners as _, dotIndex}
                <button
                    type="button"
                    class={`h-2 rounded-full transition-all duration-300 ${
                        active === dotIndex
                            ? "w-10 bg-primary"
                            : "w-4 bg-white/30"
                    }`}
                    on:click={() => (active = dotIndex)}
                    aria-label={`Slide ${dotIndex + 1}`}
                />
            {/each}
        </div>

        <div class="absolute inset-y-0 left-6 flex items-center lg:left-12">
            <button
                type="button"
                class="rounded-full bg-white/10 p-3 text-white shadow-lg transition hover:bg-white/20"
                on:click={previous}
                aria-label="Sebelumnya"
            >
                <ArrowLeft class="h-5 w-5" />
            </button>
        </div>

        <div class="absolute inset-y-0 right-6 flex items-center lg:right-12">
            <button
                type="button"
                class="rounded-full bg-white/10 p-3 text-white shadow-lg transition hover:bg-white/20"
                on:click={next}
                aria-label="Berikutnya"
            >
                <ArrowRight class="h-5 w-5" />
            </button>
        </div>
    {:else}
        <div
            class="relative h-[520px] bg-gradient-to-r from-primary via-orange-500 to-amber-400 text-white"
        >
            <div
                class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(255,255,255,0.18),_transparent_30%)]"
            ></div>
            <div
                class="relative mx-auto flex h-full max-w-7xl flex-col justify-center px-6 lg:px-8"
            >
                <span
                    class="inline-flex rounded-full bg-white/10 px-4 py-2 text-xs font-black uppercase text-white/90"
                >
                    Wujudkan Kebaikan
                </span>
                <h1
                    class="mt-6 text-4xl font-black leading-tight tracking-tight sm:text-5xl lg:text-6xl"
                >
                    Lazismu Siap Menyalurkan Donasi Anda
                </h1>
                <p
                    class="mt-6 max-w-2xl text-base text-white/90 sm:text-lg lg:text-xl"
                >
                    Akses program zakat, infaq, dan donasi kemanusiaan dengan
                    antarmuka cepat dan tampilan yang modern.
                </p>
                <div class="mt-8 flex flex-wrap gap-4">
                    <Link
                        href="/program"
                        class="inline-flex items-center justify-center rounded-2xl bg-white px-8 py-4 text-sm font-black uppercase tracking-widest text-zinc-950 transition hover:bg-zinc-100"
                    >
                        Lihat Program
                    </Link>
                    <Link
                        href="/zakat-kalkulator"
                        class="inline-flex items-center justify-center rounded-2xl border border-white/30 bg-white/10 px-8 py-4 text-sm font-bold uppercase tracking-widest text-white transition hover:bg-white/20"
                    >
                        Kalkulator Zakat
                    </Link>
                </div>
            </div>
        </div>
    {/if}
</section>
