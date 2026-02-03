<x-layouts.guest title="Berita & Artikel">
    <!-- Page Header -->
    <header class="py-20 bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-white/5">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="max-w-3xl space-y-6">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-1.5 bg-primary rounded-full"></div>
                    <h2 class="text-primary font-black uppercase tracking-[0.3em] text-xs">Pusat Informasi</h2>
                </div>
                <h1 class="text-4xl md:text-6xl font-black text-zinc-900 dark:text-white tracking-tight leading-tight">
                    Berita & Kabar Terbaru Lazismu Tamantirto
                </h1>
                <p class="text-lg text-zinc-500 font-medium leading-relaxed">
                    Ikuti perkembangan program, penyaluran amanah, dan kegiatan sosial kami di tengah masyarakat.
                </p>
            </div>
        </div>
    </header>

    <section class="py-12 bg-white dark:bg-zinc-950 min-h-screen">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <!-- Toolbar -->
            <div class="flex flex-col lg:flex-row gap-6 items-center justify-between mb-16">
                <div class="flex items-center gap-2 overflow-x-auto pb-2 lg:pb-0 w-full lg:w-auto no-scrollbar">
                    <button class="px-6 py-3 rounded-2xl bg-primary text-white font-black uppercase tracking-widest text-[10px] shadow-lg shadow-primary/20">Semua</button>
                    @foreach(\App\Models\BlogCategory::all() as $cat)
                        <button class="px-6 py-3 rounded-2xl bg-zinc-50 dark:bg-white/5 text-zinc-500 font-bold uppercase tracking-widest text-[10px] hover:bg-zinc-100 transition-all border border-zinc-100 dark:border-white/5 whitespace-nowrap">{{ $cat->name }}</button>
                    @endforeach
                </div>
                
                <div class="w-full lg:w-1/3">
                    <flux:input placeholder="Cari berita..." icon="magnifying-glass" class="rounded-2xl" />
                </div>
            </div>

            <!-- Posts Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
                @php
                    $posts = \App\Models\BlogPost::published()
                        ->latest()
                        ->paginate(9);
                @endphp

                @foreach($posts as $post)
                    <article class="flex flex-col bg-white dark:bg-zinc-900 rounded-[32px] overflow-hidden border border-zinc-200 dark:border-white/5 shadow-sm hover:shadow-xl transition-all group">
                        <div class="relative aspect-video overflow-hidden">
                            <img src="{{ $post->featured_image ? asset('storage/' . $post->featured_image) : 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?q=80&w=2070&auto=format&fit=crop' }}" class="size-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute top-6 left-6">
                                <span class="px-4 py-1.5 rounded-full bg-primary/90 text-white text-[10px] font-black uppercase tracking-widest backdrop-blur-md shadow-lg">
                                    {{ $post->category->name ?? 'Update' }}
                                </span>
                            </div>
                        </div>
                        <div class="p-8 md:p-10 flex-1 flex flex-col justify-between space-y-6">
                            <div class="space-y-4">
                                <div class="flex items-center gap-4 text-xs font-bold text-zinc-400">
                                    <span class="flex items-center gap-1.5">
                                        <flux:icon.calendar class="size-3.5" />
                                        {{ $post->published_at?->format('d M Y') }}
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <flux:icon.clock class="size-3.5" />
                                        {{ $post->reading_time }} Menit Baca
                                    </span>
                                </div>
                                <h4 class="text-2xl font-black text-zinc-900 dark:text-white leading-tight line-clamp-2 group-hover:text-primary transition-colors">
                                    <a href="{{ route('guest.posts.show', $post->slug) }}">{{ $post->title }}</a>
                                </h4>
                                <p class="text-sm text-zinc-500 line-clamp-3 leading-relaxed font-medium">
                                    {{ $post->excerpt }}
                                </p>
                            </div>
                            
                            <div class="pt-6 border-t border-zinc-100 dark:border-white/5">
                                <a href="{{ route('guest.posts.show', $post->slug) }}" class="inline-flex items-center gap-2 text-xs font-black text-primary uppercase tracking-[0.2em] group/link">
                                    Baca Selengkapnya
                                    <flux:icon.arrow-right class="size-4 group-hover/link:translate-x-1 transition-transform" />
                                </a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-20 flex justify-center">
                {{ $posts->links() }}
            </div>
        </div>
    </section>
</x-layouts.guest>
