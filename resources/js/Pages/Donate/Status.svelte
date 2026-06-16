<script>
    import { useForm } from '@inertiajs/svelte';
    import { Link } from '@inertiajs/svelte';
    import Layout from '../../Layouts/GuestLayout.svelte';
    import Camera from 'lucide-svelte/icons/camera';
    import Trash from 'lucide-svelte/icons/trash';
    import DocumentDuplicate from 'lucide-svelte/icons/copy';

    let {
        donation = null,
        bankAccounts = [],
        qrisImage = null,
        siteName = 'Lazismu Tamantirto',
    } = $props();

    let previewUrl = $state(null);
    let errors = $state({});

    let form = useForm({ proof_image: null });

    const formatRupiah = (val) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency', currency: 'IDR', maximumFractionDigits: 0
        }).format(val || 0);
    };

    function handleFileSelect(e) {
        const file = e.target?.files?.[0];
        if (!file) return;
        form.proof_image = file;
        previewUrl = URL.createObjectURL(file);
        errors.proof_image = null;
    }

    function removeFile() {
        form.proof_image = null;
        previewUrl = null;
    }

    function submit() {
        if (!form.proof_image) {
            errors.proof_image = 'Silakan pilih file bukti transfer';
            return;
        }
        form.post(`/donasi/upload-proof/${donation.id}`);
    }

    let copied = $state(false);
    async function copyToClipboard(text) {
        try {
            await navigator.clipboard.writeText(text);
            copied = true;
            setTimeout(() => copied = false, 2000);
        } catch {}
    }
</script>

<svelte:head><title>Upload Bukti Pembayaran | Lazismu</title></svelte:head>

