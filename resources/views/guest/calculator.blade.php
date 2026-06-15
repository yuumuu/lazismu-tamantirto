<x-layouts.guest title="Kalkulator Zakat">
    @php
        $goldPrice = setting('zakat_gold_price', 1200000);
        $goldNisab = setting('zakat_gold_nisab', 85);
        $silverNisab = setting('zakat_silver_nisab', 595);

        $nisabMaal = $goldPrice * $goldNisab;
        $nisabProfesi = $nisabMaal / 12;
    @endphp

    <div x-data="{
        tab: 'profesi',
        goldPrice: {{ $goldPrice }},
        goldNisab: {{ $goldNisab }},
        income: 0,
        otherIncome: 0,
        maalAsset: 0,
        fitrahQty: 1,
        ricePrice: 15000,
    
        get nisabProfesi() { return this.goldNisab * this.goldPrice / 12 },
        get totalIncome() { return Number(this.income) + Number(this.otherIncome) },
        get zakatProfesi() {
            if (this.totalIncome >= this.nisabProfesi) return Math.floor(this.totalIncome * 0.025);
            return 0;
        },
    
        get nisabMaal() { return this.goldNisab * this.goldPrice },
        get zakatMaal() {
            if (this.maalAsset >= this.nisabMaal) return Math.floor(this.maalAsset * 0.025);
            return 0;
        },
    
        get zakatFitrah() { return this.fitrahQty * 2.5 * this.ricePrice },
    
        get currentZakat() {
            if (this.tab === 'profesi') return this.zakatProfesi;
            if (this.tab === 'maal') return this.zakatMaal;
            return this.zakatFitrah;
        },
    
        get donationUrl() {
            let type = 'zakat';
            let subtype = '';
    
            if (this.tab === 'profesi') {
                subtype = 'profesi';
            } else if (this.tab === 'maal') {
                subtype = 'maal';
            } else if (this.tab === 'fitrah') {
                subtype = 'fitrah';
            }
    
            return `{{ route('guest.donate.form') }}?amount=${this.currentZakat}&type=${type}&subtype=${subtype}&from=calculator`;
        }
    }" class="py-12 md:py-24 bg-zinc-50 dark:bg-zinc-950 min-h-screen">

        <div class="mx-auto max-w-4xl px-6 lg:px-8">
            <div class="text-center space-y-4 mb-16">
                <h2 class="text-primary font-black uppercase text-xs">Simulasi Zakat</h2>
                <h1 class="text-4xl md:text-5xl font-black text-zinc-900 dark:text-white tracking-tight">Kalkulator Zakat
                </h1>
                <p class="text-zinc-500 font-medium max-w-2xl mx-auto italic">"Ambillah zakat dari sebagian harta mereka,
                    dengan zakat itu kamu membersihkan dan mencucikan mereka." (QS. At-Taubah: 103)</p>
            </div>

            <div
                class="bg-white dark:bg-zinc-900 rounded-[48px] border border-zinc-200 dark:border-white/5 shadow-2xl overflow-hidden">
                <!-- Tabs -->
                <div class="flex border-b border-zinc-100 dark:border-white/5">
                    <button @click="tab = 'profesi'"
                        :class="tab === 'profesi' ? 'text-primary border-primary' : 'text-zinc-400 border-transparent'"
                        class="flex-1 py-6 font-black uppercase tracking-widest text-[10px] md:text-xs border-b-4 transition-all hover:text-primary">Zakat
                        Profesi</button>
                    <button @click="tab = 'maal'"
                        :class="tab === 'maal' ? 'text-primary border-primary' : 'text-zinc-400 border-transparent'"
                        class="flex-1 py-6 font-black uppercase tracking-widest text-[10px] md:text-xs border-b-4 transition-all hover:text-primary">Zakat
                        Maal</button>
                    <button @click="tab = 'fitrah'"
                        :class="tab === 'fitrah' ? 'text-primary border-primary' : 'text-zinc-400 border-transparent'"
                        class="flex-1 py-6 font-black uppercase tracking-widest text-[10px] md:text-xs border-b-4 transition-all hover:text-primary">Zakat
                        Fitrah</button>
                </div>

                <div class="p-8 md:p-12">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                        <!-- Inputs -->
                        <div class="space-y-8">
                            <template x-if="tab === 'profesi'">
                                <div class="space-y-6">
                                    <flux:input x-model="income" type="number" step="100000"
                                        label="Pendapatan per Bulan" prefix="Rp" placeholder="0"
                                        class="h-14 rounded-2xl font-bold" />
                                    <flux:input x-model="otherIncome" type="number" step="100000"
                                        label="Pendapatan Lainnya" prefix="Rp" placeholder="0"
                                        class="h-14 rounded-2xl font-bold" />
                                    <p class="text-[10px] text-zinc-400 font-bold italic">
                                        *Nisab Zakat Profesi setara {{ $goldNisab }}gr emas per tahun
                                        ({{ format_rupiah($nisabProfesi) }}/bulan).
                                    </p>
                                </div>
                            </template>

                            <template x-if="tab === 'maal'">
                                <div class="space-y-6">
                                    <flux:input x-model="maalAsset" type="number" step="1000000"
                                        label="Total Harta Simpanan (Emas, Perak, Uang)" prefix="Rp" placeholder="0"
                                        class="h-14 rounded-2xl font-bold" />
                                    <p class="text-[10px] text-zinc-400 font-bold italic">
                                        *Harta yang telah mencapai haul (1 tahun) dan nisab (setara
                                        {{ $goldNisab }}gr emas &asymp; {{ format_rupiah($nisabMaal) }}).
                                    </p>
                                </div>
                            </template>

                            <template x-if="tab === 'fitrah'">
                                <div class="space-y-6">
                                    <flux:input x-model="fitrahQty" type="number" label="Jumlah Jiwa" placeholder="1"
                                        class="h-14 rounded-2xl font-bold" />
                                    <flux:input x-model="ricePrice" type="number" label="Harga Beras per Kg"
                                        prefix="Rp" class="h-14 rounded-2xl font-bold" />
                                    <p class="text-[10px] text-zinc-400 font-bold italic">
                                        *Zakat Fitrah adalah 2.5kg beras per jiwa (atau setara nilai uang).
                                    </p>
                                </div>
                            </template>
                        </div>

                        <!-- Results -->
                        <div class="flex flex-col flex-1 min-w-0">
                            <div
                                class="bg-primary/5 dark:bg-primary/10 rounded-[32px] p-6 md:p-8 border border-primary/10 space-y-8 flex-1 flex flex-col justify-center min-w-0 overflow-hidden">
                                <div class="text-center space-y-2 min-w-0">
                                    <span class="text-[10px] text-zinc-400 font-black uppercase">Total
                                        Kewajiban Zakat</span>
                                    <div
                                        class="font-black text-primary tracking-tighter break-words text-3xl sm:text-4xl md:text-5xl lg:text-5xl">
                                        <span
                                            x-text="new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(currentZakat)"></span>
                                    </div>
                                </div>

                                <div class="pt-8 border-t border-primary/10">
                                    <template x-if="currentZakat > 0">
                                        <flux:button x-bind:href="donationUrl" variant="primary"
                                            class="w-full h-16 rounded-2xl font-black uppercase tracking-widest shadow-xl shadow-primary/30">
                                            Tunaikan Sekarang
                                        </flux:button>
                                    </template>
                                    <template x-if="currentZakat === 0 && tab !== 'fitrah'">
                                        <div class="p-4 rounded-xl bg-zinc-50 dark:bg-white/5 text-center">
                                            <p class="text-xs text-zinc-500 font-bold italic">Harta Anda belum mencapai
                                                nisab wajib zakat.</p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</x-layouts.guest>
