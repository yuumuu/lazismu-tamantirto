<x-layouts.guest title="Tentang Kami">
    <!-- Page Header -->
    <header class="py-24 bg-zinc-900 border-b border-white/5 relative overflow-hidden">
        <img src="https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?q=80&w=2070&auto=format&fit=crop" class="absolute inset-0 size-full object-cover opacity-20 blur-sm">
        <div class="relative mx-auto max-w-7xl px-6 lg:px-8 text-center space-y-6">
            <h2 class="text-primary font-black uppercase tracking-[0.3em] text-xs">Tentang Kami</h2>
            <h1 class="text-4xl md:text-6xl font-black text-white tracking-tight leading-tight">
                Menebar Kebaikan,<br>Membangun Kemandirian Umat
            </h1>
        </div>
    </header>

    <!-- Mission Vision -->
    <section class="py-24 bg-white dark:bg-zinc-950">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-24 items-center">
                <div class="space-y-8">
                    <div class="space-y-4">
                        <h3 class="text-3xl font-black text-zinc-900 dark:text-white tracking-tight">Visi Kami</h3>
                        <p class="text-lg text-zinc-500 font-medium leading-relaxed">
                            Menjadi Lembaga Amil Zakat yang Amanah, Profesional, dan Mandiri dalam memberdayakan masyarakat melalui pengelolaan zakat, infaq, dan sedekah yang berkualitas.
                        </p>
                    </div>
                    <div class="space-y-4">
                        <h3 class="text-3xl font-black text-zinc-900 dark:text-white tracking-tight">Misi Kami</h3>
                        <ul class="space-y-4 text-zinc-500 font-medium">
                            <li class="flex items-start gap-4">
                                <span class="size-6 rounded-lg bg-primary/10 flex items-center justify-center text-primary mt-1 shrink-0"><flux:icon.check class="size-4" /></span>
                                <span>Meningkatkan kesadaran masyarakat tentang pentingnya zakat, infaq, dan sedekah.</span>
                            </li>
                            <li class="flex items-start gap-4">
                                <span class="size-6 rounded-lg bg-primary/10 flex items-center justify-center text-primary mt-1 shrink-0"><flux:icon.check class="size-4" /></span>
                                <span>Mengelola dana publik secara transparan, akuntebel, dan sesuai syariat.</span>
                            </li>
                            <li class="flex items-start gap-4">
                                <span class="size-6 rounded-lg bg-primary/10 flex items-center justify-center text-primary mt-1 shrink-0"><flux:icon.check class="size-4" /></span>
                                <span>Menyalurkan bantuan secara merata dan tepat sasaran di wilayah Tamantirto.</span>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="relative">
                    <img src="https://images.unsplash.com/photo-1542601906990-b4d3fb773b09?q=80&w=1914&auto=format&fit=crop" class="rounded-[64px] shadow-2xl skew-y-3 hover:skew-y-0 transition-transform duration-700">
                    <div class="absolute -bottom-8 -left-8 size-48 bg-primary rounded-[48px] -z-10 blur-2xl opacity-20 antialiased"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Core Values -->
    <section class="py-24 bg-zinc-50 dark:bg-zinc-900/50">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="text-center space-y-4 mb-20">
                <h2 class="text-primary font-black uppercase tracking-[0.3em] text-xs">Nilai Utama</h2>
                <h3 class="text-3xl md:text-5xl font-black text-zinc-900 dark:text-white tracking-tight">Prinsip Kami Dalam Melayani</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach([
                    ['title' => 'Amanah', 'desc' => 'Menjaga kepercayaan donatur dengan integritas tertinggi.', 'icon' => 'shield-check'],
                    ['title' => 'Profesional', 'desc' => 'Mengelola dana dengan standar keunggulan dan kompetensi.', 'icon' => 'briefcase'],
                    ['title' => 'Transparan', 'desc' => 'Setiap rupiah yang masuk dan keluar dapat dipertanggungjawabkan.', 'icon' => 'magnifying-glass'],
                    ['title' => 'Mandiri', 'desc' => 'Mendorong kemandirian penerima manfaat melalui pemberdayaan.', 'icon' => 'sparkles'],
                ] as $value)
                    <div class="bg-white dark:bg-zinc-900 p-8 rounded-[32px] border border-zinc-200 dark:border-white/5 shadow-sm hover:shadow-xl transition-all group hover:-translate-y-2">
                        <div class="size-16 rounded-2xl bg-primary/10 flex items-center justify-center text-primary mb-6 shadow-inner group-hover:bg-primary group-hover:text-white transition-colors">
                            <flux:icon name="{{ $value['icon'] }}" class="size-8" />
                        </div>
                        <h4 class="text-xl font-black text-zinc-900 dark:text-white tracking-tight mb-2">{{ $value['title'] }}</h4>
                        <p class="text-sm text-zinc-500 font-medium leading-relaxed">{{ $value['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    <!-- Team / Structure Section -->
    <section class="py-24 bg-white dark:bg-zinc-950 overflow-hidden">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-24 items-center">
                <div class="space-y-8">
                    <div class="space-y-4">
                        <h2 class="text-primary font-black uppercase tracking-[0.3em] text-xs">Struktur Organisasi</h2>
                        <h3 class="text-3xl md:text-5xl font-black text-zinc-900 dark:text-white tracking-tight">Dikelola Oleh Tim Profesional & Amanah</h3>
                        <p class="text-lg text-zinc-500 font-medium leading-relaxed">
                            {{ setting('site_name', 'LazisMU') }} {{ setting('site_tagline', 'Tamantirto') }} didukung oleh individu-individu yang berdedikasi tinggi untuk melayani umat. Kami memastikan setiap proses pengelolaan dana dilakukan dengan standar profesionalisme yang ketat.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-4">
                        <flux:button :href="route('guest.structure')" variant="primary" icon="users" class="h-14 rounded-2xl px-8 font-black uppercase tracking-widest shadow-xl shadow-primary/30">
                            Lihat Struktur Lengkap
                        </flux:button>
                        <flux:button :href="route('guest.reports')" variant="ghost" icon="document-text" class="h-14 rounded-2xl px-8 font-black uppercase tracking-widest">
                            Laporan Transparansi
                        </flux:button>
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    @php
                        $previewMembers = \App\Models\TeamMember::active()->ordered()->limit(4)->get();
                    @endphp
                    @foreach($previewMembers as $index => $member)
                        <div class="relative aspect-square rounded-[32px] overflow-hidden group {{ $index % 2 === 1 ? 'mt-8' : '' }}">
                            <img src="{{ $member->photo_url }}" class="size-full object-cover transition-transform duration-700 group-hover:scale-110" alt="{{ $member->name }}">
                            <div class="absolute inset-0 bg-gradient-to-t from-zinc-900/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-end p-6">
                                <span class="text-white font-black text-lg">{{ $member->name }}</span>
                                <span class="text-primary font-bold text-xs">{{ $member->position }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <!-- CTAs Section -->
    <section class="py-24 bg-zinc-900 relative overflow-hidden">
        <div class="absolute inset-0 bg-primary/5 pattern-grid opacity-20"></div>
        <div class="mx-auto max-w-4xl px-6 lg:px-8 relative text-center space-y-12">
            <h3 class="text-3xl md:text-5xl font-black text-white tracking-tight">Siap Untuk Menebar Kebaikan?</h3>
            <p class="text-zinc-400 text-lg font-medium">Bantu kami mewujudkan kemandirian umat melalui donasi dan dukungan Anda.</p>
            <div class="flex flex-wrap justify-center gap-6">
                <flux:button :href="route('guest.campaigns.index')" variant="primary"  class="h-16 rounded-2xl px-12 font-black uppercase tracking-widest shadow-2xl shadow-primary/40">
                    Mulai Berdonasi
                </flux:button>
                <flux:button :href="route('guest.contact')" variant="ghost"  class="h-16 rounded-2xl px-12 font-black uppercase tracking-widest border-white/10 !text-white hover:!bg-white/10">
                    Hubungi Kami
                </flux:button>
            </div>
        </div>
    </section>
</x-layouts.guest>
