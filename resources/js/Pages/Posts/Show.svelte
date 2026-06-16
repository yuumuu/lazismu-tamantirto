<script>
    import { Link } from '@inertiajs/svelte';
    import Layout from '../../Layouts/GuestLayout.svelte';
    import Calendar from 'lucide-svelte/icons/calendar';
    import Clock from 'lucide-svelte/icons/clock';
    import Facebook from 'lucide-svelte/icons/facebook';
    import Phone from 'lucide-svelte/icons/phone';
    import LinkIcon from 'lucide-svelte/icons/link';
    import ArrowRight from 'lucide-svelte/icons/arrow-right';
    import Eye from 'lucide-svelte/icons/eye';

    let { post = null, relatedPosts = [], categories = [] } = $props();

    const fallbackImg = 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?q=80&w=2070&auto=format&fit=crop';
    const fallbackAvatar = 'https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?q=80&w=1974&auto=format&fit=crop';

    function formatDate(dateStr) {
        if (!dateStr) return '';
        return new Date(dateStr).toLocaleDateString('id-ID', {
            day: 'numeric', month: 'short', year: 'numeric',
        });
    }

    let copied = $state(false);
    let currentUrl = $state('');

    function copyUrl() {
        navigator.clipboard.writeText(currentUrl);
        copied = true;
        setTimeout(() => copied = false, 2000);
    }

    function shareToFacebook() {
        currentUrl = window.location.href;
        window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(currentUrl), '_blank');
    }

    function shareToWhatsApp() {
        currentUrl = window.location.href;
        window.open('https://wa.me/?text=' + encodeURIComponent('Baca berita menarik ini: ' + currentUrl), '_blank');
    }
</script>

<svelte:head><title>{post?.title || 'Berita'} | Lazismu</title></svelte:head>

