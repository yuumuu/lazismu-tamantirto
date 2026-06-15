<x-layouts.guest title="Berita & Artikel">
    <!-- Page Header -->
    <header class="py-20 bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-white/5">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="max-w-3xl space-y-6">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-1.5 bg-primary rounded-full"></div>
                    <h2 class="text-primary font-black uppercase text-xs">Pusat Informasi</h2>
                </div>
                <h1 class="text-4xl md:text-6xl font-black text-zinc-900 dark:text-white tracking-tight leading-tight">
                    Berita & Kabar Terbaru {{ setting('site_name', 'Lazismu') }}
                    {{ setting('site_tagline', 'Tamantirto') }}
                </h1>
                <p class="text-lg text-zinc-500 font-medium">
                    Ikuti perkembangan program, penyaluran amanah, dan kegiatan sosial kami di tengah masyarakat.
                </p>
            </div>
        </div>
    </header>

    <section class="py-12 bg-white dark:bg-zinc-950 min-h-screen" x-data="{
        selectedCategory: 'all',
        searchQuery: '',
        posts: @js($posts),
        categories: @js($categories),
        masjidSlug: @js(request()->route('masjid_slug')),
    
        getPrefix() {
            return this.masjidSlug ? '/' + this.masjidSlug : '';
        },
    
        get filteredPosts() {
            let filtered = this.posts;
    
            // Filter by category
            if (this.selectedCategory !== 'all') {
                filtered = filtered.filter(post => post.category_id == this.selectedCategory);
            }
    
            // Filter by search
            if (this.searchQuery) {
                const query = this.searchQuery.toLowerCase();
                filtered = filtered.filter(post =>
                    post.title.toLowerCase().includes(query) ||
                    (post.excerpt && post.excerpt.toLowerCase().includes(query))
                );
            }
    
            return filtered;
        }
    }">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <!-- Toolbar -->
            <div class="flex flex-col lg:flex-row gap-6 items-center justify-between mb-16">
                <div class="flex items-center gap-2 overflow-x-auto pb-2 lg:pb-0 w-full lg:w-auto no-scrollbar">
                    <button @click="selectedCategory = 'all'"
                        :class="selectedCategory === 'all' ? 'bg-primary text-white shadow-lg shadow-primary/20' :
                            'bg-zinc-50 dark:bg-white/5 text-zinc-500 border border-zinc-100 dark:border-white/5'"
                        class="px-6 py-3 rounded-2xl font-black uppercase tracking-widest text-[10px] hover:bg-primary/90 hover:text-white transition-all whitespace-nowrap">
                        Semua
                    </button>
                    <template x-for="category in categories" :key="category.id">
                        <button @click="selectedCategory = category.id"
                            :class="selectedCategory === category.id ? 'bg-primary text-white shadow-lg shadow-primary/20' :
                                'bg-zinc-50 dark:bg-white/5 text-zinc-500 border border-zinc-100 dark:border-white/5'"
                            class="px-6 py-3 rounded-2xl font-bold uppercase tracking-widest text-[10px] hover:bg-primary/90 hover:text-white transition-all whitespace-nowrap"
                            x-text="category.name">
                        </button>
                    </template>
                </div>

                <div class="w-full lg:w-1/3">
                    <flux:input x-model="searchQuery" placeholder="Cari berita..." icon="magnifying-glass"
                        class="rounded-2xl" />
                </div>
            </div>

            <!-- Posts Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
                <!-- Empty State -->
                <div x-show="filteredPosts.length === 0" class="col-span-full text-center py-20">
                    <div
                        class="inline-flex items-center justify-center size-20 rounded-full bg-zinc-100 dark:bg-white/5 mb-6">
                        <flux:icon.document-text class="size-10 text-zinc-400" />
                    </div>
                    <h3 class="text-2xl font-black text-zinc-900 dark:text-white mb-2">Tidak Ada Berita</h3>
                    <p class="text-zinc-500">Tidak ada berita yang sesuai dengan pencarian Anda.</p>
                </div>

                <!-- Posts Loop -->
                <template x-for="post in filteredPosts" :key="post.id">
                    <div>
                        <article
                            class="flex flex-col bg-white dark:bg-zinc-900 rounded-[32px] overflow-hidden border border-zinc-200 dark:border-white/5 shadow-sm hover:shadow-xl transition-all group">
                            <div class="relative aspect-video overflow-hidden">
                                <img :src="post.featured_image ? '/storage/' + post.featured_image :
                                    'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?q=80&w=2070&auto=format&fit=crop'"
                                    class="size-full object-cover group-hover:scale-110 transition-transform duration-700">
                                <div class="absolute top-6 left-6">
                                    <span
                                        class="px-4 py-1.5 rounded-full bg-primary/90 text-white text-[10px] font-black uppercase tracking-widest backdrop-blur-md shadow-lg"
                                        x-text="post.category?.name || 'Update'">
                                    </span>
                                </div>
                            </div>
                            <div class="p-8 md:p-10 flex-1 flex flex-col justify-between space-y-6">
                                <div class="space-y-4">
                                    <div class="flex items-center gap-4 text-xs font-bold text-zinc-400">
                                        <span class="flex items-center gap-1.5">
                                            <flux:icon.calendar class="size-3.5" />
                                            <span
                                                x-text="new Date(post.published_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })"></span>
                                        </span>
                                        <span class="flex items-center gap-1.5">
                                            <flux:icon.clock class="size-3.5" />
                                            <span x-text="post.reading_time"></span> Menit Baca
                                        </span>
                                    </div>
                                    <h4
                                        class="text-2xl font-black text-zinc-900 dark:text-white leading-tight line-clamp-2 group-hover:text-primary transition-colors">
                                        <a :href="getPrefix() + '/berita/' + post.slug" x-text="post.title"></a>
                                    </h4>
                                    <p class="text-sm text-zinc-500 line-clamp-3 font-medium" x-text="post.excerpt">
                                    </p>
                                </div>

                                <div class="pt-6 border-t border-zinc-100 dark:border-white/5">
                                    <a :href="getPrefix() + '/berita/' + post.slug"
                                        class="inline-flex items-center gap-2 text-xs font-black text-primary uppercase group/link">
                                        Baca Selengkapnya
                                        <flux:icon.arrow-right
                                            class="size-4 group-hover/link:translate-x-1 transition-transform" />
                                    </a>
                                </div>
                            </div>
                        </article>
                    </div>
                </template>
            </div>

            <!-- Pagination Note -->
            <div class="mt-20 text-center">
                <p class="text-sm text-zinc-400 italic">Menampilkan <span x-text="filteredPosts.length"></span> dari
                    {{ $posts->count() }} berita</p>
            </div>
        </div>
    </section>
</x-layouts.guest>
