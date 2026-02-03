@php
    $startDate = now()->startOfMonth();
    $endDate = now()->endOfMonth();
    
    $donations = \App\Models\Donation::verified()
        ->whereBetween('verified_at', [$startDate, $endDate])
        ->get();

    $withdrawals = \App\Models\Withdrawal::where('status', \App\Enums\WithdrawalStatus::Sent)
        ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
        ->get();

    $totalIncome = $donations->sum('amount');
    $totalOutcome = $withdrawals->sum('amount');
    $balance = $totalIncome - $totalOutcome;

    $recentActivity = collect()
        ->concat($donations->map(fn($d) => ['type' => 'income', 'label' => 'Donasi: ' . ($d->is_anonymous ? 'Hamba Allah' : $d->donor_name), 'amount' => $d->amount, 'date' => $d->verified_at]))
        ->concat($withdrawals->map(fn($w) => ['type' => 'outcome', 'label' => 'Penyaluran: ' . ($w->mustahik?->name ?? 'Program Kemanusiaan'), 'amount' => $w->amount, 'date' => $w->date]))
        ->sortByDesc('date')
        ->take(15);
@endphp

<x-layouts.guest title="Laporan Keuangan">
    <!-- Page Header -->
    <header class="py-20 bg-zinc-50 dark:bg-zinc-900/50 border-b border-zinc-200 dark:border-white/5">
        <div class="mx-auto max-w-7xl px-6 lg:px-8 text-center space-y-6">
            <h2 class="text-primary font-black uppercase tracking-[0.3em] text-xs">Transparansi Dana</h2>
            <h1 class="text-4xl md:text-5xl font-black text-zinc-900 dark:text-white tracking-tight leading-tight">Laporan Penyaluran & Keuangan</h1>
            <p class="text-lg text-zinc-500 font-medium max-w-2xl mx-auto italic">
                Setiap rupiah yang Anda amanahkan sangat berarti. Kami berkomitmen untuk menyalurkannya secara transparan dan tepat sasaran.
            </p>
        </div>
    </header>

    <section class="py-24 bg-white dark:bg-zinc-950">
        <div class="mx-auto max-w-7xl px-6 lg:px-8 space-y-20">
            
            <!-- Summary Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="p-10 rounded-[48px] bg-zinc-50 dark:bg-white/5 border border-zinc-100 dark:border-white/5 space-y-4">
                    <span class="text-[10px] text-zinc-400 font-black uppercase tracking-[0.2em]">Donasi Terkumpul</span>
                    <h3 class="text-4xl font-black text-zinc-900 dark:text-white tracking-tighter">{{ format_rupiah($totalIncome) }}</h3>
                    <p class="text-xs text-zinc-500 font-bold italic">Periode: {{ $startDate->format('M Y') }}</p>
                </div>
                <div class="p-10 rounded-[48px] bg-primary/5 dark:bg-primary/20 border border-primary/10 space-y-4 shadow-xl shadow-primary/5">
                    <span class="text-[10px] text-zinc-400 font-black uppercase tracking-[0.2em]">Telah Disalurkan</span>
                    <h3 class="text-4xl font-black text-primary tracking-tighter">{{ format_rupiah($totalOutcome) }}</h3>
                    <p class="text-xs text-zinc-500 font-bold italic">Melalui {{ $withdrawals->count() }} program bantuan</p>
                </div>
                <div class="p-10 rounded-[48px] bg-zinc-50 dark:bg-white/5 border border-zinc-100 dark:border-white/5 space-y-4">
                    <span class="text-[10px] text-zinc-400 font-black uppercase tracking-[0.2em]">Saldo Saat Ini</span>
                    <h3 class="text-4xl font-black text-zinc-900 dark:text-white tracking-tighter">{{ format_rupiah($balance) }}</h3>
                    <p class="text-xs text-zinc-500 font-bold italic">Dana siap disalurkan</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-16">
                <!-- Activity List -->
                <div class="lg:col-span-2 space-y-12">
                    <h4 class="text-2xl font-black text-zinc-900 dark:text-white tracking-tight px-4 border-l-4 border-primary">Aktivitas Terbaru</h4>
                    <div class="space-y-4">
                        @foreach($recentActivity as $act)
                            <div class="p-6 rounded-[32px] bg-white dark:bg-zinc-900 border border-zinc-100 dark:border-white/5 shadow-sm hover:shadow-md transition-all flex items-center justify-between group">
                                <div class="flex items-center gap-6">
                                    <div class="size-14 rounded-2xl flex items-center justify-center flex-shrink-0 {{ $act['type'] === 'income' ? 'bg-green-100 dark:bg-green-900/30 text-green-600' : 'bg-primary/10 text-primary' }}">
                                        <flux:icon name="{{ $act['type'] === 'income' ? 'arrow-down-tray' : 'arrow-up-tray' }}" class="size-6" />
                                    </div>
                                    <div>
                                        <p class="font-black text-zinc-900 dark:text-white tracking-tight leading-tight">{{ $act['label'] }}</p>
                                        <p class="text-[10px] text-zinc-400 font-black uppercase tracking-widest mt-1">{{ \Carbon\Carbon::parse($act['date'])->format('d M Y, H:i') }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-black {{ $act['type'] === 'income' ? 'text-green-600' : 'text-primary' }}">
                                        {{ $act['type'] === 'income' ? '+' : '-' }} {{ format_rupiah($act['amount']) }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Summary Sidebar -->
                <div class="space-y-12">
                    <div class="bg-zinc-900 p-8 rounded-[48px] text-white shadow-2xl space-y-8">
                        <h4 class="text-xl font-black tracking-tight border-b border-white/10 pb-4">Komitmen Kami</h4>
                        <ul class="space-y-6">
                            <li class="flex items-start gap-4">
                                <flux:icon name="shield-check" class="size-6 text-primary shrink-0" />
                                <div class="space-y-1">
                                    <p class="text-sm font-black tracking-tight">Akuntabel</p>
                                    <p class="text-[10px] text-zinc-400 leading-relaxed font-medium">Audit berkala oleh lembaga terkait.</p>
                                </div>
                            </li>
                            <li class="flex items-start gap-4">
                                <flux:icon name="magnifying-glass" class="size-6 text-primary shrink-0" />
                                <div class="space-y-1">
                                    <p class="text-sm font-black tracking-tight">Transparan</p>
                                    <p class="text-[10px] text-zinc-400 leading-relaxed font-medium">Laporan dapat diakses kapan saja oleh publik.</p>
                                </div>
                            </li>
                        </ul>
                        <flux:button href="{{ route('guest.donate.form') }}" variant="primary"  class="w-full h-16 rounded-2xl font-black uppercase tracking-widest shadow-xl shadow-primary/30">
                            Ikut Berkontribusi
                        </flux:button>
                    </div>

                    <div class="p-8 rounded-[40px] bg-zinc-50 dark:bg-white/5 border border-zinc-100 dark:border-white/5 space-y-6">
                        <h4 class="text-xl font-black text-zinc-900 dark:text-white tracking-tight">Arsip Laporan Tahunan</h4>
                        <div class="space-y-3">
                            @php
                                $publicReports = \App\Models\FinancialReport::where('is_published', true)
                                    ->latest()
                                    ->get();
                            @endphp
                            @forelse($publicReports as $report)
                                <a href="{{ $report->file_url }}" target="_blank" class="flex items-center justify-between p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-100 dark:border-white/5 shadow-sm hover:shadow-md transition-all group">
                                    <div class="flex items-center gap-3">
                                        <flux:icon name="document-text" class="size-5 text-zinc-400 group-hover:text-primary transition-colors" />
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-zinc-900 dark:text-white leading-tight">{{ $report->title }}</span>
                                            <span class="text-[10px] text-zinc-500 font-medium uppercase tracking-widest">{{ $report->year }}</span>
                                        </div>
                                    </div>
                                    <flux:icon name="arrow-down-tray" class="size-4 text-zinc-400" />
                                </a>
                            @empty
                                <p class="text-xs text-zinc-500 italic">Belum ada laporan tersedia.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="p-8 rounded-[40px] bg-zinc-50 dark:bg-white/5 border border-zinc-100 dark:border-white/5 space-y-6">
                        <h4 class="text-lg font-black text-zinc-900 dark:text-white tracking-tight">Butuh Laporan Lengkap?</h4>
                        <p class="text-xs text-zinc-500 font-medium leading-relaxed italic">Anda dapat meminta rincian laporan keuangan tahunan melalui kantor pelayanan kami.</p>
                        <flux:button href="{{ route('guest.contact') }}" variant="ghost" class="w-full h-14 rounded-2xl font-bold border-zinc-200 dark:border-white/10">Hubungi Kami</flux:button>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.guest>
