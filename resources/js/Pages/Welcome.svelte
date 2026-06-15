<script>
    import Layout from "../Layouts/GuestLayout.svelte";
    import HeroCarousel from "../Components/Guest/HeroCarousel.svelte";
    import GuestShortcuts from "../Components/Guest/GuestShortcuts.svelte";
    import StatsCounter from "../Components/Guest/StatsCounter.svelte";
    import CampaignCard from "../Components/Guest/CampaignCard.svelte";
    import CtaCalculator from "../Components/Guest/CtaCalculator.svelte";
    import ProgramPillars from "../Components/Guest/ProgramPillars.svelte";
    import NewsGrid from "../Components/Guest/NewsGrid.svelte";

    let {
        featuredCampaigns = [],
        latestPosts = [],
        banners = [],
        stats = {},
    } = $props();
</script>

<svelte:head>
    <title>Beranda | Lazismu</title>
</svelte:head>

<Layout>
    <HeroCarousel {banners} />
    <GuestShortcuts />
    <StatsCounter {stats} />

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
    <NewsGrid posts={latestPosts} />
</Layout>
