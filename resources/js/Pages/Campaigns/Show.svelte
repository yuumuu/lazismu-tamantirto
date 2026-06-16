<script>
    import { Link, page } from '@inertiajs/svelte';
    import Layout from '../../Layouts/GuestLayout.svelte';
    import Calendar from 'lucide-svelte/icons/calendar';
    import Users from 'lucide-svelte/icons/users';
    import Share from 'lucide-svelte/icons/share';
    import Heart from 'lucide-svelte/icons/heart';
    import Phone from 'lucide-svelte/icons/phone';
    import Clock from 'lucide-svelte/icons/clock';

    let { campaign = null, donors = [], donorsCount = 0, relatedPosts = [], whatsapp = '08123456789' } = $props();

    const site = $derived(page.props.site || {});

    function formatRupiah(val) {
        if (!val) return 'Rp 0';
        return 'Rp ' + Number(val).toLocaleString('id-ID');
    }

    function progress(campaign) {
        if (!campaign?.target_amount) return 0;
        const collected = Number(campaign.verified_donations_sum_amount || campaign.current_amount || 0);
        return Math.min((collected / Number(campaign.target_amount)) * 100, 100);
    }

    function diffForHumans(dateStr) {
        if (!dateStr) return '';
        const diff = Date.now() - new Date(dateStr).getTime();
        const mins = Math.floor(diff / 60000);
        if (mins < 1) return 'baru saja';
        if (mins < 60) return `${mins} menit lalu`;
        const hours = Math.floor(mins / 60);
        if (hours < 24) return `${hours} jam lalu`;
        const days = Math.floor(hours / 24);
        if (days < 30) return `${days} hari lalu`;
        const months = Math.floor(days / 30);
        if (months < 12) return `${months} bulan lalu`;
        return `${Math.floor(months / 12)} tahun lalu`;
    }

    function formatDate(dateStr) {
        if (!dateStr) return '';
        return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
    }

    let copied = $state(false);
    async function copyUrl() {
        try {
            await navigator.clipboard.writeText(window.location.href);
            copied = true;
            setTimeout(() => copied = false, 2000);
        } catch {}
    }

    const fallbackImg = 'https://images.unsplash.com/photo-1532629345422-7515f3d16bb6?q=80&w=2070&auto=format&fit=crop';
</script>

<svelte:head>
    <title>{campaign?.title || 'Program'} | Lazismu</title>
</svelte:head>

