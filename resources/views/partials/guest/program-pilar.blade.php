<section class="py-24 bg-zinc-50 dark:bg-zinc-900/50">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="text-center space-y-4 mb-20">
            <h2 class="text-primary font-black uppercase tracking-[0.3em] text-xs">Pilar Program</h2>
            <h3 class="text-4xl md:text-5xl font-black text-zinc-900 dark:text-white tracking-tight">Fokus Kebaikan Kami</h3>
            <p class="text-zinc-500 font-medium max-w-2xl mx-auto italic">Pilar-pilar utama Lazismu dalam menggerakkan kemanfaatan bagi sesama.</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 md:gap-8">
            @php
                $pillars = [
                    ['label' => 'Pendidikan', 'icon' => 'academic-cap', 'desc' => 'Beasiswa & Sarana'],
                    ['label' => 'Kesehatan', 'icon' => 'heart', 'desc' => 'Bantuan Medis'],
                    ['label' => 'Ekonomi', 'icon' => 'banknotes', 'desc' => 'Pemberdayaan'],
                    ['label' => 'Sosial', 'icon' => 'users', 'desc' => 'Dakwah & Relawan'],
                    ['label' => 'Kemanusiaan', 'icon' => 'globe-asia-australia', 'desc' => 'Tanggap Bencana'],
                ];
            @endphp

            @foreach($pillars as $pillar)
                <div class="group bg-white dark:bg-zinc-900 p-8 rounded-[48px_0_48px_0] border border-zinc-200 dark:border-white/5 shadow-sm hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 text-center space-y-4">
                    <div class="size-16 mx-auto rounded-3xl bg-primary/5 dark:bg-primary/20 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors duration-500">
                        <flux:icon name="{{ $pillar['icon'] }}" class="size-8" />
                    </div>
                    <div class="space-y-1">
                        <h4 class="font-black text-zinc-900 dark:text-white uppercase tracking-tight text-sm">{{ $pillar['label'] }}</h4>
                        <p class="text-xs text-zinc-400 font-bold">{{ $pillar['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
