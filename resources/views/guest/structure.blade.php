<x-layouts.guest>
    <!-- Page Header -->
    <header class="py-20 bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-white/5">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="max-w-3xl space-y-6 text-center mx-auto">
                <h2 class="text-primary font-black uppercase tracking-[0.3em] text-xs">Struktur Organisasi</h2>
                <h1 class="text-4xl md:text-5xl font-black text-zinc-900 dark:text-white tracking-tight">Keluarga Besar Lazismu Tamantirto</h1>
                <p class="text-lg text-zinc-500 font-medium leading-relaxed">
                    Dipimpin oleh tim yang berdedikasi dan amanah untuk menjamin ketepatan penyaluran donasi Anda.
                </p>
            </div>
        </div>
    </header>

    <section class="py-24 bg-white dark:bg-zinc-950">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8 md:gap-12">
                @php
                    $members = \App\Models\TeamMember::with('position')->active()->ordered()->get();
                @endphp

                @foreach($members as $member)
                    <div class="flex flex-col items-center text-center space-y-6 group">
                        <div class="relative w-full aspect-[4/5] rounded-[48px] overflow-hidden shadow-2xl border-4 border-zinc-50 dark:border-zinc-900 group-hover:border-primary/20 transition-all">
                            <img src="{{ $member->photo_url }}" class="size-full object-cover grayscale group-hover:grayscale-0 transition-all duration-700 scale-105 group-hover:scale-100" alt="{{ $member->name }}">
                            <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-zinc-900/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                        </div>
                        <div class="space-y-1">
                            <h4 class="text-xl font-black text-zinc-900 dark:text-white tracking-tight group-hover:text-primary transition-colors">{{ $member->name }}</h4>
                            <p class="text-xs font-black text-zinc-400 uppercase tracking-[0.2em]">{{ $member->position->name ?? 'Staf' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</x-layouts.guest>
