<x-layouts.guest>
    <article class="bg-white dark:bg-zinc-950 min-h-screen pb-24">
        <!-- Hero Header -->
        <header class="relative pt-24 pb-32 overflow-hidden bg-zinc-900">
            <img src="{{ $post->featured_image ? asset('storage/' . $post->featured_image) : 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?q=80&w=2070&auto=format&fit=crop' }}" class="absolute inset-0 size-full object-cover opacity-40 blur-sm scale-105">
            <div class="absolute inset-0 bg-gradient-to-b from-zinc-900/60 via-zinc-900/90 to-zinc-900"></div>
            
            <div class="relative mx-auto max-w-4xl px-6 text-center space-y-8">
                <div class="flex flex-col items-center gap-4">
                    <span class="px-4 py-1.5 rounded-full bg-primary/20 border border-primary/30 text-primary text-[10px] font-black uppercase tracking-[0.3em] backdrop-blur-md">
                        {{ $post->category->name ?? 'Update' }}
                    </span>
                    <h1 class="text-4xl md:text-6xl font-black text-white tracking-tight leading-[1.1]">
                        {{ $post->title }}
                    </h1>
                </div>

                <div class="flex flex-wrap items-center justify-center gap-8 text-xs font-bold text-zinc-400">
                    <div class="flex items-center gap-3">
                        <img src="{{ $post->author?->profile_photo_url ?? 'https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?q=80&w=1974&auto=format&fit=crop' }}" class="size-8 rounded-full border border-white/20">
                        <span class="text-zinc-300">{{ $post->author->name ?? 'Admin Lazismu' }}</span>
                    </div>
                    <div class="h-4 w-px bg-white/10 hidden md:block"></div>
                    <span class="flex items-center gap-2">
                        <flux:icon.calendar class="size-3.5 text-primary" />
                        {{ $post->published_at?->format('d M Y') }}
                    </span>
                    <div class="h-4 w-px bg-white/10 hidden md:block"></div>
                    <span class="flex items-center gap-2">
                        <flux:icon.clock class="size-3.5 text-primary" />
                        {{ $post->reading_time }} Menit Baca
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
                        <div class="prose prose-zinc dark:prose-invert max-w-none prose-p:leading-relaxed prose-p:text-zinc-600 dark:prose-p:text-zinc-400 prose-p:text-lg prose-headings:font-black prose-headings:tracking-tight prose-strong:text-zinc-900 dark:prose-strong:text-white prose-img:rounded-3xl prose-img:shadow-xl prose-a:text-primary prose-a:font-black">
                            {!! $post->content !!}
                        </div>
                    </div>

                    <!-- Share Section -->
                    <div class="p-8 rounded-[32px] bg-zinc-50 dark:bg-white/5 border border-zinc-100 dark:border-white/5 flex flex-col md:flex-row items-center justify-between gap-6">
                        <h4 class="text-lg font-black text-zinc-900 dark:text-white tracking-tight">Bagikan Artikel Ini:</h4>
                        <div class="flex items-center gap-4" x-data="{ 
                            copied: false,
                            copyUrl() {
                                navigator.clipboard.writeText(window.location.href);
                                this.copied = true;
                                setTimeout(() => this.copied = false, 2000);
                            }
                        }">
                            <button @click="window.open('https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(window.location.href), '_blank')" class="size-12 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-white/10 flex items-center justify-center text-[#1877F2] hover:bg-[#1877F2] hover:text-white transition-all">
                                <flux:icon.facebook class="size-5" />
                            </button>
                            <button @click="window.open('https://wa.me/?text=' + encodeURIComponent('Baca berita menarik ini: ' + window.location.href), '_blank')" class="size-12 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-white/10 flex items-center justify-center text-[#25D366] hover:bg-[#25D366] hover:text-white transition-all">
                                <flux:icon.phone class="size-5" />
                            </button>
                            <div class="relative">
                                <button @click="copyUrl()" class="size-12 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-white/10 flex items-center justify-center text-zinc-900 dark:text-white hover:bg-zinc-900 hover:text-white transition-all">
                                    <flux:icon.link class="size-5" />
                                </button>
                                <div x-show="copied" x-transition class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-1 bg-zinc-900 text-white text-[10px] font-black uppercase tracking-widest rounded-lg shadow-xl animate-bounce">
                                    Copied!
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Related Posts -->
                    <div class="space-y-8">
                        <div class="flex items-center gap-4">
                            <div class="h-8 w-1.5 bg-primary rounded-full"></div>
                            <h3 class="text-2xl font-black text-zinc-900 dark:text-white tracking-tight">Artikel Terkait</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            @php
                                $relatedPosts = \App\Models\BlogPost::published()
                                    ->where('id', '!=', $post->id)
                                    ->where('category_id', $post->category_id)
                                    ->take(2)
                                    ->get();
                            @endphp
                            @foreach($relatedPosts as $related)
                                <a href="{{ route('guest.posts.show', $related->slug) }}" class="group flex gap-6 p-4 rounded-3xl bg-white dark:bg-zinc-900 border border-zinc-100 dark:border-white/5 hover:shadow-xl transition-all">
                                    <img src="{{ $related->featured_image ? asset('storage/' . $related->featured_image) : 'https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?q=80&w=2070&auto=format&fit=crop' }}" class="size-24 rounded-2xl object-cover group-hover:scale-105 transition-transform">
                                    <div class="flex-1 space-y-2 py-1">
                                        <span class="text-[10px] text-primary font-black uppercase tracking-widest">{{ $related->category->name }}</span>
                                        <h4 class="font-black text-zinc-900 dark:text-white leading-tight line-clamp-2 group-hover:text-primary transition-colors">{{ $related->title }}</h4>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <aside class="space-y-8">
                    <!-- Featured Campaign CTA -->
                    <div class="bg-primary rounded-[32px] p-8 text-white shadow-xl shadow-primary/30 space-y-6 relative overflow-hidden group">
                        <div class="absolute -top-12 -right-12 size-32 bg-white/10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-1000"></div>
                        <div class="space-y-2 relative z-10">
                            <h4 class="font-black uppercase tracking-widest text-xs">Sedekah Jariyah</h4>
                            <p class="text-lg font-black leading-tight">Bantu Saudara Kita Melalui Program Ini</p>
                        </div>
                        <flux:button href="{{ route('guest.campaigns.index') }}" variant="primary" class="w-full bg-white !text-primary border-none font-black uppercase tracking-widest h-14 rounded-2xl shadow-lg relative z-10 hover:!bg-zinc-100">
                            Donasi Sekarang
                        </flux:button>
                    </div>

                    <!-- Categories -->
                    <div class="bg-white dark:bg-zinc-900 rounded-[32px] p-8 border border-zinc-200 dark:border-white/5 shadow-sm space-y-6">
                        <h4 class="text-lg font-black text-zinc-900 dark:text-white tracking-tight">Kategori</h4>
                        <div class="space-y-2">
                            @foreach(\App\Models\BlogCategory::withCount('posts')->take(5)->get() as $cat)
                                <a href="#" class="flex items-center justify-between p-4 rounded-xl hover:bg-zinc-50 dark:hover:bg-white/5 transition-all group">
                                    <span class="font-bold text-zinc-600 dark:text-zinc-400 group-hover:text-primary transition-colors">{{ $cat->name }}</span>
                                    <span class="bg-zinc-100 dark:bg-white/10 px-2.5 py-1 rounded-md text-[10px] font-black text-zinc-500">{{ $cat->posts_count }}</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </aside>

            </div>
        </div>
    </article>
</x-layouts.guest>
