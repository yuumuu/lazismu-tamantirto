<script>
    import { useForm, Link } from '@inertiajs/svelte';
    import Layout from '../../Layouts/GuestLayout.svelte';

    let {
        result = null,
    } = $props();

    let form = useForm({
        phone: '',
        token: '',
    });

    let errors = $state({});

    const categoryLabels = {
        health: 'Kesehatan',
        education: 'Pendidikan',
        business: 'Modal Usaha',
        basic_needs: 'Kebutuhan Pokok',
        other: 'Lainnya',
    };

    const statusConfig = {
        pending: { label: 'Menunggu', class: 'bg-amber-500/10 border-amber-500/20 text-amber-600' },
        approved: { label: 'Disetujui', class: 'bg-green-500/10 border-green-500/20 text-green-600' },
        rejected: { label: 'Ditolak', class: 'bg-red-500/10 border-red-500/20 text-red-600' },
    };

    const formatRupiah = (val) => {
        if (!val && val !== 0) return 'Rp 0';
        return new Intl.NumberFormat('id-ID', {
            style: 'currency', currency: 'IDR', maximumFractionDigits: 0,
        }).format(val);
    };

    function validate() {
        const errs = {};
        if (!form.phone?.trim()) errs.phone = 'Nomor WhatsApp wajib diisi';
        else if (!/^0\d{8,12}$/.test(form.phone.replace(/[\s-]/g, ''))) errs.phone = 'Format nomor tidak valid';
        if (!form.token?.trim()) errs.token = 'Token wajib diisi';
        else if (form.token.length < 8) errs.token = 'Token minimal 8 karakter';
        errors = errs;
        return Object.keys(errs).length === 0;
    }

    function submit() {
        if (!validate()) return;
        form.post('/cek-status');
    }

    function reset() {
        result = null;
        form.reset();
        errors = {};
    }

    const status = $derived(result ? statusConfig[result.status] || statusConfig.pending : null);
</script>

<svelte:head>
    <title>Cek Status Pengajuan | Lazismu</title>
</svelte:head>

