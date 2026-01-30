@php
    $banners = \App\Models\Banner::where('is_active', true)->orderBy('order')->get();
@endphp

<section x-data="{ 
    activeSlide: 0, 
    slidesCount: {{ $banners->count() }},
    autoPlay() {
        setInterval(() => {
            this.activeSlide = (this.activeSlide + 1) % this.slidesCount;
        }, 5000);
    }
}" x-init="autoPlay()" class="relative w-full h-[600px] md:h-[700px] lg:h-[800px] overflow-hidden bg-zinc-900 border-b border-white/5">
    
    <!-- Slides -->
    <div class="relative w-full h-full">
        @foreach($banners as $index => $banner)
            <div x-show="activeSlide === {{ $index }}" 
                 x-transition:enter="transition ease-out duration-1000"
                 x-transition:enter-start="opacity-0 scale-105"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-1000"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute inset-0 w-full h-full"
                 style="display: none;">
                
                <!-- Background Image -->
                <div class="absolute inset-0">
                    <img src="{{ $banner->image_path }}" alt="{{ $banner->title }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-gradient-to-r from-zinc-950/90 via-zinc-950/50 to-transparent"></div>
                    <div class="absolute inset-x-0 bottom-0 h-64 bg-gradient-to-t from-zinc-950/80 to-transparent"></div>
                </div>

                <!-- Content -->
                <div class="relative h-full mx-auto max-w-7xl px-6 lg:px-8 flex flex-col justify-center">
                    <div class="max-w-2xl space-y-8">
                        <div class="space-y-4">
                            <h2 class="inline-block px-4 py-1.5 rounded-full bg-primary/20 border border-primary/30 text-primary text-xs font-black uppercase tracking-[0.2em] animate-fade-in">
                                Lazismu Tamantirto
                            </h2>
                            <h1 class="text-4xl md:text-6xl lg:text-7xl font-black text-white leading-[1.1] tracking-tight">
                                {{ $banner->title }}
                            </h1>
                            <p class="text-lg md:text-xl text-zinc-300 leading-relaxed max-w-xl font-medium">
                                {{ $banner->subtitle }}
                            </p>
                        </div>
                        
                        <div class="flex flex-wrap gap-4">
                            @if($banner->button_link)
                                <flux:button href="{{ $banner->button_link }}" variant="primary"  class="px-8 font-black uppercase tracking-wider h-14 rounded-2xl shadow-2xl shadow-primary/40 group">
                                    {{ $banner->button_text ?? 'Donasi Sekarang' }}
                                    <flux:icon.arrow-right class="size-5 ms-2 group-hover:translate-x-1 transition-transform" />
                                </flux:button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Controls -->
    @if($banners->count() > 1)
        <!-- Pagination Dots -->
        <div class="absolute bottom-12 left-6 lg:left-8 flex gap-3 z-20">
            @foreach($banners as $index => $banner)
                <button @click="activeSlide = {{ $index }}" 
                        class="h-1.5 transition-all duration-500 rounded-full"
                        :class="activeSlide === {{ $index }} ? 'w-12 bg-primary' : 'w-6 bg-white/20 hover:bg-white/40'"></button>
            @endforeach
        </div>

        <!-- Navigation Arrows -->
        <div class="absolute bottom-12 right-6 lg:right-8 flex gap-4 z-20">
            <button @click="activeSlide = (activeSlide - 1 + slidesCount) % slidesCount" 
                    class="size-12 rounded-2xl bg-white/5 border border-white/10 text-white flex items-center justify-center hover:bg-primary transition-all group">
                <flux:icon.chevron-left class="size-6 transition-transform group-hover:-translate-x-1" />
            </button>
            <button @click="activeSlide = (activeSlide + 1) % slidesCount" 
                    class="size-12 rounded-2xl bg-white/5 border border-white/10 text-white flex items-center justify-center hover:bg-primary transition-all group">
                <flux:icon.chevron-right class="size-6 transition-transform group-hover:translate-x-1" />
            </button>
        </div>
    @endif

</section>
