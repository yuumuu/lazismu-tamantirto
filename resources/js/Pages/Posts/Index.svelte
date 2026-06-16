<script>
    import { Link } from "@inertiajs/svelte";
    import Layout from "../../Layouts/GuestLayout.svelte";
    import Calendar from "lucide-svelte/icons/calendar";
    import Clock from "lucide-svelte/icons/clock";
    import FileText from "lucide-svelte/icons/file-text";
    import Search from "lucide-svelte/icons/search";

    let { posts = { data: [], meta: {} }, categories = [], filters = {} } = $props();

    let selectedCategory = $state('all');
    let searchQuery = $state('');

    const filteredPosts = $derived(
        (posts.data || []).filter(post => {
            if (selectedCategory !== 'all' && post.category_id != selectedCategory) return false;
            if (searchQuery) {
                const q = searchQuery.toLowerCase();
                return post.title?.toLowerCase().includes(q) || post.excerpt?.toLowerCase().includes(q);
            }
            return true;
        })
    );

    function formatDate(dateStr) {
        if (!dateStr) return "";
        return new Date(dateStr).toLocaleDateString("id-ID", {
            day: "numeric",
            month: "short",
            year: "numeric",
        });
    }

    const fallbackImg = 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?q=80&w=2070&auto=format&fit=crop';
</script>

<svelte:head><title>Berita & Artikel | Lazismu</title></svelte:head>

<Layout>
    <!-- Page Header -->
    <header class="py-20 bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-white/5">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="max-w-3xl space-y-6">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-1.5 bg-primary rounded-full"></div>
                    <h2 class="text-primary font-black uppercase text-xs">Pusat Informasi</h2>
                </div>
                <h1 class="text-4xl md:text-6xl font-black text-zinc-900 dark:text-white tracking-tight leading-tight">
                    Berita & Kabar Terbaru
                </h1>
                <p class="text-lg text-zinc-500 font-medium">
                    Ikuti perkembangan program, penyaluran amanah, dan kegiatan sosial kami di tengah masyarakat.
                </p>
            </div>
        </div>
    </header>

    <section class="py-12 bg-white dark:bg-zinc-950 min-h-screen">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <!-- Toolbar -->
            <div class="flex flex-col lg:flex-row gap-6 items-center justify-between mb-16">
                <!-- Category Filters -->
                <div class="flex items-center gap-2 overflow-x-auto pb-2 lg:pb-0 w-full lg:w-auto no-scrollbar">
                    <button onclick={() => selectedCategory = 'all'}
                        class="px-6 py-3 rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-primary/90 hover:text-white transition-all whitespace-nowrap
                        {selectedCategory === 'all' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'bg-zinc-50 dark:bg-white/5 text-zinc-500 border border-zinc-100 dark:border-white/5'}"
                    >
                        Semua
                    </button>
                    {#each categories as cat}
                        <button onclick={() => selectedCategory = cat.id}
                            class="px-6 py-3 rounded-2xl font-bold uppercase tracking-widest text-[10px] hover:bg-primary/90 hover:text-white transition-all whitespace-nowrap
                            {selectedCategory === cat.id ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'bg-zinc-50 dark:bg-white/5 text-zinc-500 border border-zinc-100 dark:border-white/5'}"
                        >
                            {cat.name}
                        </button>
                    {/each}
                </div>

                <!-- Search -->
                <div class="w-full lg:w-1/3">
                    <div class="relative">
                        <Search class="absolute left-4 top-1/2 -translate-y-1/2 size-5 text-zinc-400" />
                        <input type="text" bind:value={searchQuery} placeholder="Cari berita..."
                            class="w-full pl-12 pr-4 py-3 rounded-2xl border border-zinc-200 dark:border-white/10 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-white placeholder:text-zinc-400 focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary text-sm font-medium"
                        />
                    </div>
                </div>
            </div>

            <!-- Posts Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
                {#if filteredPosts.length === 0}
                    <div class="col-span-full text-center py-20">
                        <div class="inline-flex items-center justify-center size-20 rounded-full bg-zinc-100 dark:bg-white/5 mb-6">
                            <FileText class="size-10 text-zinc-400" />
                        </div>
                        <h3 class="text-2xl font-black text-zinc-900 dark:text-white mb-2">Tidak Ada Berita</h3>
                        <p class="text-zinc-500">Tidak ada berita yang sesuai dengan pencarian Anda.</p>
                    </div>
                {:else}
                    {#each filteredPosts as post}
                        <article class="flex flex-col bg-white dark:bg-zinc-900 rounded-[32px] overflow-hidden border border-zinc-200 dark:border-white/5 shadow-sm hover:shadow-xl transition-all group">
                            <div class="relative aspect-video overflow-hidden">
                                <img
                                    src={post.featured_image_url || fallbackImg}
                                    alt={post.title}
                                    class="size-full object-cover group-hover:scale-110 transition-transform duration-700"
                                />
                                <div class="absolute top-6 left-6">
                                    <span class="px-4 py-1.5 rounded-full bg-primary/90 text-white text-[10px] font-black uppercase tracking-widest backdrop-blur-md shadow-lg">
                                        {post.category?.name || 'Update'}
                                    </span>
                                </div>
                            </div>
                            <div class="p-8 md:p-10 flex-1 flex flex-col justify-between space-y-6">
                                <div class="space-y-4">
                                    <div class="flex items-center gap-4 text-xs font-bold text-zinc-400">
                                        <span class="flex items-center gap-1.5">
                                            <Calendar class="size-3.5" />
                                            <span>{formatDate(post.published_at)}</span>
                                        </span>
                                        <span class="flex items-center gap-1.5">
                                            <Clock class="size-3.5" />
                                            <span>{post.reading_time || 1}</span> Menit Baca
                                        </span>
                                    </div>
                                    <h4 class="text-2xl font-black text-zinc-900 dark:text-white leading-tight line-clamp-2 group-hover:text-primary transition-colors">
                                        <a href={`/berita/${post.slug}`}>{post.title}</a>
                                    </h4>
                                    <p class="text-sm text-zinc-500 line-clamp-3 font-medium">{post.excerpt}</p>
                                </div>
                                <div class="pt-6 border-t border-zinc-100 dark:border-white/5">
                                    <a href={`/berita/${post.slug}`}
                                        class="inline-flex items-center gap-2 text-xs font-black text-primary uppercase group/link"
                                    >
                                        Baca Selengkapnya
                                        <span class="inline-block group-hover/link:translate-x-1 transition-transform">→</span>
                                    </a>
                                </div>
                            </div>
                        </article>
                    {/each}
                {/if}
            </div>

            <!-- Pagination Note -->
            <div class="mt-20 text-center">
                <p class="text-sm text-zinc-400 italic">Menampilkan {filteredPosts.length} dari {posts.total || posts.data?.length || 0} berita</p>
            </div>
        </div>
    </section>
</Layout>

