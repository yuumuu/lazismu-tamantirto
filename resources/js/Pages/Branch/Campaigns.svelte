<script>
    import { Link } from '@inertiajs/svelte';
    import Layout from '../../Layouts/GuestLayout.svelte';
    import ArrowRight from 'lucide-svelte/icons/arrow-right';

    let { branch = {}, campaigns = { data: [], meta: {} } } = $props();

    const isExpired = (endDate) => endDate && new Date(endDate) < new Date();

    function formatRupiahShort(val) {
        if (!val) return 'Rp 0';
        const n = Number(val);
        if (n >= 1_000_000_000) return 'Rp ' + (n / 1_000_000_000).toFixed(1).replace(/\.0$/, '') + 'B';
        if (n >= 1_000_000) return 'Rp ' + (n / 1_000_000).toFixed(1).replace(/\.0$/, '') + 'M';
        if (n >= 1_000) return 'Rp ' + (n / 1_000).toFixed(1).replace(/\.0$/, '') + 'K';
        return 'Rp ' + n.toLocaleString('id-ID');
    }

    function progress(campaign) {
        if (!campaign.target_amount) return 0;
        const collected = Number(campaign.verified_donations_sum_amount || campaign.current_amount || 0);
        return Math.min((collected / Number(campaign.target_amount)) * 100, 100);
    }

    function formatDate(dateStr) {
        if (!dateStr) return '';
        return new Date(dateStr).toLocaleDateString('id-ID', {
            day: 'numeric', month: 'long', year: 'numeric',
        });
    }
</script>

<svelte:head><title>Program {branch.name} | Lazismu</title></svelte:head>