<Layout>
    <div class="py-12 md:py-24 bg-zinc-50 dark:bg-zinc-950 min-h-screen">
        <div class="mx-auto max-w-4xl px-6 lg:px-8">
            <div class="space-y-8">
                <div class="text-center space-y-4">
                    <h1 class="text-4xl md:text-5xl font-black text-zinc-900 dark:text-white tracking-tight">Cek Status Pengajuan</h1>
                    <p class="text-lg text-zinc-500 font-medium max-w-2xl mx-auto">
                        Masukkan nomor WhatsApp dan token untuk melihat status pengajuan Anda
                    </p>
                </div>

                {#if result}
                    <!-- Result Card -->
                    <div class="bg-white dark:bg-zinc-900 rounded-[40px] border border-zinc-200 dark:border-white/5 shadow-2xl p-8 md:p-12" transition:fly={{ y: 20 }}>
                        <div class="space-y-8">
                            <!-- Status Header -->
                            <div class="flex items-center justify-between pb-6 border-b border-zinc-100 dark:border-white/5">
                                <div>
                                    <h2 class="text-2xl font-black text-zinc-900 dark:text-white mb-1">Detail Pengajuan</h2>
                                    <p class="text-sm text-zinc-500">Token: <span class="font-mono font-bold text-primary">{result.token}</span></p>
                                </div>
                                {#if status}
                                    <div class="px-5 py-2 rounded-full {status.class} border">
                                        <span class="text-xs font-black uppercase tracking-wider">{status.label}</span>
                                    </div>
                                {/if}
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-1">
                                    <span class="text-xs text-zinc-400 font-black uppercase tracking-widest">Pemohon</span>
                                    <p class="text-lg font-bold text-zinc-900 dark:text-white">{result.applicant_name}</p>
                                </div>
                                <div class="space-y-1">
                                    <span class="text-xs text-zinc-400 font-black uppercase tracking-widest">WhatsApp</span>
                                    <p class="text-lg font-bold text-zinc-900 dark:text-white">{result.applicant_phone}</p>
                                </div>
                                <div class="space-y-1">
                                    <span class="text-xs text-zinc-400 font-black uppercase tracking-widest">Judul</span>
                                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{result.title}</p>
                                </div>
                                <div class="space-y-1">
                                    <span class="text-xs text-zinc-400 font-black uppercase tracking-widest">Kategori</span>
                                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{categoryLabels[result.category] || result.category}</p>
                                </div>
                                <div class="space-y-1">
                                    <span class="text-xs text-zinc-400 font-black uppercase tracking-widest">Jumlah Dimohon</span>
                                    <p class="text-xl font-black text-primary">{formatRupiah(result.amount_requested)}</p>
                                </div>
                                <div class="space-y-1">
                                    <span class="text-xs text-zinc-400 font-black uppercase tracking-widest">Tanggal Pengajuan</span>
                                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{result.created_at}</p>
                                </div>
                            </div>

                            {#if result.description}
                                <div class="pt-6 border-t border-zinc-100 dark:border-white/5">
                                    <span class="text-xs text-zinc-400 font-black uppercase tracking-widest">Deskripsi</span>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-2">{result.description}</p>
                                </div>
                            {/if}

                            {#if result.admin_notes}
                                <div class="p-6 rounded-2xl bg-zinc-50 dark:bg-white/5 border border-zinc-100 dark:border-white/5">
                                    <span class="text-xs text-zinc-400 font-black uppercase tracking-widest">Catatan Admin</span>
                                    <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-2">{result.admin_notes}</p>
                                </div>
                            {/if}

                            <button type="button" onclick={reset}
                                class="w-full h-16 rounded-xl bg-primary text-white font-black uppercase tracking-widest shadow-xl shadow-primary/30 hover:bg-primary/90 transition flex items-center justify-center gap-2 cursor-pointer"
                            >
                                <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                Cek Pengajuan Lain
                            </button>
                        </div>
                    </div>
                {:else}
                    <!-- Form Card -->
                    <div class="bg-white dark:bg-zinc-900 rounded-[40px] border border-zinc-200 dark:border-white/5 shadow-2xl p-8 md:p-12">
                        <form onsubmit={(e) => { e.preventDefault(); submit(); }} class="space-y-8">
                            <div class="space-y-6">
                                <div class="space-y-2">
                                    <label for="check_phone" class="text-sm font-black text-zinc-400 uppercase tracking-widest">Nomor WhatsApp <span class="text-red-500">*</span></label>
                                    <input id="check_phone" type="tel" bind:value={form.phone} placeholder="Contoh: 08123456789"
                                        class="w-full h-14 rounded-xl border border-zinc-200 dark:border-white/10 bg-white dark:bg-zinc-800/50 px-4 font-bold text-zinc-900 dark:text-white outline-none focus:border-primary focus:ring-2 focus:ring-primary/10"
                                    />
                                    {#if errors.phone}<p class="text-xs text-red-500 font-bold">{errors.phone}</p>{/if}
                                </div>
                                <div class="space-y-2">
                                    <label for="check_token" class="text-sm font-black text-zinc-400 uppercase tracking-widest">Token <span class="text-red-500">*</span></label>
                                    <input id="check_token" type="text" bind:value={form.token} maxlength="8" placeholder="Masukkan 8 digit token"
                                        class="w-full h-14 rounded-xl border border-zinc-200 dark:border-white/10 bg-white dark:bg-zinc-800/50 px-4 font-bold text-zinc-900 dark:text-white outline-none focus:border-primary focus:ring-2 focus:ring-primary/10 tracking-[0.5em] text-center uppercase"
                                    />
                                    {#if errors.token}<p class="text-xs text-red-500 font-bold">{errors.token}</p>{/if}
                                </div>
                            </div>

                            <button type="submit" disabled={form.processing}
                                class="w-full h-16 rounded-xl bg-primary text-white font-black uppercase tracking-widest shadow-xl shadow-primary/30 hover:bg-primary/90 transition-all disabled:opacity-50 flex items-center justify-center gap-2 cursor-pointer"
                            >
                                {#if form.processing}
                                    <svg class="size-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                    Memproses...
                                {:else}
                                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                    Cek Status
                                {/if}
                            </button>
                        </form>
                    </div>

                    <!-- Info Card -->
                    <div class="bg-gradient-to-br from-primary/5 to-primary/10 dark:from-primary/10 dark:to-primary/20 rounded-[40px] border border-primary/20 p-8 md:p-12">
                        <div class="flex items-start gap-4">
                            <div class="size-12 rounded-xl bg-primary/20 flex items-center justify-center shrink-0">
                                <svg class="size-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-zinc-900 dark:text-white mb-2">Cara Cek Status</h3>
                                <ul class="space-y-2 text-sm text-zinc-600 dark:text-zinc-400">
                                    <li>• Masukkan nomor WhatsApp yang didaftarkan saat pengajuan.</li>
                                    <li>• Masukkan token 8 digit yang diterima setelah pengajuan berhasil.</li>
                                    <li>• Klik tombol "Cek Status" untuk melihat hasilnya.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                {/if}
            </div>
        </div>
    </div>
</Layout>