<Layout>
    {#if post}
        <article class="bg-white dark:bg-zinc-950 min-h-screen pb-24">
            <!-- Hero Header -->
            <header class="relative pt-24 pb-32 overflow-hidden bg-zinc-900">
                <img
                    src={post.featured_image_url || fallbackImg}
                    alt=""
                    class="absolute inset-0 size-full object-cover opacity-40 blur-sm scale-105"
                />
                <div class="absolute inset-0 bg-gradient-to-b from-zinc-900/60 via-zinc-900/90 to-zinc-900"></div>

                <div class="relative mx-auto max-w-4xl px-6 text-center space-y-8">
                    <div class="flex flex-col items-center gap-4">
                        <span class="px-4 py-1.5 rounded-full bg-primary/20 border border-primary/30 text-primary text-[10px] font-black uppercase backdrop-blur-md">
                            {post.category?.name || 'Update'}
                        </span>
                        <h1 class="text-4xl md:text-6xl font-black text-white tracking-tight leading-[1.1]">
                            {post.title}
                        </h1>
                    </div>

                    <div class="flex flex-wrap items-center justify-center gap-8 text-xs font-bold text-zinc-400">
                        <div class="flex items-center gap-3">
                            <img
                                src={post.author?.profile_photo_url || fallbackAvatar}
                                alt={post.author?.name || 'Admin'}
                                class="size-8 rounded-full border border-white/20"
                            />
                            <span class="text-zinc-300">{post.author?.name || 'Admin Lazismu'}</span>
                        </div>
                        <div class="h-4 w-px bg-white/10 hidden md:block"></div>
                        <span class="flex items-center gap-2">
                            <Calendar class="size-3.5 text-primary" />
                            {formatDate(post.published_at)}
                        </span>
                        <div class="h-4 w-px bg-white/10 hidden md:block"></div>
                        <span class="flex items-center gap-2">
                            <Clock class="size-3.5 text-primary" />
                            {post.reading_time || 1} Menit Baca
                        </span>
                        <div class="h-4 w-px bg-white/10 hidden md:block"></div>
                        <span class="flex items-center gap-2">
                            <Eye class="size-3.5 text-primary" />
                            {post.view_count || 0} Dilihat
                        </span>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="mx-auto max-w-7xl px-6 lg:px-8 -mt-16 relative z-20">
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-12">
                    <!-- Main Article -->
                    <div class="lg:col-span-3 space-y-12">
                        <div class="bg-white dark:bg-zinc-900 rounded-[40px] border border-zinc-200 dark:border-white/5 shadow-2xl p-8 md:p-16">
                            <div class="prose prose-zinc dark:prose-invert max-w-none prose-p:leading-relaxed prose-p:text-zinc-600 dark:prose-p:text-zinc-400 prose-p:text-lg prose-headings:font-black prose-headings:tracking-tight prose-strong:text-zinc-900 dark:prose-strong:text-white prose-img:rounded-2xl prose-img:shadow-xl prose-a:text-primary prose-a:font-black">
                                {@html post.content}
                            </div>
                        </div>

                        <!-- Share Section -->
                        <div class="p-8 rounded-[32px] bg-zinc-50 dark:bg-white/5 border border-zinc-100 dark:border-white/5 flex flex-col md:flex-row items-center justify-between gap-6">
                            <h4 class="text-lg font-black text-zinc-900 dark:text-white tracking-tight">Bagikan Artikel Ini:</h4>
                            <div class="flex items-center gap-4">
                                <button onclick={shareToFacebook}
                                    class="size-12 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-white/10 flex items-center justify-center text-[#1877F2] hover:bg-[#1877F2] hover:text-white transition-all cursor-pointer"
                                >
                                    <Facebook class="size-5" />
                                </button>
                                <button onclick={shareToWhatsApp}
                                    class="size-12 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-white/10 flex items-center justify-center text-[#25D366] hover:bg-[#25D366] hover:text-white transition-all cursor-pointer"
                                >
                                    <Phone class="size-5" />
                                </button>
                                <div class="relative">
                                    <button onclick={copyUrl}
                                        class="size-12 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-white/10 flex items-center justify-center text-zinc-900 dark:text-white hover:bg-zinc-900 hover:text-white transition-all cursor-pointer"
                                    >
                                        <LinkIcon class="size-5" />
                                    </button>
                                    {#if copied}
                                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-1 bg-zinc-900 text-white text-[10px] font-black uppercase tracking-widest rounded-lg shadow-xl animate-bounce">
                                            Copied!
                                        </div>
                                    {/if}
                                </div>
                            </div>
                        </div>

                        <!-- Related Posts -->
                        {#if relatedPosts.length > 0}
                            <div class="space-y-8">
                                <div class="flex items-center gap-4">
                                    <div class="h-8 w-1.5 bg-primary rounded-full"></div>
                                    <h3 class="text-2xl font-black text-zinc-900 dark:text-white tracking-tight">Artikel Terkait</h3>
                                </div>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                    {#each relatedPosts as related}
                                        <a href={`/berita/${related.slug}`}
                                            class="group flex gap-6 p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-100 dark:border-white/5 hover:shadow-xl transition-all"
                                        >
                                            <img
                                                src={related.featured_image_url || fallbackImg}
                                                alt={related.title}
                                                class="size-24 rounded-2xl object-cover group-hover:scale-105 transition-transform"
                                            />
                                            <div class="flex-1 space-y-2 py-1">
                                                <span class="text-[10px] text-primary font-black uppercase tracking-widest">{related.category?.name || 'Update'}</span>
                                                <h4 class="font-black text-zinc-900 dark:text-white leading-tight line-clamp-2 group-hover:text-primary transition-colors">{related.title}</h4>
                                            </div>
                                        </a>
                                    {/each}
                                </div>
                            </div>
                        {/if}
                    </div>

                    <!-- Sidebar -->
                    <aside class="space-y-8">
                        <!-- CTA -->
                        <div class="bg-primary rounded-[32px] p-8 text-white shadow-xl shadow-primary/30 space-y-6 relative overflow-hidden group">
                            <div class="absolute -top-12 -right-12 size-32 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-1000"></div>
                            <div class="space-y-2 relative z-10">
                                <h4 class="font-black uppercase tracking-widest text-xs">Sedekah Jariyah</h4>
                                <p class="text-lg font-black leading-tight">Bantu Saudara Kita Melalui Program Ini</p>
                            </div>
                            <a href="/program"
                                class="block w-full text-center py-4 rounded-2xl bg-white text-primary font-black uppercase tracking-widest shadow-lg hover:bg-zinc-100 transition-all relative z-10"
                            >
                                Donasi Sekarang
                            </a>
                        </div>

                        <!-- Categories -->
                        {#if categories.length > 0}
                            <div class="bg-white dark:bg-zinc-900 rounded-[32px] p-8 border border-zinc-200 dark:border-white/5 shadow-sm space-y-6">
                                <h4 class="text-lg font-black text-zinc-900 dark:text-white tracking-tight">Kategori</h4>
                                <div class="space-y-2">
                                    {#each categories as cat}
                                        <a href="#"
                                            class="flex items-center justify-between p-4 rounded-xl hover:bg-zinc-50 dark:hover:bg-white/5 transition-all group"
                                        >
                                            <span class="font-bold text-zinc-600 dark:text-zinc-400 group-hover:text-primary transition-colors">{cat.name}</span>
                                            <span class="bg-zinc-100 dark:bg-white/10 px-2.5 py-1 rounded-md text-[10px] font-black text-zinc-500">{cat.posts_count}</span>
                                        </a>
                                    {/each}
                                </div>
                            </div>
                        {/if}
                    </aside>
                </div>
            </div>
        </article>
    {/if}
</Layout>