<Layout>
    <section class="py-20 bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-white/5">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="max-w-3xl space-y-4">
                <Link href="/cabang/{branch.slug}" class="text-sm font-bold text-primary hover:text-primary/80 transition-colors">
                    &larr; Kembali ke {branch.name}
                </Link>
                <h1 class="text-4xl md:text-5xl font-black tracking-tight text-zinc-900 dark:text-white">
                    Program {branch.name}
                </h1>
                <p class="text-lg text-zinc-500 dark:text-zinc-400">
                    Jelajahi program-program donasi dan pemberdayaan dari {branch.name}.
                </p>
            </div>
        </div>
    </section>

    <section class="py-24 bg-white dark:bg-zinc-950">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            {#if campaigns.data.length > 0}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    {#each campaigns.data as campaign}
                        <div class="group bg-white dark:bg-zinc-900 rounded-[40px] overflow-hidden border border-zinc-200 dark:border-white/5 shadow-sm hover:shadow-xl transition-all hover:border-primary/20 flex flex-col">
                            <div class="relative aspect-[4/3] overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                                {#if campaign.featured_image || campaign.image || campaign.image_path}
                                    <img
                                        src={(campaign.featured_image || campaign.image || campaign.image_path)?.startsWith('http')
                                            ? (campaign.featured_image || campaign.image || campaign.image_path)
                                            : `/storage/${campaign.featured_image || campaign.image || campaign.image_path}`}
                                        alt={campaign.title}
                                        class="size-full object-cover group-hover:scale-105 transition-transform duration-700"
                                    >
                                {:else}
                                    <div class="flex items-center justify-center size-full">
                                        <span class="text-4xl font-black text-zinc-300 dark:text-zinc-700">{campaign.title?.[0] || 'P'}</span>
                                    </div>
                                {/if}
                                {#if campaign.category}
                                    <div class="absolute top-4 left-4">
                                        <span class="px-3 py-1.5 rounded-xl bg-white/90 dark:bg-zinc-900/90 text-xs font-bold uppercase tracking-wider text-zinc-900 dark:text-white backdrop-blur-sm shadow-lg">
                                            {campaign.category.name}
                                        </span>
                                    </div>
                                {/if}
                                {#if isExpired(campaign.end_date)}
                                    <div class="absolute top-4 right-4">
                                        <span class="px-3 py-1.5 rounded-xl bg-red-500/90 text-xs font-bold uppercase tracking-wider text-white backdrop-blur-sm shadow-lg">
                                            Berakhir
                                        </span>
                                    </div>
                                {/if}
                            </div>
                            <div class="flex flex-col flex-1 p-6 md:p-8">
                                <h3 class="text-lg font-black text-zinc-900 dark:text-white group-hover:text-primary transition-colors">
                                    {campaign.title}
                                </h3>
                                <p class="text-zinc-500 dark:text-zinc-400 text-sm mt-3 line-clamp-2 leading-relaxed">
                                    {campaign.short_description || campaign.description || ''}
                                </p>
                                <div class="mt-6 space-y-3">
                                    {#if campaign.target_amount}
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-zinc-500 dark:text-zinc-400 font-medium">Terkumpul</span>
                                            <span class="font-black text-zinc-900 dark:text-white">{formatRupiahShort(campaign.verified_donations_sum_amount || campaign.current_amount || 0)}</span>
                                        </div>
                                        <div class="h-2.5 rounded-full bg-zinc-100 dark:bg-white/5 overflow-hidden">
                                            <div class="h-full rounded-full bg-gradient-to-r from-primary to-primary/60 transition-all duration-700" style="width: {progress(campaign)}%"></div>
                                        </div>
                                        <div class="flex items-center justify-between text-xs">
                                            <span class="text-zinc-400">{formatRupiahShort(campaign.target_amount)} target</span>
                                            <span class="font-bold text-zinc-500 dark:text-zinc-400">{Math.round(progress(campaign))}%</span>
                                        </div>
                                    {:else}
                                        <div class="flex items-center justify-between text-sm">
                                            <span class="text-zinc-500 dark:text-zinc-400 font-medium">Terkumpul</span>
                                            <span class="font-black text-zinc-900 dark:text-white">{formatRupiahShort(campaign.verified_donations_sum_amount || campaign.current_amount || 0)}</span>
                                        </div>
                                    {/if}
                                </div>
                                {#if campaign.end_date}
                                    <div class="mt-4 pt-4 border-t border-zinc-100 dark:border-white/5">
                                        <p class="text-xs text-zinc-400 font-medium">
                                            {#if isExpired(campaign.end_date)}
                                                Berakhir {formatDate(campaign.end_date)}
                                            {:else}
                                                Batas donasi: {formatDate(campaign.end_date)}
                                            {/if}
                                        </p>
                                    </div>
                                {/if}
                                <div class="mt-auto pt-6">
                                    <Link
                                        href={campaign.is_donation_open !== false ? `/program/${campaign.slug}` : '#'}
                                        class="inline-flex items-center justify-center w-full rounded-2xl bg-primary px-5 py-3 text-sm font-black uppercase text-white shadow-lg hover:bg-primary/90 transition-all gap-2"
                                    >
                                        Donasi
                                        <ArrowRight class="size-4" />
                                    </Link>
                                </div>
                            </div>
                        </div>
                    {/each}
                </div>

                <!-- Pagination -->
                {#if campaigns.meta?.last_page > 1}
                    <div class="mt-16 flex justify-center gap-2">
                        {#each Array(campaigns.meta.last_page) as _, i}
                            <Link
                                href={campaigns.meta.links?.[i + 1]?.url || '#'}
                                class="px-5 py-3 rounded-2xl text-sm font-bold transition-all {campaigns.meta.current_page === i + 1 ? 'bg-primary text-white shadow-lg' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-primary/10'}">
                                {i + 1}
                            </Link>
                        {/each}
                    </div>
                {/if}
            {:else}
                <div class="text-center py-20">
                    <p class="text-zinc-400 font-bold text-lg">Belum ada program donasi di cabang ini.</p>
                    <Link href="/cabang/{branch.slug}" class="inline-flex items-center mt-6 text-primary font-bold hover:underline">
                        Kembali ke halaman {branch.name}
                    </Link>
                </div>
            {/if}
        </div>
    </section>
</Layout>
