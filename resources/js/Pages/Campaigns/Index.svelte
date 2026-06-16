<script>
    import { Link, router, page } from '@inertiajs/svelte';
    import Layout from '../../Layouts/GuestLayout.svelte';
    import Heart from 'lucide-svelte/icons/heart';
    import Share from 'lucide-svelte/icons/share';
    import Clock from 'lucide-svelte/icons/clock';
    import Search from 'lucide-svelte/icons/search';
    import X from 'lucide-svelte/icons/x';

    let { campaigns = { data: [], meta: {} }, categories = [], filters = {} } = $props();

    let searchInput = $state(filters.search || '');
    let categoryFilter = $state(filters.category || '');
    let sortFilter = $state(filters.sort || 'latest');
    let typeFilter = $state(filters.type || '');

    const isExpired = (endDate) => endDate && new Date(endDate) < new Date();
    const hasFilters = filters.search || filters.category || filters.sort !== 'latest' || filters.type;

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

    function submitFilter() {
        router.get('/program', {
            search: searchInput,
            category: categoryFilter,
            sort: sortFilter,
            type: typeFilter,
        }, { preserveState: true, replace: true });
    }

    function resetFilters() {
        searchInput = '';
        categoryFilter = '';
        sortFilter = 'latest';
        typeFilter = '';
        router.get('/program', {}, { replace: true });
    }
</script>

<svelte:head><title>Program Donasi | Lazismu</title></svelte:head>

