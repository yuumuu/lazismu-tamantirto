<script>
    import ArrowLeft from "lucide-svelte/icons/arrow-left";
    import ArrowRight from "lucide-svelte/icons/arrow-right";
    import { onMount } from "svelte";
    import { Link } from "@inertiajs/svelte";

    export let banners = [];

    let active = 0;
    let container;
    let interval;

    const scrollTo = (index) => {
        if (!container || banners.length === 0) return;
        active = index;
        const width = container.clientWidth;
        container.scrollTo({
            left: width * index,
            behavior: "smooth",
        });
    };

    const next = () => {
        if (banners.length === 0) return;
        scrollTo((active + 1) % banners.length);
    };

    const previous = () => {
        if (banners.length === 0) return;
        scrollTo((active - 1 + banners.length) % banners.length);
    };

    const handleScroll = () => {
        if (!container) return;
        const width = container.clientWidth;
        const scrollLeft = container.scrollLeft;
        active = Math.round(scrollLeft / width);
    };

    const pause = () => clearInterval(interval);
    const resume = () => {
        clearInterval(interval);
        if (banners.length > 1) {
            interval = setInterval(next, 7000);
        }
    };

    onMount(() => {
        resume();
        return () => pause();
    });
</script>

<section class="relative overflow-hidden bg-zinc-950 text-white" aria-label="Hero banner" on:mouseenter={pause} on:mouseleave={resume}>
    {#if banners.length > 0}
        <div 
            bind:this={container}
            on:scroll={handleScroll}
            class="flex w-full overflow-x-auto snap-x snap-mandatory scroll-smooth"
            style="scrollbar-width: none; -ms-overflow-style: none;"
        >
            <style>
                div::-webkit-scrollbar {
                    display: none;
                }
            </style>
            
            {#each banners as banner}
                <div class="snap-center shrink-0 w-full relative h-[720px] min-h-[520px]">
                    <div class="absolute inset-0">
                        <img
                            src={banner.image_path || banner.image || "/images/hero-fallback.jpg"}
                            alt={banner.title || "Hero banner"}
                            class="w-full h-full object-cover"
                        />
                        <div class="absolute inset-0 bg-gradient-to-r from-zinc-950/95 via-zinc-950/70 to-transparent"></div>
                        <div class="absolute inset-x-0 bottom-0 h-64 bg-gradient-to-t from-zinc-950/90 to-transparent"></div>
                    </div>

                    <div class="relative z-10 mx-auto flex h-full max-w-7xl flex-col justify-center px-6 lg:px-8">
                        <div class="max-w-2xl space-y-6">
                            <span class="inline-flex rounded-full border border-primary/30 bg-primary/10 px-4 py-2 text-xs font-black uppercase text-primary">
                                {banner.subtitle || "Kebaikan Untuk Semua"}
                            </span>
                            <h1 class="text-4xl font-black leading-tight tracking-tight text-white sm:text-5xl lg:text-6xl">
                                {banner.title || "Aksi Nyata, Manfaat Besar untuk Komunitas"}
                            </h1>
                            <p class="max-w-xl text-base text-zinc-200 sm:text-lg lg:text-xl">
                                {banner.description || banner.excerpt || "Donasi yang tersalurkan dengan transparansi, amanah, dan dampak nyata untuk keluarga dan program sosial."}
                            </p>
                            <div class="flex flex-wrap gap-4">
                                <Link
                                    href={banner.button_link || "/donasi"}
                                    class="inline-flex items-center justify-center rounded-2xl bg-primary px-8 py-4 text-sm font-black uppercase tracking-widest text-white shadow-2xl shadow-primary/30 transition hover:bg-primary/90"
                                >
                                    {banner.button_text || "Donasi Sekarang"}
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

        {#if banners.length > 1}
            <div class="pointer-events-none absolute inset-x-0 bottom-10 flex justify-center gap-3">
                {#each banners as _, dotIndex}
                    <button
                        type="button"
                        class={`h-2 rounded-full transition-all duration-300 pointer-events-auto ${
                            active === dotIndex ? "w-10 bg-primary" : "w-4 bg-white/30 hover:bg-white/50"
                        }`}
                        on:click={() => scrollTo(dotIndex)}
                        aria-label={`Slide ${dotIndex + 1}`}
                    ></button>
                {/each}
            </div>

            <div class="absolute inset-y-0 left-6 hidden items-center lg:flex lg:left-12">
                <button
                    type="button"
                    class="rounded-full bg-white/10 p-3 text-white shadow-lg transition hover:bg-white/20"
                    on:click={previous}
                    aria-label="Sebelumnya"
                >
                    <ArrowLeft class="h-5 w-5" />
                </button>
            </div>

            <div class="absolute inset-y-0 right-6 hidden items-center lg:flex lg:right-12">
                <button
                    type="button"
                    class="rounded-full bg-white/10 p-3 text-white shadow-lg transition hover:bg-white/20"
                    on:click={next}
                    aria-label="Berikutnya"
                >
                    <ArrowRight class="h-5 w-5" />
                </button>
            </div>
        {/if}
    {:else}
        <div class="relative h-[520px] bg-gradient-to-r from-primary via-orange-500 to-amber-400 text-white">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(255,255,255,0.18),_transparent_30%)]"></div>
            <div class="relative mx-auto flex h-full max-w-7xl flex-col justify-center px-6 lg:px-8">
                <span class="inline-flex rounded-full bg-white/10 px-4 py-2 text-xs font-black uppercase text-white/90">
                    Wujudkan Kebaikan
                </span>
                <h1 class="mt-6 text-4xl font-black leading-tight tracking-tight sm:text-5xl lg:text-6xl">
                    Lazismu Siap Menyalurkan Donasi Anda
                </h1>
                <p class="mt-6 max-w-2xl text-base text-white/90 sm:text-lg lg:text-xl">
                    Akses program zakat, infaq, dan donasi kemanusiaan dengan antarmuka cepat dan tampilan yang modern.
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