<Layout>
    {#if campaign}
        <!-- Background Header (Blur) -->
        <div class="relative h-[400px] md:h-[500px] overflow-hidden">
            <img
                src={campaign.featured_image_url || fallbackImg}
                alt=""
                class="size-full object-cover blur-2xl scale-110 opacity-30 dark:opacity-20"
            />
            <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-white dark:from-zinc-950 to-transparent"></div>
        </div>

        <!-- Main Content -->
        <div class="mx-auto max-w-7xl px-6 lg:px-8 -mt-64 md:-mt-80 relative z-10 pb-24">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-12">
                    <!-- Hero Card -->
                    <div class="bg-white dark:bg-zinc-900 rounded-[40px] overflow-hidden border border-zinc-200 dark:border-white/5 shadow-2xl">
                        <div class="aspect-video relative overflow-hidden group">
                            <img
                                src={campaign.featured_image_url || fallbackImg}
                                alt={campaign.title}
                                class="size-full object-cover group-hover:scale-105 transition-transform duration-1000"
                            />
                            <div class="absolute top-8 left-8 flex flex-col gap-3">
                                {#if campaign.is_urgent}
                                    <span class="px-4 py-2 rounded-full bg-red-500 text-white text-xs font-black uppercase tracking-widest shadow-xl">🚨 Darurat</span>
                                {/if}
                                <span class="px-4 py-2 rounded-full bg-primary/90 text-white text-xs font-black uppercase tracking-widest shadow-xl backdrop-blur-md">
                                    {campaign.category?.name || 'Program'}
                                </span>
                            </div>
                        </div>

                        <div class="p-8 md:p-12 space-y-8">
                            <div class="space-y-4">
                                <h1 class="text-3xl md:text-5xl font-black text-zinc-900 dark:text-white leading-[1.1] tracking-tighter">
                                    {campaign.title}
                                </h1>
                                <div class="flex flex-wrap items-center gap-6 text-sm font-bold text-zinc-500">
                                    <span class="flex items-center gap-2">
                                        <Calendar class="size-4 text-primary" />
                                        {#if campaign.end_date}
                                            Berakhir pada {formatDate(campaign.end_date)}
                                        {:else}
                                            Program Berkelanjutan
                                        {/if}
                                    </span>
                                    <span class="flex items-center gap-2">
                                        <Users class="size-4 text-primary" />
                                        {donorsCount} Donatur Bergabung
                                    </span>
                                </div>
                            </div>

                            <!-- Progress -->
                            <div class="bg-zinc-50 dark:bg-white/5 p-8 rounded-[32px] border border-zinc-100 dark:border-white/5 space-y-6">
                                <div class="flex justify-between items-end">
                                    <div class="space-y-1">
                                        <span class="text-xs text-zinc-400 font-black uppercase">Dana Terkumpul</span>
                                        <div class="flex items-baseline gap-2">
                                            <span class="text-3xl md:text-4xl font-black text-primary tracking-tighter">{formatRupiah(campaign.verified_donations_sum_amount || campaign.current_amount)}</span>
                                            {#if campaign.target_amount > 0}
                                                <span class="text-sm font-bold text-zinc-500">dari {formatRupiah(campaign.target_amount)}</span>
                                            {/if}
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-3xl font-black text-primary tracking-tighter">{Math.round(progress(campaign))}%</span>
                                    </div>
                                </div>

                                <div class="relative w-full h-4 bg-zinc-200 dark:bg-white/10 rounded-full overflow-hidden">
                                    <div class="absolute inset-y-0 left-0 bg-primary rounded-full transition-all duration-1000"
                                        style="width: {progress(campaign)}%"
                                    >
                                        <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-shimmer"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="space-y-8">
                                <div class="prose prose-zinc dark:prose-invert max-w-none prose-p:leading-relaxed prose-p:text-zinc-600 dark:prose-p:text-zinc-400 prose-headings:font-black prose-headings:tracking-tight prose-strong:text-zinc-900 dark:prose-strong:text-white">
                                    {@html campaign.description}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Donors List -->
                    <div class="bg-white dark:bg-zinc-900 rounded-[32px] p-8 md:p-12 border border-zinc-200 dark:border-white/5 shadow-xl space-y-8">
                        <h3 class="text-2xl font-black text-zinc-900 dark:text-white tracking-tight">Donatur Dermawan ({donorsCount})</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {#if donors.length > 0}
                                {#each donors as donation}
                                    <div class="flex items-center gap-4 p-4 rounded-2xl bg-zinc-50 dark:bg-white/5 border border-zinc-100 dark:border-white/5">
                                        <div class="size-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary font-black uppercase tracking-wider text-xs">
                                            {donation.is_anonymous ? 'HA' : (donation.donor_name?.substring(0, 2) || '??')}
                                        </div>
                                        <div class="flex-1">
                                            <div class="flex justify-between items-center mb-0.5">
                                                <span class="font-bold text-zinc-900 dark:text-white">{donation.is_anonymous ? 'Hamba Allah' : donation.donor_name}</span>
                                                <span class="text-xs font-black text-primary">{formatRupiah(donation.amount)}</span>
                                            </div>
                                            {#if donation.donor_message}
                                                <p class="text-[10px] text-zinc-500 font-medium line-clamp-1 italic">"{donation.donor_message}"</p>
                                            {/if}
                                            <p class="text-[9px] text-zinc-400 font-bold uppercase tracking-widest mt-1">{diffForHumans(donation.created_at || donation.verified_at)}</p>
                                        </div>
                                    </div>
                                {/each}
                            {:else}
                                <div class="col-span-full py-12 text-center">
                                    <p class="text-zinc-500 italic font-medium">Belum ada donasi terverifikasi untuk program ini.</p>
                                </div>
                            {/if}
                        </div>
                        {#if donorsCount > 10}
                            <button class="w-full py-4 rounded-2xl font-bold border-2 border-zinc-200 dark:border-white/10 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-50 dark:hover:bg-white/5 transition-all cursor-pointer">
                                Lihat Semua Donatur
                            </button>
                        {/if}
                    </div>
                </div>

                <!-- Right Column: Sticky CTA -->
                <div class="space-y-8">
                    <div class="sticky top-24 space-y-6">
                        <div class="bg-white dark:bg-zinc-900 rounded-[32px] p-8 border border-zinc-200 dark:border-white/5 shadow-2xl shadow-primary/10 space-y-8">
                            <div class="space-y-2">
                                <h4 class="text-sm font-black text-zinc-900 dark:text-white uppercase tracking-widest">Ayo Bantu Sekarang</h4>
                                <p class="text-xs text-zinc-500 font-medium italic">Pahala jariyah yang tak terputus melalui sedekah terbaik Anda.</p>
                            </div>

                            <div class="space-y-4">
                                <a href={`/donasi/${campaign.slug}`}
                                    class="block w-full h-16 rounded-xl bg-primary text-white font-black uppercase shadow-xl shadow-primary/30 hover:bg-primary/90 transition-all flex items-center justify-center"
                                >
                                    Donasi Sekarang
                                </a>

                                <div class="grid grid-cols-2 gap-4">
                                    <button onclick={copyUrl}
                                        class="relative flex flex-col items-center justify-center gap-2 p-4 rounded-2xl bg-zinc-50 dark:bg-white/5 border border-zinc-100 dark:border-white/5 hover:bg-primary/5 hover:border-primary/20 transition-all group cursor-pointer"
                                    >
                                        <Share class="size-6 text-zinc-400 group-hover:text-primary transition-colors" />
                                        <span class="text-[10px] font-black uppercase tracking-wider text-zinc-500 group-hover:text-primary transition-colors">Bagikan</span>
                                        {#if copied}
                                            <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-1 bg-zinc-900 text-white text-[10px] font-black uppercase tracking-widest rounded-lg shadow-xl whitespace-nowrap animate-bounce">
                                                Copied!
                                            </div>
                                        {/if}
                                    </button>
                                    <button
                                        class="flex flex-col items-center justify-center gap-2 p-4 rounded-2xl bg-zinc-50 dark:bg-white/5 border border-zinc-100 dark:border-white/5 hover:bg-primary/5 hover:border-primary/20 transition-all group cursor-pointer"
                                    >
                                        <Heart class="size-6 text-zinc-400 group-hover:text-primary transition-colors" />
                                        <span class="text-[10px] font-black uppercase tracking-wider text-zinc-500 group-hover:text-primary transition-colors">Doa Lukis</span>
                                    </button>
                                </div>
                            </div>

                            <div class="pt-6 border-t border-zinc-100 dark:border-white/5 flex items-center gap-4">
                                <img
                                    src="https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?q=80&w=1974&auto=format&fit=crop"
                                    class="size-10 rounded-full object-cover"
                                    alt="Admin"
                                />
                                <div>
                                    <p class="text-[10px] text-zinc-400 font-black uppercase tracking-widest leading-none mb-1">Dikelola Oleh</p>
                                    <p class="text-sm font-bold text-zinc-900 dark:text-white underline decoration-primary underline-offset-4 decoration-2">
                                        Admin {site.name || 'Lazismu'} {site.tagline || 'Tamantirto'}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- WhatsApp Widget -->
                        <a href={`https://wa.me/${whatsapp}?text=${encodeURIComponent('Hai Lazismu, saya ingin bertanya tentang program: ' + campaign.title)}`}
                            target="_blank"
                            class="bg-primary rounded-2xl p-6 text-white shadow-xl shadow-primary/20 flex items-center justify-between hover:bg-primary/90 transition-all group block"
                        >
                            <div class="space-y-1">
                                <h4 class="font-black uppercase tracking-wider text-sm">Butuh Bantuan?</h4>
                                <p class="text-xs font-medium text-white/80 italic">Hubungi tim Lazismu</p>
                            </div>
                            <div class="size-12 rounded-xl bg-white/20 backdrop-blur-md flex items-center justify-center group-hover:bg-white transition-all">
                                <Phone class="size-6 text-white group-hover:text-primary transition-colors" />
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    {/if}
</Layout>

