<script>
    import Layout from "../Layouts/GuestLayout.svelte";
    import HeroCarousel from "../Components/Guest/HeroCarousel.svelte";
    import GuestShortcuts from "../Components/Guest/GuestShortcuts.svelte";
    import StatsCounter from "../Components/Guest/StatsCounter.svelte";
    import CampaignCard from "../Components/Guest/CampaignCard.svelte";
    import CtaCalculator from "../Components/Guest/CtaCalculator.svelte";
    import ProgramPillars from "../Components/Guest/ProgramPillars.svelte";
    import NewsGrid from "../Components/Guest/NewsGrid.svelte";
    import ArrowRight from 'lucide-svelte/icons/arrow-right';

    let {
        featuredCampaigns = [],
        latestPosts = [],
        banners = [],
        stats = {},
        activeBranches = [],
    } = $props();
</script>

<svelte:head>
    <title>Beranda | Lazismu</title>
</svelte:head>

<Layout>
    <HeroCarousel {banners} />
    <GuestShortcuts />
    <StatsCounter {stats} />

    {#if activeBranches.length > 0}
        <section class="py-24 bg-zinc-50 dark:bg-zinc-900">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mb-16 flex flex-col gap-6 md:flex-row md:items-end md:justify-between">
                    <div class="max-w-2xl">
                        <p class="text-xs font-black uppercase text-primary">Cabang Kami</p>
                        <h2 class="mt-4 text-4xl font-black tracking-tight text-zinc-900 dark:text-white sm:text-5xl">
                            Lazismu di Seluruh Indonesia
                        </h2>
                    </div>
                </div>

                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    {#each activeBranches as branch}
                        <a href="/cabang/{branch.slug}"
                            class="group bg-white dark:bg-zinc-800 rounded-[32px] border border-zinc-200 dark:border-white/5 p-6 hover:shadow-xl hover:border-primary/30 transition-all"
                        >
                            <div class="flex items-center gap-4">
                                <div class="size-16 rounded-2xl bg-primary/10 flex items-center justify-center text-primary font-black text-xl shrink-0">
                                    {branch.name?.charAt(0) || 'L'}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-black text-zinc-900 dark:text-white group-hover:text-primary transition-colors truncate">{branch.name}</h3>
                                    {#if branch.address}
                                        <p class="text-xs text-zinc-500 mt-1 line-clamp-2">{branch.address}</p>
                                    {/if}
                                </div>
                                <ArrowRight class="size-5 text-zinc-300 group-hover:text-primary group-hover:translate-x-1 transition-all shrink-0" />
                            </div>
                        </a>
                    {/each}
                </div>
            </div>
        </section>
    {/if}

    <section class="py-24 bg-white dark:bg-zinc-950">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div
                class="mb-16 flex flex-col gap-6 md:flex-row md:items-end md:justify-between"
            >
                <div class="max-w-2xl">
                    <p class="text-xs font-black uppercase text-primary">
                        Program Pilihan
                    </p>
                    <h2
                        class="mt-4 text-4xl font-black tracking-tight text-zinc-900 dark:text-white sm:text-5xl"
                    >
                        Kebaikan yang Bisa Anda Bantu Hari Ini
                    </h2>
                </div>
                <a
                    href="/program"
                    class="inline-flex items-center justify-center rounded-2xl border border-zinc-200 bg-white px-6 py-3 text-sm font-black uppercase text-zinc-900 shadow-sm transition hover:border-primary hover:text-primary dark:border-white/10 dark:bg-zinc-900 dark:text-white"
                >
                    Lihat Semua Program
                </a>
            </div>

            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
                {#each featuredCampaigns as campaign}
                    <CampaignCard {campaign} />
                {/each}
            </div>

            {#if featuredCampaigns.length === 0}
                <div
                    class="rounded-[32px] border border-dashed border-zinc-200 p-16 text-center text-zinc-500 dark:border-white/10 dark:text-zinc-400"
                >
                    Belum ada program unggulan saat ini.
                </div>
            {/if}
        </div>
    </section>

    <CtaCalculator />
    <ProgramPillars />
    <section class="py-16 bg-primary">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="text-white space-y-2">
                    <h2 class="text-3xl font-black tracking-tight">Butuh Bantuan?</h2>
                    <p class="text-white/80 font-medium">Ajukan permohonan bantuan atau cek status pengajuan Anda.</p>
                </div>
                <div class="flex gap-4">
                    <a href="/pengajuan"
                        class="inline-flex items-center gap-2 rounded-2xl bg-white text-primary px-6 py-4 font-black uppercase tracking-wider shadow-xl hover:bg-zinc-100 transition-all"
                    >
                        Ajukan Bantuan
                        <ArrowRight class="size-5" />
                    </a>
                    <a href="/cek-status"
                        class="inline-flex items-center gap-2 rounded-2xl bg-white/10 text-white px-6 py-4 font-black uppercase tracking-wider border border-white/20 hover:bg-white/20 transition-all"
                    >
                        Cek Status
                    </a>
                </div>
            </div>
        </div>
    </section>
    <NewsGrid posts={latestPosts} />
</Layout>
