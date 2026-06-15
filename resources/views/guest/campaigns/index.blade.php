<x-layouts.guest title="Program Donasi">
    <!-- Page Header -->
    <header class="py-20 bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-white/5">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="max-w-3xl space-y-6">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-1.5 bg-primary rounded-full"></div>
                    <h2 class="text-primary font-black uppercase text-xs">Program Kebaikan</h2>
                </div>
                <h1 class="text-4xl md:text-6xl font-black text-zinc-900 dark:text-white tracking-tight leading-tight">
                    Pilih Program Donasi & Alirkan Kebaikan
                </h1>
                <p class="text-lg text-zinc-500 font-medium">
                    Setiap rupiah yang Anda berikan adalah secercah harapan bagi mereka yang membutuhkan. Mari
                    bergotong-royong membangun negeri.
                </p>
            </div>
        </div>
    </header>

    <!-- Filter & Content Section -->
    <section class="py-12 bg-white dark:bg-zinc-950 min-h-screen">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <!-- Toolbar -->
            <form method="GET" action="{{ guest_route('guest.campaigns.index') }}"
                class="flex flex-col lg:flex-row gap-6 items-center justify-between mb-12">
                <div class="w-full lg:w-1/3">
                    <flux:input name="search" value="{{ request('search') }}" placeholder="Cari program donasi..."
                        icon="magnifying-glass" class="rounded-2xl" />
                </div>

                <div class="flex items-center gap-4 w-full lg:w-auto">
                    <div class="flex-1 lg:w-48">
                        <flux:select name="category" placeholder="Semua Kategori" class="rounded-2xl"
                            onchange="this.form.submit()">
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </flux:select>
                    </div>
                    <div class="flex-1 lg:w-48">
                        <flux:select name="sort" placeholder="Urutkan" class="rounded-2xl"
                            onchange="this.form.submit()">
                            <option value="latest" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>Terbaru
                            </option>
                            <option value="urgent" {{ request('sort') == 'urgent' ? 'selected' : '' }}>Mendesak</option>
                            <option value="target" {{ request('sort') == 'target' ? 'selected' : '' }}>Target Terbesar
                            </option>
                        </flux:select>
                    </div>
                </div>
            </form>

            <div class="space-y-8">
                @forelse($campaigns as $campaign)
                    @include('partials.guest.campaign-card', ['campaign' => $campaign])
                @empty
                    <div class="py-32 text-center">
                        <div
                            class="inline-flex size-20 items-center justify-center rounded-3xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-100 dark:border-white/5 text-zinc-300 mb-6">
                            <flux:icon.magnifying-glass class="size-10" />
                        </div>
                        <h4 class="text-xl font-black text-zinc-900 dark:text-white mb-2 tracking-tight">Tidak Ada
                            Program Ditemukan</h4>
                        <p class="text-zinc-500 font-medium italic">Coba cari dengan kata kunci lain atau ubah filter
                            Anda.</p>
                        @if (request()->hasAny(['search', 'category', 'sort']))
                            <div class="mt-6">
                                <a href="{{ guest_route('guest.campaigns.index') }}"
                                    class="inline-flex items-center gap-2 px-6 py-3 bg-primary text-white rounded-2xl font-bold hover:bg-primary/90 transition-colors">
                                    <flux:icon.arrow-path class="size-5" />
                                    Reset Filter
                                </a>
                            </div>
                        @endif
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if ($campaigns->hasPages())
                <div class="mt-20 flex justify-center">
                    {{ $campaigns->links() }}
                </div>
            @endif

            <!-- Results Info -->
            <div class="mt-8 text-center">
                <p class="text-sm text-zinc-400 italic">
                    Menampilkan {{ $campaigns->count() }} dari {{ $campaigns->total() }} program
                    @if (request()->hasAny(['search', 'category', 'sort']))
                        <span class="text-primary">dengan filter aktif</span>
                    @endif
                </p>
            </div>
        </div>
    </section>
</x-layouts.guest>
