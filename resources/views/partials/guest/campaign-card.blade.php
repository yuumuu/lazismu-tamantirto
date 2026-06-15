@props(['campaign'])

{{-- Campaign card with eager loaded data --}}
<div
    class="bg-white dark:bg-zinc-900 rounded-[32px] overflow-hidden border border-zinc-200 dark:border-white/5 shadow-sm hover:shadow-xl hover:border-primary/20 transition-all group flex flex-col md:flex-row h-full">
    <!-- Image Section -->
    <div class="relative w-full md:w-[40%] aspect-[4/3] md:aspect-auto overflow-hidden">
        <img src="{{ $campaign->featured_image ? asset('storage/' . $campaign->featured_image) : 'https://images.unsplash.com/photo-1532629345422-7515f3d16bb6?q=80&w=2070&auto=format&fit=crop' }}"
            class="size-full object-cover group-hover:scale-105 transition-transform duration-700">

        <!-- Badges -->
        <div class="absolute top-4 left-4 flex flex-col gap-2">
            @if ($campaign->is_urgent)
                <span
                    class="px-3 py-1 rounded-full bg-red-500 text-white text-[10px] font-black uppercase tracking-wider shadow-lg">Mendesak</span>
            @endif
            <span
                class="px-3 py-1 rounded-full bg-primary text-white text-[10px] font-black uppercase tracking-wider shadow-lg">
                {{ $campaign->category->name ?? 'Program' }}
            </span>
        </div>

        @if ($campaign->end_date)
            <div class="absolute bottom-4 left-4 right-4">
                <div
                    class="px-3 py-2 rounded-xl bg-zinc-900/60 backdrop-blur-md border border-white/10 text-white text-[10px] font-bold flex items-center gap-2 w-fit">
                    <flux:icon.clock class="size-3.5 text-primary" />
                    <span>{{ $campaign->days_remaining }} Hari Lagi</span>
                </div>
            </div>
        @endif
    </div>

    <!-- Content Section -->
    <div class="p-8 md:p-10 flex-1 flex flex-col justify-between space-y-6">
        <div class="space-y-4">
            <h4
                class="text-2xl font-black text-zinc-900 dark:text-white leading-tight line-clamp-2 group-hover:text-primary transition-colors">
                <a
                    href="{{ guest_route('guest.campaigns.show', ['slug' => $campaign->slug]) }}">{{ $campaign->title }}</a>
            </h4>
            <p class="text-sm text-zinc-500 line-clamp-2 font-medium">
                {{ $campaign->short_description }}
            </p>
        </div>

        <div class="space-y-6">
            <!-- Progress Bar -->
            <div class="space-y-2">
                <div class="flex justify-between items-end">
                    <div class="flex flex-col">
                        <span class="text-[10px] text-zinc-400 font-black uppercase">Terkumpul</span>
                        <span
                            class="text-lg font-black text-primary tracking-tight">{{ format_rupiah_short($campaign->verified_donations_sum_amount ?? $campaign->current_amount) }}</span>
                    </div>
                    <div class="flex flex-col items-end">
                        <span class="text-[10px] text-zinc-400 font-black uppercase">Target</span>
                        <span
                            class="text-sm font-bold text-zinc-900 dark:text-white">{{ format_rupiah_short($campaign->target_amount) }}</span>
                    </div>
                </div>
                <div class="relative w-full h-3 bg-zinc-100 dark:bg-white/5 rounded-full overflow-hidden">
                    <div class="absolute top-0 left-0 h-full bg-primary rounded-full transition-all duration-1000 group-hover:bg-primary-light"
                        style="width: {{ $campaign->progress_percentage }}%">
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent animate-shimmer">
                        </div>
                    </div>
                </div>
                <div class="flex justify-between text-[10px] font-bold uppercase tracking-widest">
                    <span class="text-primary">{{ $campaign->progress_percentage }}% Tercapai</span>
                    <span class="text-zinc-500">{{ $campaign->verified_donations_count ?? 0 }} Donatur</span>
                </div>
            </div>

            <div class="flex items-center gap-4 pt-2">
                <flux:button href="{{ guest_route('guest.donate.form', ['campaign_slug' => $campaign->slug]) }}"
                    variant="primary"
                    class="flex-1 font-black uppercase tracking-wider rounded-2xl h-14 shadow-lg shadow-primary/20 !text-white hover:!bg-primary/90">
                    Donasi Sekarang
                </flux:button>
                <flux:button href="{{ guest_route('guest.campaigns.show', ['slug' => $campaign->slug]) }}"
                    variant="ghost"
                    class="size-14 rounded-2xl border border-zinc-200 dark:border-white/10 flex items-center justify-center p-0">
                    <flux:icon.share class="size-6 text-zinc-500" />
                </flux:button>
            </div>
        </div>
    </div>
</div>