<Layout>
    <div class="py-12 md:py-24 bg-gradient-to-b from-primary/5 via-white to-zinc-50 dark:from-primary/10 dark:via-zinc-950 dark:to-zinc-950 min-h-screen">
        <div class="mx-auto max-w-4xl px-6 lg:px-8">
            <div class="space-y-8">
                <!-- Payment Instructions -->
                <div class="bg-white dark:bg-zinc-900 rounded-[40px] border border-zinc-200 dark:border-white/5 shadow-2xl overflow-hidden">
                    <div class="p-8 md:p-12 space-y-10">
                        <div class="text-center space-y-2">
                            <h2 class="text-3xl font-black text-zinc-900 dark:text-white tracking-tight">Menunggu Pembayaran</h2>
                            <p class="text-zinc-500 font-medium">Silakan selesaikan pembayaran sesuai instruksi di bawah.</p>
                        </div>

                        {#if donation.payment_method === 'qris'}
                            <div class="flex flex-col items-center gap-8">
                                <div class="p-6 bg-white rounded-2xl shadow-xl border border-zinc-100 flex flex-col items-center gap-4">
                                    <img src={qrisImage || 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=LAZISMU-TAMANTIRTO'}
                                        class="size-64 object-contain" alt="QRIS" />
                                    <span class="text-xs font-black text-zinc-400 uppercase tracking-widest">Pindai Dengan E-Wallet Apa Saja</span>
                                </div>
                                <div class="text-center">
                                    <p class="text-lg font-black text-zinc-900 dark:text-white">QRIS {siteName}</p>
                                </div>
                            </div>
                        {:else}
                            <div class="space-y-4">
                                {#each bankAccounts as bank}
                                    <div class="p-6 rounded-2xl bg-zinc-50 dark:bg-white/5 border border-zinc-100 dark:border-white/5 flex flex-col md:flex-row md:items-center justify-between gap-6">
                                        <div class="flex items-center gap-4">
                                            <div class="size-14 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-100 dark:border-white/5 flex items-center justify-center font-black text-primary text-sm uppercase">
                                                {bank.bank_name?.substring(0, 3)}
                                            </div>
                                            <div>
                                                <h4 class="font-black text-zinc-900 dark:text-white tracking-tight">{bank.bank_name}</h4>
                                                <p class="text-xs text-zinc-500 font-medium">{bank.account_name}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <span class="text-xl font-mono font-black text-zinc-900 dark:text-white">{bank.account_number}</span>
                                            <button onclick={() => copyToClipboard(bank.account_number)}
                                                class="p-2 hover:bg-zinc-200 dark:hover:bg-white/10 rounded-xl text-zinc-400 transition-colors cursor-pointer"
                                            >
                                                <DocumentDuplicate class="size-5" />
                                            </button>
                                        </div>
                                    </div>
                                {/each}
                            </div>
                        {/if}

                        <!-- Summary -->
                        <div class="p-8 rounded-[32px] bg-primary/5 dark:bg-primary/10 border border-primary/10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                            <div>
                                <span class="text-[10px] text-zinc-400 font-black uppercase">Total Nominal</span>
                                <h3 class="text-4xl font-black text-primary tracking-tighter">{formatRupiah(donation.amount)}</h3>
                            </div>
                            <Link href={`/donasi/konfirmasi/${donation.id}`}
                                class="inline-flex items-center justify-center px-6 py-3 rounded-2xl font-bold text-zinc-500 border-2 border-zinc-200 dark:border-white/10 hover:bg-zinc-50 dark:hover:bg-white/5 transition-all"
                            >
                                Detail Donasi
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Upload Form -->
                <div class="bg-white dark:bg-zinc-900 rounded-[40px] border border-zinc-200 dark:border-white/5 shadow-2xl p-8 md:p-12 space-y-8">
                    <div class="text-center space-y-2">
                        <h3 class="text-2xl font-black text-zinc-900 dark:text-white tracking-tight">Konfirmasi Pembayaran</h3>
                        <p class="text-sm text-zinc-500 font-medium italic">Sudah membayar? Unggah bukti transfer Anda di sini.</p>
                    </div>

                    <form onsubmit={(e) => { e.preventDefault(); submit(); }} class="space-y-6">
                        <div class="space-y-4">
                            {#if previewUrl}
                                <div class="relative w-full aspect-video rounded-2xl overflow-hidden border border-zinc-200 dark:border-white/5 group">
                                    <img src={previewUrl} class="size-full object-cover" alt="Preview" />
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <button type="button" onclick={removeFile}
                                            class="p-4 bg-red-500 text-white rounded-2xl hover:bg-red-600 shadow-xl transition-transform hover:scale-110 cursor-pointer"
                                        >
                                            <Trash class="size-6" />
                                        </button>
                                    </div>
                                </div>
                            {:else}
                                <label class="flex flex-col items-center justify-center w-full aspect-video rounded-2xl border-2 border-dashed border-zinc-200 dark:border-white/5 hover:border-primary transition-all cursor-pointer bg-zinc-50 dark:bg-zinc-950 group">
                                    <div class="p-6 rounded-2xl bg-white dark:bg-zinc-900 shadow-xl mb-6 group-hover:scale-110 transition-transform">
                                        <Camera class="size-10 text-primary" />
                                    </div>
                                    <span class="text-sm font-bold text-zinc-900 dark:text-white group-hover:text-primary transition-colors">Klik untuk Unggah Bukti</span>
                                    <span class="text-[10px] text-zinc-400 mt-2 uppercase tracking-widest">PNG, JPG, WEBP (Max 2MB)</span>
                                    <input type="file" onchange={handleFileSelect} class="hidden" accept="image/*" />
                                </label>
                            {/if}
                            {#if errors.proof_image}
                                <p class="text-xs text-red-500 font-bold mt-2 text-center">{errors.proof_image}</p>
                            {/if}
                        </div>

                        <button type="submit" disabled={form.processing}
                            class="w-full h-16 rounded-xl bg-primary text-white font-black uppercase tracking-widest shadow-xl shadow-primary/30 hover:bg-primary/90 transition-all disabled:opacity-50 flex items-center justify-center gap-2 cursor-pointer"
                        >
                            {#if form.processing}
                                Memproses...
                            {:else}
                                Konfirmasi Pembayaran
                            {/if}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</Layout>

