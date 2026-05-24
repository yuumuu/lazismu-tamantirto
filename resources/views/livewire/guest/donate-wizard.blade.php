<div class="space-y-8">
    <!-- Stepper Header -->
    <div class="flex items-center justify-between mb-12">
        @foreach([1 => 'Nominal', 2 => 'Profil', 3 => 'Metode'] as $num => $label)
        <div class="flex flex-col items-center gap-3 relative flex-1">
            <div class="size-12 rounded-2xl flex items-center justify-center transition-all duration-500 {{ $step >= $num ? 'bg-primary text-white shadow-xl shadow-primary/30' : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-500' }}">
                @if($step > $num)
                <flux:icon.check class="size-6" />
                @else
                <span class="font-black text-lg">{{ $num }}</span>
                @endif
            </div>
            <span class="text-[10px] font-black uppercase tracking-[0.2em] {{ $step >= $num ? 'text-primary' : 'text-zinc-400' }}">{{ $label }}</span>

            @if($num < 3)
                <div class="absolute top-6 left-[calc(50%+24px)] right-[calc(-50%+24px)] h-0.5 bg-zinc-200 dark:bg-zinc-800 -z-10">
                    <div class="h-full bg-primary transition-all duration-700" style="width: {{ $step > $num ? '100%' : '0%' }}"></div>
                </div>
            @endif
        </div>
        @endforeach
    </div>

