<script>
    import { useForm, page } from '@inertiajs/svelte';
    import Layout from '../../Layouts/GuestLayout.svelte';
    import { fade, fly } from 'svelte/transition';
    import Eye from 'lucide-svelte/icons/eye';
    import Check from 'lucide-svelte/icons/check';
    import Building from 'lucide-svelte/icons/building';
    import CreditCard from 'lucide-svelte/icons/credit-card';
    import ShieldCheck from 'lucide-svelte/icons/shield-check';

    let {
        campaign = null,
        bankAccounts = [],
        activeBranches = [],
        campaignTypes = [],
        calculatorAmount = null,
        calculatorType = null,
        calculatorSubtype = null,
        fromCalculator = false,
        qrisImage = null,
    } = $props();

    let step = $state(1);
    let branchId = $state(campaign?.branch_id || (activeBranches[0]?.id || 1));
    let donationType = $state(calculatorType || campaign?.type || 'infaq');
    let amount = $state(calculatorAmount ? Number(calculatorAmount) : '');
    let isSpecificCampaign = $state(!!campaign);
    let campaignId = $state(campaign?.id || null);
    let selectedCampaign = $state(campaign);
    let donorName = $state('');
    let donorPhone = $state('');
    let donorEmail = $state('');
    let donorMessage = $state('');
    let isAnonymous = $state(false);
    let errors = $state({});
    let submitting = $state(false);
    let submitError = $state('');
    let selectedBank = $state('');
    let paymentMethod = $state('manual');
    let branchSearch = $state('');
    let branchDropdownOpen = $state(false);
    let filteredBranches = $derived(
        branchSearch
            ? activeBranches.filter(b => b.name.toLowerCase().includes(branchSearch.toLowerCase()) || (b.city && b.city.toLowerCase().includes(branchSearch.toLowerCase())))
            : activeBranches
    );

    const amountPresets = [10000, 25000, 50000, 100000, 250000, 500000];
    let campaigns = $state([]);

    async function loadCampaigns() {
        try {
            const res = await fetch('/api/campaigns?active=1&limit=20');
            const json = await res.json();
            campaigns = json.data || json;
        } catch {
            campaigns = [];
        }
    }
    if (!campaign) loadCampaigns();

    const formatRupiah = (val) => {
        if (!val && val !== 0) return 'Rp 0';
        return new Intl.NumberFormat('id-ID', {
            style: 'currency', currency: 'IDR', maximumFractionDigits: 0
        }).format(val);
    };

    const donationTypeLabel = (subtype) => {
        if (subtype === 'profesi') return 'Zakat Profesi';
        if (subtype === 'maal') return 'Zakat Maal';
        if (subtype === 'fitrah') return 'Zakat Fitrah';
        return 'Zakat';
    };

    function setAmount(val) {
        amount = val;
        errors.amount = null;
    }

    function setDonationType(type) {
        donationType = type;
    }

    function selectGeneralDonation() {
        isSpecificCampaign = false;
        campaignId = null;
        selectedCampaign = null;
    }

    function selectSpecificCampaign() {
        isSpecificCampaign = true;
    }

    function selectCampaign(c) {
        campaignId = c.id;
        selectedCampaign = c;
        branchId = c.branch_id;
    }

    function validateStep() {
        const errs = {};
        if (step === 1) {
            if (!amount || amount < 10000) errs.amount = 'Minimal donasi adalah Rp 10.000';
            if (amount > 10000000) errs.amount = 'Maksimal donasi adalah Rp 10.000.000';
            if (isSpecificCampaign && !campaignId) errs.campaign_id = 'Silakan pilih program donasi';
        } else if (step === 2) {
            if (!donorName?.trim()) errs.donor_name = 'Nama lengkap wajib diisi';
            if (!donorPhone?.trim()) errs.donor_phone = 'Nomor WhatsApp wajib diisi';
            else if (!/^0\d{8,12}$/.test(donorPhone.replace(/[\s-]/g, ''))) errs.donor_phone = 'Format nomor tidak valid';
            if (donorEmail && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(donorEmail)) errs.donor_email = 'Format email tidak valid';
        }
        errors = errs;
        return Object.keys(errs).length === 0;
    }

    function nextStep() {
        if (validateStep()) step++;
    }

    function prevStep() {
        step--;
        errors = {};
    }

    let form = useForm({
        amount: 0,
        donation_type: 'infaq',
        branch_id: 1,
        campaign_id: null,
        name: '',
        phone: '',
        email: '',
        message: '',
        is_anonymous: false,
        payment_method: 'manual',
    });

    function submit() {
        if (step < 4) return;
        if (paymentMethod === 'manual' && !selectedBank && bankAccounts.length > 0) {
            submitError = 'Silakan pilih bank tujuan transfer.';
            return;
        }
        submitting = true;
        submitError = '';
        form.amount = Number(amount);
        form.donation_type = donationType;
        form.branch_id = branchId;
        form.campaign_id = campaignId;
        form.name = donorName;
        form.phone = donorPhone;
        form.email = donorEmail;
        form.message = donorMessage;
        form.is_anonymous = isAnonymous;
        form.payment_method = paymentMethod;
        form.post('/donasi/submit', {
            onSuccess: () => {
                submitting = false;
            },
            onError: (errs) => {
                submitting = false;
                submitError = Object.values(errs).join(', ');
            },
            onFinish: () => {
                submitting = false;
            },
        });
    }