<Layout>
    <!-- Page Header -->
    <header class="py-20 bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-white/5">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="max-w-3xl space-y-6">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-1.5 bg-primary rounded-full"></div>
                    <h2 class="text-primary font-black uppercase text-xs">Program Kebaikan</h2>
                </div>
                <h1 class="text-4xl md:text-6xl font-black text-zinc-900 dark:text-white tracking-tight leading-tight">
                    Pilih Program Donasi & Alirkan Kebaikan
                </h1>
                <p class="text-lg text-zinc-500 font-medium">
                    Setiap rupiah yang Anda berikan adalah secercah harapan bagi mereka yang membutuhkan. Mari bergotong-royong membangun negeri.
                </p>
            </div>
        </div>
    </header>

    <!-- Content -->
    <section class="py-12 bg-white dark:bg-zinc-950 min-h-screen">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <!-- Toolbar -->
            <form onsubmit={(e) => { e.preventDefault(); submitFilter(); }}
                class="flex flex-col lg:flex-row gap-6 items-center justify-between mb-12"
            >
                <div class="w-full lg:w-1/3">
                    <div class="relative">
                        <Search class="absolute left-4 top-1/2 -translate-y-1/2 size-5 text-zinc-400" />
                        <input type="text" bind:value={searchInput} placeholder="Cari program donasi..."
                            class="w-full pl-12 pr-4 py-3 rounded-2xl border border-zinc-200 dark:border-white/10 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary text-sm font-medium"
                        />
                    </div>
                </div>

                <div class="flex items-center gap-4 w-full lg:w-auto">
                    <div class="flex-1 lg:w-48">
                        <select bind:value={categoryFilter} onchange={submitFilter}
                            class="w-full px-4 py-3 rounded-2xl border border-zinc-200 dark:border-white/10 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                        >
                            <option value="">Semua Kategori</option>
                            {#each categories as cat}
                                <option value={cat.id}>{cat.name}</option>
                            {/each}
                        </select>
                    </div>
                    <div class="flex-1 lg:w-48">
                        <select bind:value={sortFilter} onchange={submitFilter}
                            class="w-full px-4 py-3 rounded-2xl border border-zinc-200 dark:border-white/10 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white text-sm font-medium focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary"
                        >
                            <option value="latest">Terbaru</option>
                            <option value="urgent">Mendesak</option>
                            <option value="target">Target Terbesar</option>
                        </select>
                    </div>
                </div>
            </form>

            <!-- Campaign List -->
            <div class="space-y-8">
                {#if campaigns.data && campaigns.data.length > 0}
                    {#each campaigns.data as campaign}
                        <div class="bg-white dark:bg-zinc-900 rounded-[32px] overflow-hidden border border-zinc-200 dark:border-white/5 shadow-sm hover:shadow-xl hover:border-primary/20 transition-all group flex flex-col md:flex-row h-full">
                            <!-- Image -->
                            <div class="relative w-full md:w-[40%] aspect-[4/3] md:aspect-auto overflow-hidden">
                                <img
                                    src={campaign.featured_image_url || 'https://images.unsplash.com/photo-1532629345422-7515f3d16bb6?q=80&w=2070&auto=format&fit=crop'}
                                    alt={campaign.title}
                                    class="size-full object-cover group-hover:scale-105 transition-transform duration-700"
                                />
                                <div class="absolute top-4 left-4 flex flex-col gap-2">
                                    {#if campaign.is_urgent}
                                        <span class="px-3 py-1 rounded-full bg-red-500 text-white text-[10px] font-black uppercase tracking-wider shadow-lg">Mendesak</span>
                                    {/if}
                                    <span class="px-3 py-1 rounded-full bg-primary text-white text-[10px] font-black uppercase tracking-wider shadow-lg">
                                        {campaign.category?.name || 'Program'}
                                    </span>
                                </div>
                                {#if campaign.end_date}
                                    <div class="absolute bottom-4 left-4">
                                        <div class="px-3 py-2 rounded-xl bg-zinc-900/60 backdrop-blur-md border border-white/10 text-white text-[10px] font-bold flex items-center gap-2 w-fit">
                                            <Clock class="size-3.5 text-primary" />
                                            <span>{Math.ceil((new Date(campaign.end_date) - new Date()) / (1000 * 60 * 60 * 24))} Hari Lagi</span>
                                        </div>
                                    </div>
                                {/if}
                            </div>

                            <!-- Content -->
                            <div class="p-8 md:p-10 flex-1 flex flex-col justify-between space-y-6">
                                <div class="space-y-4">
                                    <h4 class="text-2xl font-black text-zinc-900 dark:text-white leading-tight line-clamp-2 group-hover:text-primary transition-colors">
                                        <a href={`/program/${campaign.slug}`}>{campaign.title}</a>
                                    </h4>
                                    <p class="text-sm text-zinc-500 line-clamp-2 font-medium">{campaign.short_description || ''}</p>
                                </div>

                                <div class="space-y-6">
                                    <div class="space-y-2">
                                        <div class="flex justify-between items-end">
                                            <div class="flex flex-col">
                                                <span class="text-[10px] text-zinc-400 font-black uppercase">Terkumpul</span>
                                                <span class="text-lg font-black text-primary tracking-tight">{formatRupiahShort(campaign.verified_donations_sum_amount || campaign.current_amount)}</span>
                                            </div>
                                            <div class="flex flex-col items-end">
                                                <span class="text-[10px] text-zinc-400 font-black uppercase">Target</span>
                                                <span class="text-sm font-bold text-zinc-900 dark:text-white">{formatRupiahShort(campaign.target_amount)}</span>
                                            </div>
                                        </div>
                                        <div class="relative w-full h-3 bg-zinc-100 dark:bg-white/5 rounded-full overflow-hidden">
                                            <div class="absolute top-0 left-0 h-full bg-primary rounded-full transition-all duration-1000"
                                                style="width: {progress(campaign)}%"
                                            >
                                                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent animate-shimmer"></div>
                                            </div>
                                        </div>
                                        <div class="flex justify-between text-[10px] font-bold uppercase tracking-widest">
                                            <span class="text-primary">{Math.round(progress(campaign))}% Tercapai</span>
                                            <span class="text-zinc-500">{campaign.verified_donations_count || 0} Donatur</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-4 pt-2">
                                        <a href={`/donasi/${campaign.slug}`}
                                            class="flex-1 inline-flex items-center justify-center h-14 rounded-xl bg-primary text-white font-black uppercase tracking-wider shadow-lg shadow-primary/20 hover:bg-primary/90 transition-all"
                                        >
                                            Donasi Sekarang
                                        </a>
                                        <a href={`/program/${campaign.slug}`}
                                            class="size-14 rounded-xl border border-zinc-200 dark:border-white/10 flex items-center justify-center hover:bg-zinc-50 dark:hover:bg-white/5 transition-all"
                                        >
                                            <Share class="size-6 text-zinc-500" />
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/each}
                {:else}
                    <div class="py-32 text-center">
                        <div class="inline-flex size-20 items-center justify-center rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-100 dark:border-white/5 text-zinc-300 mb-6">
                            <Search class="size-10" />
                        </div>
                        <h4 class="text-xl font-black text-zinc-900 dark:text-white mb-2 tracking-tight">Tidak Ada Program Ditemukan</h4>
                        <p class="text-zinc-500 font-medium italic">Coba cari dengan kata kunci lain atau ubah filter Anda.</p>
                        {#if hasFilters}
                            <div class="mt-6">
                                <button onclick={resetFilters}
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-2xl font-bold hover:bg-primary/90 transition-colors"
                                >
                                    Reset Filter
                                </button>
                            </div>
                        {/if}
                    </div>
                {/if}
            </div>

            <!-- Pagination -->
            {#if campaigns.links && campaigns.links.length > 3}
                <div class="mt-20 flex justify-center gap-2">
                    {#each campaigns.links as link}
                        {#if link.url}
                            <Link href={link.url}
                                class="px-4 py-2.5 rounded-xl text-sm font-bold transition-all
                                {link.active
                                    ? 'bg-primary text-white shadow-lg shadow-primary/20'
                                    : 'bg-white dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-white/10 hover:border-primary/30 hover:text-primary'}"
                            >
                                {@html link.label}
                            </Link>
                        {:else}
                            <span class="px-4 py-2.5 rounded-xl text-sm font-bold text-zinc-300 dark:text-zinc-700">{@html link.label}</span>
                        {/if}
                    {/each}
                </div>
            {/if}

            <!-- Results Info -->
            {#if campaigns.total}
                <div class="mt-8 text-center">
                    <p class="text-sm text-zinc-400 italic">
                        Menampilkan {campaigns.data?.length || 0} dari {campaigns.total} program
                        {#if hasFilters}
                            <span class="text-primary">dengan filter aktif</span>
                        {/if}
                    </p>
                </div>
            {/if}
        </div>
    </section>
</Layout>

