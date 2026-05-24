@php
    // Use eager loaded count instead of separate query
    $donorsCount = $campaign->verified_donations_count;
@endphp

<x-layouts.guest>
    <!-- Background Header (Blur) -->
    <div class="relative h-[400px] md:h-[500px] overflow-hidden">
        <img src="{{ $campaign->featured_image ? asset('storage/' . $campaign->featured_image) : 'https://images.unsplash.com/photo-1532629345422-7515f3d16bb6?q=80&w=2070&auto=format&fit=crop' }}" class="size-full object-cover blur-2xl scale-110 opacity-30 dark:opacity-20">
        <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-white dark:from-zinc-950 to-transparent"></div>
    </div>

    <!-- Main Content -->
    <div class="mx-auto max-w-7xl px-6 lg:px-8 -mt-64 md:-mt-80 relative z-10 pb-24">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-12">

            <!-- Left Column: Details -->
            <div class="lg:col-span-2 space-y-12">
                <!-- Hero Card -->
                <div class="bg-white dark:bg-zinc-900 rounded-[48px] overflow-hidden border border-zinc-200 dark:border-white/5 shadow-2xl">
                    <div class="aspect-video relative overflow-hidden group">
                        <img src="{{ $campaign->featured_image ? asset('storage/' . $campaign->featured_image) : 'https://images.unsplash.com/photo-1532629345422-7515f3d16bb6?q=80&w=2070&auto=format&fit=crop' }}" class="size-full object-cover group-hover:scale-105 transition-transform duration-1000">
                        <div class="absolute top-8 left-8 flex flex-col gap-3">
                            @if($campaign->is_urgent)
                                <span class="px-4 py-2 rounded-full bg-red-500 text-white text-xs font-black uppercase tracking-widest shadow-xl">🚨 Darurat</span>
                            @endif
                            <span class="px-4 py-2 rounded-full bg-primary/90 text-white text-xs font-black uppercase tracking-widest shadow-xl backdrop-blur-md">
                                {{ $campaign->category->name ?? 'Program' }}
                            </span>
                        </div>
                    </div>

                    <div class="p-8 md:p-12 space-y-8">
                        <div class="space-y-4">
                            <h1 class="text-3xl md:text-5xl font-black text-zinc-900 dark:text-white leading-[1.1] tracking-tighter">
                                {{ $campaign->title }}
                            </h1>
                            <div class="flex flex-wrap items-center gap-6 text-sm font-bold text-zinc-500">
                                <span class="flex items-center gap-2">
                                    <flux:icon.calendar class="size-4 text-primary" />
                                    @if($campaign->end_date)
                                        Berakhir pada {{ $campaign->end_date->format('d M Y') }}
                                    @else
                                        Program Berkelanjutan
                                    @endif
                                </span>
                                <span class="flex items-center gap-2">
                                    <flux:icon.users class="size-4 text-primary" />
                                    {{ $donorsCount }} Donatur Bergabung
                                </span>
                            </div>
                        </div>

                        <!-- Progress Section -->
                        <div class="bg-zinc-50 dark:bg-white/5 p-8 rounded-[32px] border border-zinc-100 dark:border-white/5 space-y-6">
                            <div class="flex justify-between items-end">
                                <div class="space-y-1">
                                    <span class="text-xs text-zinc-400 font-black uppercase tracking-[0.2em]">Dana Terkumpul</span>
                                    <div class="flex items-baseline gap-2">
                                        <span class="text-3xl md:text-4xl font-black text-primary tracking-tighter">{{ format_rupiah($campaign->current_amount) }}</span>
                                        @if($campaign->target_amount > 0)
                                            <span class="text-sm font-bold text-zinc-500">dari {{ format_rupiah($campaign->target_amount) }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-3xl font-black text-primary tracking-tighter">{{ $campaign->progress_percentage }}%</span>
                                </div>
                            </div>

                            <div class="relative w-full h-4 bg-zinc-200 dark:bg-white/10 rounded-full overflow-hidden">
                                <div class="absolute inset-y-0 left-0 bg-primary rounded-full transition-all duration-1000" style="width: {{ $campaign->progress_percentage }}%">
                                    <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent animate-shimmer"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="space-y-8">
                            <div class="prose prose-zinc dark:prose-invert max-w-none prose-p:leading-relaxed prose-p:text-zinc-600 dark:prose-p:text-zinc-400 prose-headings:font-black prose-headings:tracking-tight prose-strong:text-zinc-900 dark:prose-strong:text-white">
                                {!! $campaign->description !!}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Donors List -->
                <div class="bg-white dark:bg-zinc-900 rounded-[32px] p-8 md:p-12 border border-zinc-200 dark:border-white/5 shadow-xl space-y-8">
                    <h3 class="text-2xl font-black text-zinc-900 dark:text-white tracking-tight">Donatur Dermawan ({{ $donorsCount }})</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @forelse($donors as $donation)
                            <div class="flex items-center gap-4 p-4 rounded-2xl bg-zinc-50 dark:bg-white/5 border border-zinc-100 dark:border-white/5">
                                <div class="size-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary font-black uppercase tracking-wider text-xs">
                                    {{ $donation->is_anonymous ? 'HA' : substr($donation->donor_name, 0, 2) }}
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-center mb-0.5">
                                        <span class="font-bold text-zinc-900 dark:text-white">{{ $donation->is_anonymous ? 'Hamba Allah' : $donation->donor_name }}</span>
                                        <span class="text-xs font-black text-primary">{{ format_rupiah($donation->amount) }}</span>
                                    </div>
                                    @if($donation->donor_message)
                                        <p class="text-[10px] text-zinc-500 font-medium line-clamp-1 italic">"{{ $donation->donor_message }}"</p>
                                    @endif
                                    <p class="text-[9px] text-zinc-400 font-bold uppercase tracking-widest mt-1">{{ $donation->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full py-12 text-center">
                                <p class="text-zinc-500 italic font-medium">Belum ada donasi terverifikasi untuk program ini.</p>
                            </div>
                        @endforelse
                    </div>
                    @if($donorsCount > 10)
                        <flux:button variant="ghost" class="w-full font-bold border-zinc-200 dark:border-white/10 rounded-2xl h-14">Lihat Semua Donatur</flux:button>
                    @endif
                </div>
            </div>

            <!-- Right Column: Sticky CTA -->
            <div class="space-y-8">
                <div class="sticky top-24 space-y-6">
                    <div class="bg-white dark:bg-zinc-900 rounded-[32px] p-8 border border-zinc-200 dark:border-white/5 shadow-2xl shadow-primary/10 space-y-8">
                        <div class="space-y-2">
                            <h4 class="text-sm font-black text-zinc-900 dark:text-white uppercase tracking-widest">Ayo Bantu Sekarang</h4>
                            <p class="text-xs text-zinc-500 font-medium italic leading-relaxed">Pahala jariyah yang tak terputus melalui sedekah terbaik Anda.</p>
                        </div>

                        <div class="space-y-4">
                            <flux:button href="{{ guest_route('guest.donate.form', $campaign->slug) }}" variant="primary"  class="w-full h-16 rounded-2xl font-black uppercase tracking-[0.2em] shadow-xl shadow-primary/30">
                                Donasi Sekarang
                            </flux:button>

                            <div class="grid grid-cols-2 gap-4" x-data="{
                                copied: false,
                                copyUrl() {
                                    navigator.clipboard.writeText(window.location.href);
                                    this.copied = true;
                                    setTimeout(() => this.copied = false, 2000);
                                }
                            }">
                                <button @click="copyUrl()" class="relative flex flex-col items-center justify-center gap-2 p-4 rounded-2xl bg-zinc-50 dark:bg-white/5 border border-zinc-100 dark:border-white/5 hover:bg-primary/5 hover:border-primary/20 transition-all group">
                                    <flux:icon.share class="size-6 text-zinc-400 group-hover:text-primary transition-colors" />
                                    <span class="text-[10px] font-black uppercase tracking-wider text-zinc-500 group-hover:text-primary transition-colors">Bagikan</span>
                                    <div x-show="copied" x-transition class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-1 bg-zinc-900 text-white text-[10px] font-black uppercase tracking-widest rounded-lg shadow-xl whitespace-nowrap">
                                        Copied!
                                    </div>
                                </button>
                                <button class="flex flex-col items-center justify-center gap-2 p-4 rounded-2xl bg-zinc-50 dark:bg-white/5 border border-zinc-100 dark:border-white/5 hover:bg-primary/5 hover:border-primary/20 transition-all group">
                                    <flux:icon.heart class="size-6 text-zinc-400 group-hover:text-primary transition-colors" />
                                    <span class="text-[10px] font-black uppercase tracking-wider text-zinc-500 group-hover:text-primary transition-colors">Doa Lukis</span>
                                </button>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-zinc-100 dark:border-white/5 flex items-center gap-4">
                            <img src="https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?q=80&w=1974&auto=format&fit=crop" class="size-10 rounded-full object-cover">
                            <div>
                                <p class="text-[10px] text-zinc-400 font-black uppercase tracking-widest leading-none mb-1">Dikelola Oleh</p>
                                <p class="text-sm font-bold text-zinc-900 dark:text-white underline decoration-primary underline-offset-4 decoration-2">Admin {{ setting('site_name', 'Lazismu') }} {{ setting('site_tagline', 'Tamantirto') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Call Center Widget -->
                    <div class="bg-primary rounded-3xl p-6 text-white shadow-xl shadow-primary/20 flex items-center justify-between">
                        <div class="space-y-1">
                            <h4 class="font-black uppercase tracking-wider text-sm">Butuh Bantuan?</h4>
                            <p class="text-xs font-medium text-white/80 italic">Hubungi tim Lazismu</p>
                        </div>
                        <a href="{{ generate_whatsapp_url(setting('whatsapp', '08123456789'), 'Hai Lazismu, saya ingin bertanya tentang program: ' . $campaign->title) }}" class="size-12 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center hover:bg-white group transition-all">
                            <flux:icon.phone class="size-6 text-white group-hover:text-primary transition-colors" />
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-layouts.guest>