<!-- Wizard Content -->
<div class="bg-white dark:bg-zinc-900 rounded-[40px] border border-zinc-200 dark:border-white/5 shadow-2xl p-8 md:p-12">
    @if($step === 1)
    <!-- Step 1: Amount & Campaign -->
    <div class="space-y-10 animate-fade-in">
        <!-- Calculator Info Banner (if from calculator) -->
        @if($from_calculator && $donation_subtype)
        <div class="p-6 rounded-[28px] bg-gradient-to-r from-primary/10 to-primary/5 border border-primary/20 flex items-center gap-4 animate-fade-in">
            <div class="size-12 rounded-2xl bg-primary/20 flex items-center justify-center">
                <flux:icon.calculator class="size-6 text-primary" />
            </div>
            <div class="flex-1">
                <h4 class="font-black text-zinc-900 dark:text-white text-sm mb-1">Dari Kalkulator Zakat</h4>
                <p class="text-xs text-zinc-600 dark:text-zinc-400 font-medium">
                    Nominal {{ $this->getZakatSubtypeLabel() }} Anda: <span class="font-black text-primary">{{ format_rupiah($amount) }}</span>
                </p>
            </div>
            <a href="{{ route('guest.calculator') }}" class="text-xs text-primary font-bold hover:underline">Hitung Ulang</a>
        </div>
        @endif

        <div class="text-center space-y-2">
            <h2 class="text-3xl font-black text-zinc-900 dark:text-white tracking-tight">Pilih Nominal Donasi</h2>
            <p class="text-zinc-500 font-medium">Berapa banyak kebaikan yang ingin Anda alirkan hari ini?</p>
        </div>

        <!-- Cabang Selection -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <label class="text-sm font-black text-zinc-400 uppercase tracking-widest">Pilih Cabang Penyalur</label>
            </div>
            <flux:select wire:model.live="masjid_id" class="h-14 rounded-2xl bg-zinc-50 dark:bg-zinc-800/50">
                @foreach(\App\Models\Masjid::where('is_active', true)->get() as $masjid)
                    <option value="{{ $masjid->id }}">{{ $masjid->name }}</option>
                @endforeach
            </flux:select>
        </div>

        <!-- Donation Type Cards -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <label class="text-sm font-black text-zinc-400 uppercase tracking-widest">Jenis Donasi</label>
                @if($from_calculator && $donation_type === 'zakat')
                <span class="text-xs text-primary font-bold">✓ Dipilih dari kalkulator</span>
                @endif
            </div>
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                @foreach(\App\Enums\CampaignType::cases() as $type)
                <button type="button"
                    wire:click="setDonationType('{{ $type->value }}')"
                    class="p-4 rounded-2xl border-2 transition-all flex flex-col items-center gap-3 group {{ $donation_type == $type->value ? 'border-primary bg-primary/5' : 'border-zinc-100 dark:border-white/5 hover:border-primary/50' }}">
                    <div class="size-10 rounded-xl bg-zinc-50 dark:bg-white/5 flex items-center justify-center text-zinc-400 group-hover:text-primary transition-colors {{ $donation_type == $type->value ? 'text-primary' : '' }}">
                        <flux:icon name="{{ $type->icon() }}" class="size-6" />
                    </div>
                    <span class="text-xs font-black uppercase tracking-tight {{ $donation_type == $type->value ? 'text-primary' : 'text-zinc-600 dark:text-zinc-400' }}">
                        {{ $type->label() }}
                    </span>
                </button>
                @endforeach
            </div>
        </div>

        <!-- Nominal Presets -->
        <div class="space-y-4">
            <label class="text-sm font-black text-zinc-400 uppercase tracking-widest">Pilih Nominal</label>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                @foreach([10000, 25000, 50000, 100000, 250000, 500000] as $preset)
                <button type="button"
                    wire:click="setAmount({{ $preset }})"
                    class="p-6 rounded-2xl border-2 transition-all text-center group {{ $amount == $preset ? 'border-primary bg-primary/5' : 'border-zinc-100 dark:border-white/5 hover:border-primary/50' }}">
                    <span class="block text-lg font-black {{ $amount == $preset ? 'text-primary' : 'text-zinc-600 dark:text-zinc-400' }}">
                        {{ format_rupiah($preset, false) }}
                    </span>
                </button>
                @endforeach
            </div>
        </div>

        <!-- Custom Amount -->
        <div class="space-y-4">
            <flux:input wire:model.live="amount" type="number" step="1000" label="Nominal Kustom (Rp)" placeholder="Masukkan jumlah lainnya..." class="text-xl font-black h-16 rounded-2xl" />
            @error('amount') <p class="text-xs text-red-500 font-bold">{{ $message }}</p> @enderror
        </div>

        <!-- Donation Target -->
        <div class="space-y-4">
            <label class="text-sm font-black text-zinc-400 uppercase tracking-widest">Tujuan Donasi</label>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <button type="button"
                    wire:click="selectGeneralDonation"
                    class="p-6 rounded-2xl border-2 transition-all flex flex-col items-center gap-2 group {{ !$is_specific_campaign ? 'border-primary bg-primary/5' : 'border-zinc-100 dark:border-white/5 hover:border-primary/50' }}">
                    <div class="flex items-center gap-3">
                        <flux:icon.building-library class="size-5 {{ !$is_specific_campaign ? 'text-primary' : 'text-zinc-400' }}" />
                        <span class="font-black {{ !$is_specific_campaign ? 'text-primary' : 'text-zinc-600 dark:text-zinc-400' }}">Donasi Umum</span>
                    </div>
                    <p class="text-[10px] text-zinc-400 font-medium text-center">Donasi akan disalurkan oleh Lazismu ke yang paling membutuhkan.</p>
                </button>

                <button type="button"
                    wire:click="selectSpecificCampaign"
                    class="p-6 rounded-2xl border-2 transition-all flex flex-col items-center gap-2 group {{ $is_specific_campaign ? 'border-primary bg-primary/5' : 'border-zinc-100 dark:border-white/5 hover:border-primary/50' }}">
                    <div class="flex items-center gap-3">
                        <flux:icon.megaphone class="size-5 {{ $is_specific_campaign ? 'text-primary' : 'text-zinc-400' }}" />
                        <span class="font-black {{ $is_specific_campaign ? 'text-primary' : 'text-zinc-600 dark:text-zinc-400' }}">Program Spesifik</span>
                    </div>
                    <p class="text-[10px] text-zinc-400 font-medium text-center">Donasi Anda akan disalurkan ke program spesifik pilihan Anda.</p>
                </button>
            </div>
        </div>

        <!-- Selected Campaign Info (Conditional) -->
        @if($is_specific_campaign)
        <div class="pt-8 border-t border-zinc-100 dark:border-white/5 space-y-4 animate-fade-in">
            <div class="flex items-center justify-between">
                <label class="text-sm font-black text-zinc-400 uppercase tracking-widest">Program Terpilih</label>
            </div>

            @if($selectedCampaign)
            <div class="p-6 rounded-[24px] bg-zinc-50 dark:bg-white/5 border border-zinc-100 dark:border-white/5 flex items-center gap-6">
                <img src="{{ $selectedCampaign->featured_image ? asset('storage/' . $selectedCampaign->featured_image) : 'https://images.unsplash.com/photo-1532629345422-7515f3d16bb6?q=80&w=2070&auto=format&fit=crop' }}" class="size-20 rounded-2xl object-cover">
                <div class="flex-1 space-y-1">
                    <span class="text-[10px] text-zinc-400 font-black uppercase tracking-widest">{{ $selectedCampaign->category->name ?? 'Program' }}</span>
                    <h4 class="text-lg font-black text-zinc-900 dark:text-white leading-tight line-clamp-1">{{ $selectedCampaign->title }}</h4>
                </div>
                <div class="size-10 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                    <flux:icon.check class="size-5" />
                </div>
            </div>
            @else
            <a href="{{ route('guest.campaigns.index') }}" class="w-full h-20 rounded-2xl border-dashed border-2 border-zinc-200 dark:border-white/10 bg-zinc-50/50 hover:bg-zinc-100 dark:hover:bg-white/10 transition-all group block">
                <div class="flex items-center justify-center gap-2 h-full">
                    <svg class="size-5 text-zinc-400 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <span class="font-bold text-zinc-500 group-hover:text-primary transition-colors">Klik Untuk Pilih Program</span>
                </div>
            </a>
            @endif
            @error('campaign_id') <p class="text-xs text-red-500 font-bold mt-2 text-center">{{ $message }}</p> @enderror
        </div>
        @endif

        <flux:button wire:click="nextStep" variant="primary" class="w-full h-16 rounded-2xl font-black uppercase tracking-widest shadow-xl shadow-primary/30 !text-white hover:!bg-primary/90">
            Lanjutkan ke Profil
        </flux:button>
    </div>
    @elseif($step === 2)
    <!-- Step 2: Donor Info -->
    <div class="space-y-10 animate-fade-in shadow-xs">
        <div class="text-center space-y-2">
            <h2 class="text-3xl font-black text-zinc-900 dark:text-white tracking-tight">Informasi Donatur</h2>
            <p class="text-zinc-500 font-medium">Lengkapi sedikit data diri Anda untuk riwayat donasi.</p>
        </div>

        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <flux:input wire:model="donor_name" label="Nama Lengkap" placeholder="Masukkan nama Anda..." class="rounded-2xl h-14" />
                <flux:input wire:model="donor_phone" label="Nomor WhatsApp" placeholder="Contoh: 08123456789" class="rounded-2xl h-14" />
            </div>
            <flux:input wire:model="donor_email" label="Email" type="email" placeholder="nama@email.com" class="rounded-2xl h-14" />

            <flux:textarea wire:model="donor_message" label="Pesan & Doa (Opsional)" placeholder="Tuliskan pesan atau doa Anda..." rows="3" class="rounded-2xl" />

            <div class="p-6 rounded-2xl bg-zinc-50 dark:bg-white/5 border border-zinc-100 dark:border-white/5">
                <flux:switch wire:model="is_anonymous" label="Sembunyikan Nama Saya" description="Nama akan ditampilkan sebagai 'Hamba Allah' di publik." />
            </div>
        </div>

        <div class="flex gap-4">
            <flux:button wire:click="previousStep" variant="ghost" class="flex-1 h-16 rounded-2xl font-bold border-zinc-200 dark:border-white/10">Kembali</flux:button>
            <flux:button wire:click="nextStep" variant="primary" class="flex-2 h-16 rounded-2xl font-black uppercase tracking-widest shadow-xl shadow-primary/30">Lanjutkan</flux:button>
        </div>
    </div>
    @elseif($step === 3)
    <!-- Step 3: Payment Method -->
    <div class="space-y-10 animate-fade-in">
        <div class="text-center space-y-2">
            <h2 class="text-3xl font-black text-zinc-900 dark:text-white tracking-tight">Metode Pembayaran</h2>
            <p class="text-zinc-500 font-medium">Pilih metode yang paling memudahkan bagi Anda.</p>
        </div>

        <div class="space-y-6">
            <!-- Bank Transfer Section -->
            <div class="space-y-4">
                <label class="text-sm font-black text-zinc-400 uppercase tracking-widest">Transfer Bank (Manual Konfirmasi)</label>
                <div class="grid grid-cols-1 gap-4">
                    @foreach($bank_accounts as $index => $bank)
                    <button type="button"
                        wire:click="selectBankTransfer({{ $index }})"
                        class="flex items-center gap-6 p-6 rounded-[28px] border-2 text-left transition-all hover:border-primary/50 {{ $payment_method == 'bank_transfer' && $selected_bank === $index ? 'border-primary bg-primary/5 shadow-lg shadow-primary/10' : 'border-zinc-100 dark:border-white/5 bg-zinc-50/50 dark:bg-white/2' }}">
                        <!-- Radio Indicator -->
                        <div class="size-6 rounded-full border-2 flex items-center justify-center transition-all {{ $payment_method == 'bank_transfer' && $selected_bank === $index ? 'border-primary' : 'border-zinc-300 dark:border-zinc-700' }}">
                            @if($payment_method == 'bank_transfer' && $selected_bank === $index)
                            <div class="size-3 rounded-full bg-primary animate-scale-in"></div>
                            @endif
                        </div>

                        <div class="flex-1 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="size-14 rounded-2xl bg-white dark:bg-zinc-800 border border-zinc-100 dark:border-white/5 flex items-center justify-center shadow-sm">
                                    <span class="font-black text-primary text-[10px] uppercase tracking-tighter">{{ substr($bank['bank_name'], 0, 3) }}</span>
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-black text-zinc-900 dark:text-white tracking-tight leading-none mb-1">{{ $bank['bank_name'] }}</span>
                                    <span class="text-xs text-zinc-500 font-medium">{{ $bank['account_name'] }}</span>
                                </div>
                            </div>
                            <div class="flex flex-col items-end">
                                <span class="text-sm font-mono font-black text-zinc-900 dark:text-white tracking-tight">{{ $bank['account_number'] }}</span>
                                <span class="text-[8px] text-zinc-400 font-black uppercase tracking-widest">Salin Nomor</span>
                            </div>
                        </div>
                    </button>
                    @endforeach
                </div>
            </div>

            <!-- E-Wallet / QRIS Section -->
            <div class="space-y-4 pt-6 border-t border-zinc-100 dark:border-white/5">
                <label class="text-sm font-black text-zinc-400 uppercase tracking-widest">Otomatis / QRIS</label>
                <button type="button"
                    wire:click="selectQris"
                    class="w-full flex items-center gap-6 p-6 rounded-[28px] border-2 text-left transition-all hover:border-primary/50 {{ $payment_method == 'qris' ? 'border-primary bg-primary/5 shadow-lg shadow-primary/10' : 'border-zinc-100 dark:border-white/5 bg-zinc-50/50 dark:bg-white/2' }}">

                    <!-- Radio Indicator -->
                    <div class="size-6 rounded-full border-2 flex items-center justify-center transition-all {{ $payment_method == 'qris' ? 'border-primary' : 'border-zinc-300 dark:border-zinc-700' }}">
                        @if($payment_method == 'qris')
                        <div class="size-3 rounded-full bg-primary animate-scale-in"></div>
                        @endif
                    </div>

                    <div class="flex-1 flex items-center gap-4">
                        <div class="size-14 rounded-2xl bg-white dark:bg-zinc-800 border border-zinc-100 dark:border-white/5 flex items-center justify-center shadow-sm">
                            <flux:icon.qr-code class="size-7 text-primary" />
                        </div>
                        <div class="flex flex-col">
                            <span class="font-black text-zinc-900 dark:text-white tracking-tight leading-none mb-1">QRIS / E-Wallet</span>
                            <span class="text-xs text-zinc-500 font-medium tracking-tight">GoPay, OVO, Dana, LinkAja, ShopeePay</span>
                        </div>
                    </div>

                    <div class="hidden md:flex items-center gap-2">
                        <div class="px-3 py-1 rounded-full bg-green-500/10 text-green-600 text-[8px] font-black uppercase tracking-widest border border-green-500/20">Otomatis</div>
                    </div>
                </button>
            </div>
        </div>

        <div class="flex flex-col md:flex-row gap-4 pt-8">
            <flux:button wire:click="previousStep" variant="ghost" class="flex-1 h-16 rounded-2xl font-bold border-zinc-200 dark:border-white/10 order-2 md:order-1">Kembali</flux:button>
            <flux:button wire:click="submit" variant="primary" class="flex-[2] h-16 rounded-2xl font-black uppercase tracking-widest shadow-xl shadow-primary/30 !text-white hover:!bg-primary/90 order-1 md:order-2">
                <span>Konfirmasi Donasi</span>
                <flux:icon.arrow-right class="size-5 ms-2" />
            </flux:button>
        </div>
    </div>
    @endif
</div>