</script>

<svelte:head>
    <title>{campaign ? `Donasi - ${campaign.title}` : 'Donasi Umum | Lazismu'}</title>
</svelte:head>

<Layout>
    <div class="py-12 md:py-24 bg-zinc-50 dark:bg-zinc-950 min-h-screen">
        <div class="mx-auto max-w-4xl px-6 lg:px-8">
            <div class="space-y-8">
                <!-- Stepper Progress -->
                <div class="mb-12">
                    <div class="flex items-center justify-between">
                        {#each [
                            { num: 1, label: 'Nominal', icon: Building },
                            { num: 2, label: 'Profil', icon: Eye },
                            { num: 3, label: 'Pembayaran', icon: CreditCard },
                            { num: 4, label: 'Konfirmasi', icon: ShieldCheck },
                        ] as s}
                            <div class="flex flex-col items-center gap-2 relative flex-1">
                                <div class="size-12 rounded-xl flex items-center justify-center transition-all duration-500 {step > s.num ? 'bg-green-500 text-white shadow-lg shadow-green-500/30' : step === s.num ? 'bg-primary text-white shadow-xl shadow-primary/30' : 'bg-zinc-200 dark:bg-zinc-800 text-zinc-500'}">
                                    {#if step > s.num}
                                        <Check class="size-6" />
                                    {:else}
                                        <span class="font-black text-lg">{s.num}</span>
                                    {/if}
                                </div>
                                <span class="text-[10px] font-black uppercase text-center {step >= s.num ? 'text-primary' : 'text-zinc-400'}">{s.label}</span>
                                {#if s.num < 4}
                                    <div class="absolute top-6 left-[60%] w-full h-0.5 bg-zinc-200 dark:bg-zinc-800 -z-10 {step > s.num ? '!bg-green-500' : ''}"></div>
                                {/if}
                            </div>
                        {/each}
                    </div>
                </div>

                <!-- Calculator Banner -->
                {#if fromCalculator && calculatorSubtype}
                    <div class="p-6 rounded-[28px] bg-gradient-to-r from-primary/10 to-primary/5 border border-primary/20 flex items-center gap-4" transition={fade}>
                        <div class="size-12 rounded-xl bg-primary/20 flex items-center justify-center text-primary">
                            <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-black text-zinc-900 dark:text-white text-sm mb-1">Dari Kalkulator Zakat</h4>
                            <p class="text-xs text-zinc-600 dark:text-zinc-400 font-medium">
                                Nominal {donationTypeLabel(calculatorSubtype)} Anda: <span class="font-black text-primary">{formatRupiah(calculatorAmount)}</span>
                            </p>
                        </div>
                        <a href="/zakat-kalkulator" class="text-xs text-primary font-bold hover:underline">Hitung Ulang</a>
                    </div>
                {/if}

                <!-- Main Card -->
                <div class="bg-white dark:bg-zinc-900 rounded-[40px] border border-zinc-200 dark:border-white/5 shadow-2xl p-8 md:p-12">
                    {#if step === 1}
                        <div class="space-y-10" transition={fade}>
                            <div class="text-center space-y-2">
                                <h2 class="text-3xl font-black text-zinc-900 dark:text-white tracking-tight">Pilih Nominal Donasi</h2>
                                <p class="text-zinc-500 font-medium">Berapa banyak kebaikan yang ingin Anda alirkan hari ini?</p>
                            </div>

                            <!-- Campaign Display -->
                            {#if campaign}
                                <div class="p-6 rounded-[24px] bg-zinc-50 dark:bg-white/5 border border-zinc-100 dark:border-white/5 flex items-center gap-6">
                                    <img src={campaign.featured_image || 'https://images.unsplash.com/photo-1532629345422-7515f3d16bb6?q=80&w=2070&auto=format&fit=crop'}
                                        class="size-20 rounded-2xl object-cover" alt={campaign.title}>
                                    <div class="flex-1 space-y-1">
                                        <span class="text-[10px] text-zinc-400 font-black uppercase tracking-widest">{campaign.category?.name || 'Program'}</span>
                                        <h4 class="text-lg font-black text-zinc-900 dark:text-white leading-tight line-clamp-1">{campaign.title}</h4>
                                    </div>
                                </div>
                            {/if}

                            <!-- Branch Selection -->
                            <div class="space-y-4 relative">
                                <div class="flex items-center justify-between">
                                    <label for="branch_search" class="text-sm font-black text-zinc-400 uppercase tracking-widest">Pilih Cabang Penyalur</label>
                                </div>
                                <div class="relative">
                                    <input id="branch_search" type="text" bind:value={branchSearch} onfocus={() => (branchDropdownOpen = true)} onblur={() => setTimeout(() => (branchDropdownOpen = false), 200)} placeholder="Cari cabang..."
                                        class="w-full h-14 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-white/10 px-4 font-bold text-zinc-900 dark:text-white outline-none focus:border-primary focus:ring-2 focus:ring-primary/10"
                                    />
                                    {#if branchDropdownOpen}
                                        <div class="absolute z-10 mt-1 w-full max-h-48 overflow-y-auto rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-white/10 shadow-xl">
                                            {#each filteredBranches as branch}
                                                <button type="button" onclick={() => { branchId = branch.id; branchSearch = ''; branchDropdownOpen = false; }}
                                                    class="w-full px-4 py-3 text-left font-bold text-zinc-900 dark:text-white hover:bg-zinc-50 dark:hover:bg-white/5 transition {branchId === branch.id ? 'bg-primary/5 text-primary' : ''}"
                                                >
                                                    {branch.name}{branch.city ? ` - ${branch.city}` : ''}
                                                </button>
                                            {/each}
                                            {#if filteredBranches.length === 0}
                                                <p class="px-4 py-3 text-sm text-zinc-400">Cabang tidak ditemukan</p>
                                            {/if}
                                        </div>
                                    {/if}
                                </div>
                            </div>

                            <!-- Donation Type -->
                            <div role="group" aria-labelledby="group-donation-type" class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <span id="group-donation-type" class="text-sm font-black text-zinc-400 uppercase tracking-widest">Jenis Donasi</span>
                                    {#if fromCalculator && calculatorType === 'zakat'}
                                        <span class="text-xs text-primary font-bold">Dipilih dari kalkulator</span>
                                    {/if}
                                </div>
                                <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
                                    {#each campaignTypes as type}
                                        <button type="button" onclick={() => setDonationType(type.value)}
                                            class="p-4 rounded-2xl border-2 transition-all flex flex-col items-center gap-3 {donationType === type.value ? 'border-primary bg-primary/5' : 'border-zinc-100 dark:border-white/5 hover:border-primary/50'}"
                                        >
                                            <span class="text-xs font-black uppercase tracking-tight {donationType === type.value ? 'text-primary' : 'text-zinc-600 dark:text-zinc-400'}">
                                                {type.label}
                                            </span>
                                        </button>
                                    {/each}
                                </div>
                            </div>

                            <!-- Amount Presets -->
                            <div role="group" aria-labelledby="group-amount-presets" class="space-y-4">
                                <span id="group-amount-presets" class="text-sm font-black text-zinc-400 uppercase tracking-widest">Pilih Nominal</span>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                    {#each amountPresets as preset}
                                        <button type="button" onclick={() => setAmount(preset)}
                                            class="p-6 rounded-2xl border-2 transition-all text-center {amount === preset ? 'border-primary bg-primary/5' : 'border-zinc-100 dark:border-white/5 hover:border-primary/50'}"
                                        >
                                            <span class="block text-lg font-black {amount === preset ? 'text-primary' : 'text-zinc-600 dark:text-zinc-400'}">
                                                {formatRupiah(preset)}
                                            </span>
                                        </button>
                                    {/each}
                                </div>
                            </div>

                            <!-- Custom Amount -->
                            <div class="space-y-4">
                                <label for="custom_amount" class="block text-sm font-black text-zinc-400 uppercase tracking-widest mb-2">Nominal Kustom (Rp)</label>
                                <input id="custom_amount" type="number" bind:value={amount} min="10000" step="1000"
                                    class="w-full h-16 rounded-xl border border-zinc-200 dark:border-white/10 bg-zinc-50 dark:bg-zinc-800/50 px-6 text-xl font-black text-zinc-900 dark:text-white outline-none focus:border-primary focus:ring-2 focus:ring-primary/10"
                                    placeholder="Masukkan jumlah lainnya..."
                                />
                                {#if errors.amount}
                                    <p class="text-xs text-red-500 font-bold">{errors.amount}</p>
                                {/if}
                            </div>

                            <!-- Donation Target -->
                            <div role="group" aria-labelledby="group-donation-target" class="space-y-4">
                                <span id="group-donation-target" class="text-sm font-black text-zinc-400 uppercase tracking-widest">Tujuan Donasi</span>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <button type="button" onclick={selectGeneralDonation}
                                        class="p-6 rounded-2xl border-2 transition-all flex flex-col items-center gap-2 {!isSpecificCampaign ? 'border-primary bg-primary/5' : 'border-zinc-100 dark:border-white/5 hover:border-primary/50'}"
                                    >
                                        <span class="font-black {!isSpecificCampaign ? 'text-primary' : 'text-zinc-600 dark:text-zinc-400'}">Donasi Umum</span>
                                        <p class="text-[10px] text-zinc-400 font-medium text-center">Donasi akan disalurkan oleh Lazismu ke yang paling membutuhkan.</p>
                                    </button>
                                    <button type="button" onclick={selectSpecificCampaign}
                                        class="p-6 rounded-2xl border-2 transition-all flex flex-col items-center gap-2 {isSpecificCampaign ? 'border-primary bg-primary/5' : 'border-zinc-100 dark:border-white/5 hover:border-primary/50'}"
                                    >
                                        <span class="font-black {isSpecificCampaign ? 'text-primary' : 'text-zinc-600 dark:text-zinc-400'}">Program Spesifik</span>
                                        <p class="text-[10px] text-zinc-400 font-medium text-center">Donasi Anda akan disalurkan ke program spesifik pilihan Anda.</p>
                                    </button>
                                </div>
                            </div>

                            <!-- Campaign Selection -->
                            {#if isSpecificCampaign && !campaign}
                                <div role="group" aria-labelledby="group-campaign-select" class="pt-8 border-t border-zinc-100 dark:border-white/5 space-y-4" transition={fade}>
                                    <span id="group-campaign-select" class="text-sm font-black text-zinc-400 uppercase tracking-widest">Program Terpilih</span>
                                    <div class="space-y-3 max-h-64 overflow-y-auto">
                                        {#each campaigns as c}
                                            <button type="button" onclick={() => selectCampaign(c)}
                                                class="w-full p-4 rounded-2xl border-2 text-left transition-all flex items-center gap-4 {campaignId === c.id ? 'border-primary bg-primary/5' : 'border-zinc-100 dark:border-white/5 hover:border-primary/50'}"
                                            >
                                                <div class="{campaignId === c.id ? 'size-6 rounded-full bg-primary flex items-center justify-center' : 'size-6 rounded-full border-2 border-zinc-300 dark:border-zinc-600'} flex-shrink-0">
                                                    {#if campaignId === c.id}
                                                        <svg class="size-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                    {/if}
                                                </div>
                                                <div class="flex-1">
                                                    <p class="font-bold text-zinc-900 dark:text-white text-sm">{c.title}</p>
                                                </div>
                                            </button>
                                        {/each}
                                        {#if campaigns.length === 0}
                                            <p class="text-xs text-zinc-500 italic text-center">Memuat program...</p>
                                        {/if}
                                    </div>
                                    {#if errors.campaign_id}
                                        <p class="text-xs text-red-500 font-bold">{errors.campaign_id}</p>
                                    {/if}
                                </div>
                            {/if}

                            <button type="button" onclick={nextStep}
                                class="w-full h-16 rounded-xl bg-primary text-white font-black uppercase tracking-widest shadow-xl shadow-primary/30 hover:bg-primary/90 transition"
                            >
                                Lanjutkan ke Profil
                            </button>
                        </div>
                    {/if}

                    {#if step === 2}
                        <div class="space-y-10" transition={fade}>
                            <div class="text-center space-y-2">
                                <h2 class="text-3xl font-black text-zinc-900 dark:text-white tracking-tight">Informasi Donatur</h2>
                                <p class="text-zinc-500 font-medium">Lengkapi sedikit data diri Anda untuk riwayat donasi.</p>
                            </div>

                            <div class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="space-y-2">
                                        <label for="donor_name" class="text-sm font-black text-zinc-400 uppercase tracking-widest">Nama Lengkap</label>
                                        <input id="donor_name" type="text" bind:value={donorName} placeholder="Masukkan nama Anda..."
                                            class="w-full h-14 rounded-xl border border-zinc-200 dark:border-white/10 bg-white dark:bg-zinc-800/50 px-4 font-bold text-zinc-900 dark:text-white outline-none focus:border-primary focus:ring-2 focus:ring-primary/10"
                                        />
                                        {#if errors.donor_name}<p class="text-xs text-red-500 font-bold">{errors.donor_name}</p>{/if}
                                    </div>
                                    <div class="space-y-2">
                                        <label for="donor_phone" class="text-sm font-black text-zinc-400 uppercase tracking-widest">Nomor WhatsApp</label>
                                        <input id="donor_phone" type="tel" bind:value={donorPhone} placeholder="Contoh: 08123456789"
                                            class="w-full h-14 rounded-xl border border-zinc-200 dark:border-white/10 bg-white dark:bg-zinc-800/50 px-4 font-bold text-zinc-900 dark:text-white outline-none focus:border-primary focus:ring-2 focus:ring-primary/10"
                                        />
                                        {#if errors.donor_phone}<p class="text-xs text-red-500 font-bold">{errors.donor_phone}</p>{/if}
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label for="donor_email" class="text-sm font-black text-zinc-400 uppercase tracking-widest">Email</label>
                                    <input id="donor_email" type="email" bind:value={donorEmail} placeholder="nama@email.com"
                                        class="w-full h-14 rounded-xl border border-zinc-200 dark:border-white/10 bg-white dark:bg-zinc-800/50 px-4 font-bold text-zinc-900 dark:text-white outline-none focus:border-primary focus:ring-2 focus:ring-primary/10"
                                    />
                                    {#if errors.donor_email}<p class="text-xs text-red-500 font-bold">{errors.donor_email}</p>{/if}
                                </div>
                                <div class="space-y-2">
                                    <label for="donor_message" class="text-sm font-black text-zinc-400 uppercase tracking-widest">Pesan & Doa (Opsional)</label>
                                    <textarea id="donor_message" bind:value={donorMessage} rows="3" placeholder="Tuliskan pesan atau doa Anda..."
                                        class="w-full rounded-2xl border border-zinc-200 dark:border-white/10 bg-white dark:bg-zinc-800/50 px-4 py-3 text-sm font-medium text-zinc-900 dark:text-white outline-none focus:border-primary focus:ring-2 focus:ring-primary/10"
                                    ></textarea>
                                </div>
                                <div class="p-6 rounded-2xl bg-zinc-50 dark:bg-white/5 border border-zinc-100 dark:border-white/5 flex items-center gap-4">
                                    <input type="checkbox" id="is_anonymous" bind:checked={isAnonymous}
                                        class="h-5 w-5 rounded border-zinc-300 text-primary focus:ring-primary"
                                    />
                                    <label for="is_anonymous" class="cursor-pointer">
                                        <span class="font-bold text-zinc-900 dark:text-white text-sm">Sembunyikan Nama Saya</span>
                                        <p class="text-xs text-zinc-500 font-medium">Nama akan ditampilkan sebagai 'Hamba Allah' di publik.</p>
                                    </label>
                                </div>
                            </div>

                            <div class="flex gap-4">
                                <button type="button" onclick={prevStep}
                                    class="flex-1 h-16 rounded-xl font-bold border-2 border-zinc-200 dark:border-white/10 bg-transparent text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-white/5 transition"
                                >
                                    Kembali
                                </button>
                                <button type="button" onclick={() => { if (validateStep()) step = 3; }}
                                    class="flex-[2] h-16 rounded-xl bg-primary text-white font-black uppercase tracking-widest shadow-xl shadow-primary/30 hover:bg-primary/90 transition"
                                >
                                    Lanjutkan ke Pembayaran
                                </button>
                            </div>
                        </div>
                    {/if}

                    {#if step === 3}
                        <div class="space-y-10" transition={fade}>
                            <div class="text-center space-y-2">
                                <h2 class="text-3xl font-black text-zinc-900 dark:text-white tracking-tight">Pilih Metode Pembayaran</h2>
                                <p class="text-zinc-500 font-medium">Ringkasan donasi Anda.</p>
                            </div>

                            <!-- Summary -->
                            <div class="p-6 rounded-2xl bg-zinc-50 dark:bg-white/5 border border-zinc-100 dark:border-white/5 space-y-4">
                                <div class="flex justify-between items-center py-2">
                                    <span class="text-sm font-medium text-zinc-500">Donasi</span>
                                    <span class="font-black text-zinc-900 dark:text-white">{donationTypeLabel(donationType)}</span>
                                </div>
                                {#if selectedCampaign}
                                    <div class="flex justify-between items-center py-2 border-t border-zinc-200 dark:border-white/10">
                                        <span class="text-sm font-medium text-zinc-500">Program</span>
                                        <span class="font-bold text-zinc-900 dark:text-white text-right max-w-[200px]">{selectedCampaign.title}</span>
                                    </div>
                                {/if}
                                <div class="flex justify-between items-center py-2 border-t border-zinc-200 dark:border-white/10">
                                    <span class="text-sm font-medium text-zinc-500">Nominal</span>
                                    <span class="font-black text-2xl text-primary">{formatRupiah(amount)}</span>
                                </div>
                            </div>

                            <!-- Bank Selection -->
                            <div role="group" aria-labelledby="group-transfer-bank" class="space-y-4">
                                <span id="group-transfer-bank" class="text-sm font-black text-zinc-400 uppercase tracking-widest">Transfer Bank</span>
                                <div class="space-y-3">
                                    {#each bankAccounts as bank}
                                        <button type="button" onclick={() => (selectedBank = bank)}
                                            class="w-full p-5 rounded-2xl border-2 text-left transition-all {selectedBank?.account_number === bank.account_number ? 'border-primary bg-primary/5' : 'border-zinc-100 dark:border-white/5 hover:border-primary/50'}"
                                        >
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <p class="font-black text-zinc-900 dark:text-white">{bank.bank_name}</p>
                                                    <p class="text-sm text-zinc-500 font-bold mt-1">{bank.account_number}</p>
                                                    <p class="text-xs text-zinc-400 mt-0.5">a.n. {bank.account_name}</p>
                                                </div>
                                                {#if selectedBank?.account_number === bank.account_number}
                                                    <div class="size-8 rounded-full bg-primary flex items-center justify-center">
                                                        <Check class="size-5 text-white" />
                                                    </div>
                                                {/if}
                                            </div>
                                        </button>
                                    {/each}
                                    {#if bankAccounts.length === 0}
                                        <p class="text-sm text-zinc-500 italic">Info rekening akan muncul setelah admin mengaturnya.</p>
                                    {/if}
                                </div>
                            </div>

                            <!-- E-Wallet / QRIS -->
                            {#if qrisImage}
                                <div role="group" aria-labelledby="group-qris" class="space-y-4">
                                    <span id="group-qris" class="text-sm font-black text-zinc-400 uppercase tracking-widest">E-Wallet / QRIS</span>
                                    <button type="button" onclick={() => { paymentMethod = 'qris'; selectedBank = ''; }}
                                        class="w-full p-5 rounded-2xl border-2 text-left transition-all {paymentMethod === 'qris' ? 'border-primary bg-primary/5' : 'border-zinc-100 dark:border-white/5 hover:border-primary/50'}"
                                    >
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-4">
                                                <div class="size-12 rounded-xl bg-primary/10 flex items-center justify-center">
                                                    <svg class="size-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                                </div>
                                                <div>
                                                    <p class="font-black text-zinc-900 dark:text-white">QRIS</p>
                                                    <p class="text-xs text-zinc-500 font-medium">Bayar via Scan QRIS</p>
                                                </div>
                                            </div>
                                            {#if paymentMethod === 'qris'}
                                                <div class="size-8 rounded-full bg-primary flex items-center justify-center">
                                                    <Check class="size-5 text-white" />
                                                </div>
                                            {/if}
                                        </div>
                                    </button>
                                    {#if paymentMethod === 'qris'}
                                        <div class="p-6 rounded-2xl bg-white dark:bg-zinc-800/50 border border-zinc-200 dark:border-white/10 flex flex-col items-center gap-4">
                                            <img src={qrisImage} alt="QRIS Code" class="size-48 object-contain rounded-xl" />
                                            <p class="text-sm text-zinc-500 font-medium text-center">Scan QRIS di atas menggunakan aplikasi e-wallet atau mobile banking Anda.</p>
                                        </div>
                                    {/if}
                                </div>
                            {/if}

                            <div class="flex gap-4">
                                <button type="button" onclick={prevStep}
                                    class="flex-1 h-16 rounded-xl font-bold border-2 border-zinc-200 dark:border-white/10 bg-transparent text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-white/5 transition"
                                >
                                    Kembali
                                </button>
                                <button type="button" onclick={() => { step = 4; errors = {}; }}
                                    class="flex-[2] h-16 rounded-xl bg-primary text-white font-black uppercase tracking-widest shadow-xl shadow-primary/30 hover:bg-primary/90 transition"
                                >
                                    Lanjutkan ke Konfirmasi
                                </button>
                            </div>
                        </div>
                    {/if}

                    {#if step === 4}
                        <div class="space-y-10" transition={fade}>
                            <div class="text-center space-y-2">
                                <h2 class="text-3xl font-black text-zinc-900 dark:text-white tracking-tight">Konfirmasi Donasi</h2>
                                <p class="text-zinc-500 font-medium">Periksa kembali data donasi Anda sebelum mengirim.</p>
                            </div>

                            <!-- Full Summary -->
                            <div class="space-y-4">
                                <div class="p-6 rounded-2xl bg-zinc-50 dark:bg-white/5 border border-zinc-100 dark:border-white/5 space-y-4">
                                    <div class="flex justify-between items-center py-2">
                                        <span class="text-sm font-medium text-zinc-500">Jenis Donasi</span>
                                        <span class="font-black text-zinc-900 dark:text-white">{donationTypeLabel(donationType)}</span>
                                    </div>
                                    {#if selectedCampaign}
                                        <div class="flex justify-between items-center py-2 border-t border-zinc-200 dark:border-white/10">
                                            <span class="text-sm font-medium text-zinc-500">Program</span>
                                            <span class="font-bold text-zinc-900 dark:text-white text-right max-w-[250px]">{selectedCampaign.title}</span>
                                        </div>
                                    {/if}
                                    <div class="flex justify-between items-center py-2 border-t border-zinc-200 dark:border-white/10">
                                        <span class="text-sm font-medium text-zinc-500">Nominal</span>
                                        <span class="font-black text-3xl text-primary">{formatRupiah(amount)}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-2 border-t border-zinc-200 dark:border-white/10">
                                        <span class="text-sm font-medium text-zinc-500">Donatur</span>
                                        <span class="font-bold text-zinc-900 dark:text-white">{isAnonymous ? 'Hamba Allah' : donorName}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-2 border-t border-zinc-200 dark:border-white/10">
                                        <span class="text-sm font-medium text-zinc-500">Pembayaran</span>
                                        <span class="font-bold text-zinc-900 dark:text-white">{selectedBank ? `${selectedBank.bank_name} - ${selectedBank.account_number}` : 'Belum dipilih'}</span>
                                    </div>
                                </div>
                            </div>

                            {#if submitError}
                                <div class="p-4 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                                    <p class="text-sm font-bold text-red-600">{submitError}</p>
                                </div>
                            {/if}

                            <div class="flex gap-4">
                                <button type="button" onclick={() => { step = 3; errors = {}; }}
                                    class="flex-1 h-16 rounded-xl font-bold border-2 border-zinc-200 dark:border-white/10 bg-transparent text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-white/5 transition"
                                >
                                    Kembali
                                </button>
                                <button type="button" onclick={submit} disabled={submitting}
                                    class="flex-[2] h-16 rounded-xl bg-primary text-white font-black uppercase tracking-widest shadow-xl shadow-primary/30 hover:bg-primary/90 transition disabled:opacity-50 flex items-center justify-center gap-3"
                                >
                                    {#if submitting}
                                        <svg class="animate-spin size-5" viewBox="0 0 24 24" fill="none">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                                        </svg>
                                        Memproses...
                                    {:else}
                                        Konfirmasi & Kirim
                                    {/if}
                                </button>
                            </div>
                        </div>
                    {/if}
                </div>
            </div>
        </div>
    </div>
</Layout>

