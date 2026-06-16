<script>
    import Layout from "../../Layouts/GuestLayout.svelte";
    import CampaignCard from "../../Components/Guest/CampaignCard.svelte";
    import MapPin from "lucide-svelte/icons/map-pin";
    import Phone from "lucide-svelte/icons/phone";
    import Mail from "lucide-svelte/icons/mail";
    import ArrowRight from "lucide-svelte/icons/arrow-right";
    import { Link } from "@inertiajs/svelte";

    let { branch = {}, campaigns = [], posts = [], stats = {}, contact = {} } = $props();

    const branchTypes = {
        pusat: "Pusat",
        masjid: "Masjid",
        ranting: "Ranting",
        cabang_muhammadiyah: "Cabang Muhammadiyah",
        ortom: "Ortom",
        pcr: "PCR",
    };
</script>

<svelte:head>
    <title>{branch.name} | Lazismu</title>
</svelte:head>

<Layout>
    <!-- Branch Hero -->
    <section class="relative overflow-hidden bg-gradient-to-br from-zinc-900 via-zinc-800 to-zinc-950">
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48ZyBmaWxsPSJub25lIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiPjxnIGZpbGw9IiNmZmYiIGZpbGwtb3BhY2l0eT0iMC4wMyI+PGNpcmNsZSBjeD0iMzAiIGN5PSIzMCIgcj0iMiIvPjwvZz48L2c+PC9zdmc+')] opacity-50"></div>
        <div class="absolute -top-24 -right-24 size-96 rounded-full bg-primary/10 blur-3xl"></div>
        <div class="absolute -bottom-24 -left-24 size-96 rounded-full bg-primary/5 blur-3xl"></div>
        <div class="mx-auto max-w-7xl px-6 lg:px-8 relative py-28 md:py-36">
            <div class="flex flex-col md:flex-row md:items-center gap-8">
                {#if branch.logo_url}
                    <div class="size-28 rounded-[40px] bg-white/10 border border-white/10 flex items-center justify-center shrink-0 overflow-hidden shadow-2xl">
                        <img src={branch.logo_url} alt={branch.name} class="size-full object-cover">
                    </div>
                {:else}
                    <div class="size-28 rounded-[40px] bg-primary/20 border border-primary/20 flex items-center justify-center shrink-0 shadow-2xl">
                        <span class="text-4xl font-black text-white">{branch.name[0]}</span>
                    </div>
                {/if}
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="px-4 py-1.5 rounded-xl bg-white/10 text-white/80 text-xs font-bold uppercase tracking-widest">
                            {branchTypes[branch.type] || branch.type || 'Cabang'}
                        </span>
                    </div>
                    <h1 class="text-4xl md:text-6xl font-black text-white tracking-tight">
                        {branch.name}
                    </h1>
                    <p class="text-lg text-zinc-300 max-w-2xl">
                        Program donasi dan pemberdayaan {branch.name} — mari berkontribusi untuk kemaslahatan umat.
                    </p>
                    <div class="flex flex-wrap gap-4 pt-4">
                        <Link href="/program" class="inline-flex items-center justify-center rounded-2xl bg-primary px-8 py-4 text-sm font-black uppercase text-white shadow-xl shadow-primary/30 hover:bg-primary/90 transition-all">
                            Donasi Sekarang
                            <ArrowRight class="size-4 ml-2" />
                        </Link>
                        <Link href="/kontak" class="inline-flex items-center justify-center rounded-2xl border border-white/20 bg-white/10 px-8 py-4 text-sm font-bold uppercase text-white hover:bg-white/20 transition-all">
                            Hubungi Kami
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <section class="bg-white dark:bg-zinc-950 border-b border-zinc-200 dark:border-white/5">
        <div class="mx-auto max-w-7xl px-6 lg:px-8 py-12">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <p class="text-3xl md:text-4xl font-black text-primary">{stats.totalCampaigns || 0}</p>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 font-bold uppercase tracking-wider mt-2">Program</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl md:text-4xl font-black text-primary">{stats.totalDonations || 0}</p>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 font-bold uppercase tracking-wider mt-2">Donasi</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl md:text-4xl font-black text-primary">{new Intl.NumberFormat('id-ID').format(stats.donationAmount || 0)}</p>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 font-bold uppercase tracking-wider mt-2">Terkumpul</p>
                </div>
                <div class="text-center">
                    <p class="text-3xl md:text-4xl font-black text-primary">{stats.beneficiaries || 0}</p>
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 font-bold uppercase tracking-wider mt-2">Penerima Manfaat</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Campaigns -->
    <section class="py-24 bg-white dark:bg-zinc-950">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between mb-16 gap-6">
                <div class="max-w-2xl">
                    <p class="text-xs font-black uppercase text-primary tracking-widest">Program Donasi</p>
                    <h2 class="mt-4 text-3xl md:text-4xl font-black tracking-tight text-zinc-900 dark:text-white">
                        Program {branch.name}
                    </h2>
                </div>
                <a href="/cabang/{branch.slug}/program" class="inline-flex items-center justify-center rounded-2xl border border-zinc-200 bg-white px-6 py-3 text-sm font-black uppercase text-zinc-900 shadow-sm hover:border-primary hover:text-primary transition-all dark:border-white/10 dark:bg-zinc-900 dark:text-white">
                    Lihat Semua Program
                </a>
            </div>
            {#if campaigns.length > 0}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    {#each campaigns as campaign}
                        <CampaignCard {campaign} />
                    {/each}
                </div>
            {:else}
                <div class="text-center py-20 bg-zinc-50 dark:bg-zinc-900 rounded-[40px] border border-zinc-200 dark:border-white/5">
                    <p class="text-zinc-400 font-bold">Belum ada program donasi di cabang ini.</p>
                </div>
            {/if}
        </div>
    </section>

    <!-- News -->
    <section class="py-24 bg-zinc-50 dark:bg-zinc-900/50">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between mb-16 gap-6">
                <div class="max-w-2xl">
                    <p class="text-xs font-black uppercase text-primary tracking-widest">Berita & Artikel</p>
                    <h2 class="mt-4 text-3xl md:text-4xl font-black tracking-tight text-zinc-900 dark:text-white">
                        Terbaru dari {branch.name}
                    </h2>
                </div>
                <a href="/berita" class="inline-flex items-center justify-center rounded-2xl border border-zinc-200 bg-white px-6 py-3 text-sm font-black uppercase text-zinc-900 shadow-sm hover:border-primary hover:text-primary transition-all dark:border-white/10 dark:bg-zinc-900 dark:text-white">
                    Lihat Semua Berita
                </a>
            </div>
            {#if posts.length > 0}
                <div class="grid gap-8 md:grid-cols-3">
                    {#each posts as post}
                        <article class="group overflow-hidden rounded-[36px] border border-zinc-200 bg-white shadow-sm hover:-translate-y-1 hover:shadow-xl dark:border-white/5 dark:bg-zinc-950">
                            <div class="relative aspect-video overflow-hidden bg-zinc-100 text-zinc-500">
                                {#if post.featured_image}
                                    <img src={(post.featured_image.startsWith('http') ? post.featured_image : `/storage/${post.featured_image}`)} alt={post.title} class="h-full w-full object-cover transition duration-700 group-hover:scale-105">
                                {:else}
                                    <div class="flex h-full items-center justify-center text-6xl">📰</div>
                                {/if}
                                <div class="absolute inset-0 bg-gradient-to-t from-zinc-950/80 via-transparent to-transparent"></div>
                            </div>
                            <div class="p-8 space-y-4">
                                <div class="space-y-2">
                                    <p class="text-xs font-black uppercase text-zinc-500 dark:text-zinc-400">{post.category?.name || 'Update'}</p>
                                    <p class="text-[11px] uppercase text-zinc-400 dark:text-zinc-500">{new Date(post.published_at || post.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })}</p>
                                </div>
                                <h3 class="text-xl font-black leading-tight tracking-tight text-zinc-900 dark:text-white line-clamp-2">
                                    <a href="/berita/{post.slug}">{post.title}</a>
                                </h3>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400 line-clamp-2">{post.short_description || post.excerpt || ''}</p>
                                <a href="/berita/{post.slug}" class="inline-flex items-center gap-2 text-sm font-semibold text-primary hover:underline transition">
                                    Baca Selengkapnya
                                    <ArrowRight class="h-4 w-4" />
                                </a>
                            </div>
                        </article>
                    {/each}
                </div>
            {:else}
                <div class="text-center py-20 bg-white dark:bg-zinc-950 rounded-[40px] border border-zinc-200 dark:border-white/5">
                    <p class="text-zinc-400 font-bold">Belum ada berita di cabang ini.</p>
                </div>
            {/if}
        </div>
    </section>

    <!-- Contact Card -->
    <section class="py-24 bg-white dark:bg-zinc-950">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="rounded-[40px] bg-gradient-to-br from-primary/5 to-primary/10 dark:from-primary/10 dark:to-primary/20 border border-primary/10 p-10 md:p-16">
                <div class="max-w-3xl space-y-8">
                    <div>
                        <p class="text-xs font-black uppercase text-primary tracking-widest">Kontak</p>
                        <h2 class="mt-4 text-3xl md:text-4xl font-black tracking-tight text-zinc-900 dark:text-white">
                            Hubungi {branch.name}
                        </h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {#if contact.address}
                            <div class="flex items-start gap-4 p-6 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-100 dark:border-white/5">
                                <MapPin class="size-6 text-primary shrink-0 mt-0.5" />
                                <div>
                                    <p class="font-bold text-sm text-zinc-900 dark:text-white">Alamat</p>
                                    <p class="text-zinc-500 dark:text-zinc-400 text-sm mt-1">{contact.address}</p>
                                </div>
                            </div>
                        {/if}
                        {#if contact.phone}
                            <div class="flex items-start gap-4 p-6 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-100 dark:border-white/5">
                                <Phone class="size-6 text-primary shrink-0 mt-0.5" />
                                <div>
                                    <p class="font-bold text-sm text-zinc-900 dark:text-white">Telepon</p>
                                    <p class="text-zinc-500 dark:text-zinc-400 text-sm mt-1">{contact.phone}</p>
                                </div>
                            </div>
                        {/if}
                        {#if contact.email}
                            <div class="flex items-start gap-4 p-6 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-100 dark:border-white/5">
                                <Mail class="size-6 text-primary shrink-0 mt-0.5" />
                                <div>
                                    <p class="font-bold text-sm text-zinc-900 dark:text-white">Email</p>
                                    <p class="text-zinc-500 dark:text-zinc-400 text-sm mt-1">{contact.email}</p>
                                </div>
                            </div>
                        {/if}
                    </div>
                </div>
            </div>
        </div>
    </section>
</Layout>
