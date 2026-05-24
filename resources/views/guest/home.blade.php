<x-layouts.guest title="Beranda">
    <!-- Hero Section -->
    @include('partials.guest.hero')

    <!-- Feature Shortcuts -->
    @include('partials.guest.shortcuts')

    <!-- Stats Counter -->
    @include('partials.guest.stats-counter')

    <!-- Featured Campaigns -->
    <section class="py-24 bg-white dark:bg-zinc-950 overflow-hidden">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-8 mb-16">
                <div class="space-y-4">
                    <h2 class="text-primary font-black uppercase tracking-[0.3em] text-xs">Program Pilihan</h2>
                    <h3 class="text-3xl md:text-5xl font-black text-zinc-900 dark:text-white tracking-tight">Kebaikan yang Bisa<br>Anda Bantu Hari Ini</h3>
                </div>
                <flux:button href="{{ guest_route('guest.campaigns.index') }}" variant="ghost"  icon-trailing="arrow-right" class="font-bold border-zinc-200 dark:border-white/10 rounded-2xl">
                    Lihat Semua Program
                </flux:button>
            </div>

            <div class="space-y-6">
                @forelse($featuredCampaigns as $campaign)
                    @include('partials.guest.campaign-card', ['campaign' => $campaign])
                @empty
                    <div class="py-20 text-center border-2 border-dashed border-zinc-100 dark:border-zinc-800 rounded-3xl">
                        <p class="text-zinc-500 font-medium italic">Belum ada program unggulan saat ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Zakat CTA -->
    @include('partials.guest.cta-calculator')

    <!-- Pilar Program -->
    @include('partials.guest.program-pilar')

    <!-- News/Blog Section Placeholder -->
    <section class="py-24 bg-zinc-50 dark:bg-zinc-900/50">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="text-center space-y-4 mb-16">
                <h2 class="text-primary font-black uppercase tracking-[0.3em] text-xs">Berita Terbaru</h2>
                <h3 class="text-3xl md:text-4xl font-black text-zinc-900 dark:text-white tracking-tight">Kabar {{ setting('site_name', 'Lazismu') }} {{ setting('site_tagline', 'Tamantirto') }}</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($latestPosts as $post)
                    <article class="flex flex-col bg-white dark:bg-zinc-900 rounded-3xl overflow-hidden border border-zinc-200 dark:border-white/5 shadow-sm hover:shadow-xl transition-all group">
                        <div class="relative aspect-video overflow-hidden">
                            <img src="{{ $post->featured_image ? asset('storage/' . $post->featured_image) : 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?q=80&w=2070&auto=format&fit=crop' }}" class="size-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute top-4 left-4">
                                <span class="px-3 py-1 rounded-full bg-primary/90 text-white text-[10px] font-black uppercase tracking-wider backdrop-blur-sm">
                                    {{ $post->category->name ?? 'Update' }}
                                </span>
                            </div>
                        </div>
                        <div class="p-8 space-y-4">
                            <span class="text-xs text-zinc-500 font-medium">{{ $post->published_at?->format('d M Y') }}</span>
                            <h4 class="text-xl font-black text-zinc-900 dark:text-white line-clamp-2 leading-tight group-hover:text-primary transition-colors">
                                <a href="{{ guest_route('guest.posts.show', ['slug' => $post->slug]) }}">{{ $post->title }}</a>
                            </h4>
                            <p class="text-sm text-zinc-500 line-clamp-2 leading-relaxed">
                                {{ $post->short_description }}
                            </p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
</x-layouts.guest>
