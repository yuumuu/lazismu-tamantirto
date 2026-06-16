<script>
    import { Link } from '@inertiajs/svelte';
    import Layout from '../../Layouts/GuestLayout.svelte';

    let {
        donation = null,
        bankAccounts = [],
        qrisImage = null,
        siteName = 'Lazismu Tamantirto',
        contactWhatsapp = '6281234567890',
    } = $props();

    const campaign = donation?.campaign;

    const formatRupiah = (val) => {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency', currency: 'IDR', maximumFractionDigits: 0
        }).format(val || 0);
    };

    let copied = $state(false);

    async function copyToClipboard(text) {
        try {
            await navigator.clipboard.writeText(text);
            copied = true;
            setTimeout(() => copied = false, 2000);
        } catch {
            alert('Gagal menyalin. Silakan salin manual.');
        }
    }
</script>

<svelte:head>
    <title>Terima Kasih - Donasi Berhasil | Lazismu</title>
</svelte:head>

<Layout>
    <div class="py-12 md:py-24 bg-gradient-to-b from-primary/5 via-white to-zinc-50 dark:from-primary/10 dark:via-zinc-950 dark:to-zinc-950 min-h-screen">
        <div class="mx-auto max-w-4xl px-6 lg:px-8">

            <!-- Success Animation & Message -->
            <div class="text-center space-y-8 mb-16">
                <div class="inline-flex items-center justify-center size-24 rounded-full bg-green-500/10 border-4 border-green-500/20">
                    <svg class="size-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>

                <div class="space-y-4">
                    <h1 class="text-4xl md:text-5xl font-black text-zinc-900 dark:text-white tracking-tight">
                        Jazakallahu Khairan!
                    </h1>
                    <p class="text-xl text-zinc-600 dark:text-zinc-400 font-medium max-w-2xl mx-auto">
                        Terima kasih atas kepercayaan Anda menyalurkan {donation.donation_type} melalui {siteName}
                    </p>
                </div>

                <div class="p-6 rounded-[32px] bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-white/5 max-w-2xl mx-auto">
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 italic">
                        "Barangsiapa yang memberikan kemudahan kepada orang yang sedang kesulitan, maka Allah akan memberikan kemudahan kepadanya di dunia dan akhirat."
                    </p>
                    <p class="text-xs text-zinc-400 font-bold mt-2">— HR. Muslim</p>
                </div>
            </div>

            <!-- Donation Summary Card -->
            <div class="bg-white dark:bg-zinc-900 rounded-[40px] border border-zinc-200 dark:border-white/5 shadow-2xl overflow-hidden mb-8">
                <div class="p-8 md:p-12 space-y-8">
                    <div class="flex items-center justify-between pb-6 border-b border-zinc-100 dark:border-white/5">
                        <div>
                            <h2 class="text-2xl font-black text-zinc-900 dark:text-white mb-1">Ringkasan Donasi</h2>
                            <p class="text-sm text-zinc-500">ID Transaksi: <span class="font-mono font-bold text-primary">{donation.id}</span></p>
                        </div>
                        <div class="px-4 py-2 rounded-full bg-amber-500/10 border border-amber-500/20">
                            <span class="text-xs font-black uppercase tracking-wider text-amber-600">Menunggu Pembayaran</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <div>
                                <span class="text-xs text-zinc-400 font-black uppercase tracking-widest">Donatur</span>
                                <p class="text-lg font-bold text-zinc-900 dark:text-white mt-1">
                                    {donation.is_anonymous ? 'Hamba Allah' : donation.donor_name}
                                </p>
                            </div>
                            <div>
                                <span class="text-xs text-zinc-400 font-black uppercase tracking-widest">Email</span>
                                <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400 mt-1">{donation.donor_email}</p>
                            </div>
                            <div>
                                <span class="text-xs text-zinc-400 font-black uppercase tracking-widest">WhatsApp</span>
                                <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400 mt-1">{donation.donor_phone}</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <span class="text-xs text-zinc-400 font-black uppercase tracking-widest">Jenis Donasi</span>
                                <p class="text-lg font-bold text-zinc-900 dark:text-white mt-1 capitalize">{donation.donation_type}</p>
                            </div>
                            {#if campaign}
                                <div>
                                    <span class="text-xs text-zinc-400 font-black uppercase tracking-widest">Program</span>
                                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400 mt-1">{campaign.title}</p>
                                </div>
                            {/if}
                            <div>
                                <span class="text-xs text-zinc-400 font-black uppercase tracking-widest">Nominal Donasi</span>
                                <p class="text-3xl font-black text-primary mt-1">{formatRupiah(donation.amount)}</p>
                            </div>
                        </div>
                    </div>

                    {#if donation.donor_message}
                        <div class="pt-6 border-t border-zinc-100 dark:border-white/5">
                            <span class="text-xs text-zinc-400 font-black uppercase tracking-widest">Pesan & Doa</span>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 mt-2 italic">{donation.donor_message}</p>
                        </div>
                    {/if}
                </div>
            </div>

            <!-- Payment Instructions -->
            {#if donation.payment_method === 'bank_transfer'}
                <div class="bg-gradient-to-br from-primary/5 to-primary/10 dark:from-primary/10 dark:to-primary/20 rounded-[40px] border border-primary/20 p-8 md:p-12 mb-8">
                    <div class="flex items-start gap-4 mb-6">
                        <div class="size-12 rounded-xl bg-primary/20 flex items-center justify-center shrink-0">
                            <svg class="size-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-zinc-900 dark:text-white mb-2">Instruksi Pembayaran</h3>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Silakan transfer ke rekening berikut:</p>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-zinc-900 rounded-[32px] p-6 md:p-8 border border-zinc-200 dark:border-white/5">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <p class="text-xs text-zinc-400 font-black uppercase tracking-widest mb-1">Bank</p>
                                <p class="text-2xl font-black text-zinc-900 dark:text-white">{donation.bank_name}</p>
                            </div>
                            <div class="size-16 rounded-xl bg-zinc-50 dark:bg-white/5 flex items-center justify-center">
                                <span class="text-xs font-black text-primary uppercase">{donation.bank_name?.substring(0, 3)}</span>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div>
                                <p class="text-xs text-zinc-400 font-black uppercase tracking-widest mb-1">Nomor Rekening</p>
                                <div class="flex items-center gap-3">
                                    <p class="text-xl font-mono font-black text-zinc-900 dark:text-white">{donation.account_number}</p>
                                    <button onclick={() => copyToClipboard(donation.account_number)}
                                        class="px-3 py-1 rounded-lg bg-primary/10 hover:bg-primary/20 text-primary text-xs font-bold transition-colors"
                                    >
                                        {copied ? 'Tersalin' : 'Salin'}
                                    </button>
                                </div>
                            </div>

                            <div>
                                <p class="text-xs text-zinc-400 font-black uppercase tracking-widest mb-1">Atas Nama</p>
                                <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">{siteName}</p>
                            </div>

                            <div class="pt-4 border-t border-zinc-100 dark:border-white/5">
                                <p class="text-xs text-zinc-400 font-black uppercase tracking-widest mb-1">Jumlah Transfer</p>
                                <p class="text-3xl font-black text-primary">{formatRupiah(donation.amount)}</p>
                                <p class="text-xs text-zinc-400 mt-1">Transfer sesuai nominal untuk mempermudah verifikasi</p>
                            </div>
                        </div>
                    </div>
                </div>

            {:else if donation.payment_method === 'qris'}
                <div class="bg-gradient-to-br from-primary/5 to-primary/10 dark:from-primary/10 dark:to-primary/20 rounded-[40px] border border-primary/20 p-8 md:p-12 mb-8">
                    <div class="text-center space-y-6">
                        <div class="inline-flex items-center justify-center size-12 rounded-xl bg-primary/20">
                            <svg class="size-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-zinc-900 dark:text-white mb-2">Scan QRIS</h3>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400">Scan kode QR berikut dengan aplikasi e-wallet Anda</p>
                        </div>

                        <div class="inline-block p-8 bg-white dark:bg-zinc-900 rounded-[32px] border border-zinc-200 dark:border-white/5">
                            {#if qrisImage}
                                <img src={qrisImage} alt="QRIS Code" class="size-64 object-contain rounded-2xl">
                            {:else}
                                <div class="size-64 bg-zinc-100 dark:bg-white/5 rounded-2xl flex items-center justify-center">
                                    <div class="text-center space-y-2">
                                        <svg class="size-12 text-zinc-300 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" /></svg>
                                        <p class="text-xs text-zinc-400 font-bold">QRIS belum dikonfigurasi</p>
                                    </div>
                                </div>
                            {/if}
                        </div>

                        <div class="p-4 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-white/5">
                            <p class="text-xs text-zinc-400 font-black uppercase tracking-widest mb-1">Jumlah Pembayaran</p>
                            <p class="text-2xl font-black text-primary">{formatRupiah(donation.amount)}</p>
                        </div>

                        <div class="text-xs text-zinc-400">
                            <p class="font-bold mb-1">Cara Pembayaran:</p>
                            <ol class="text-left space-y-1 max-w-md mx-auto">
                                <li>1. Buka aplikasi e-wallet (GoPay, OVO, Dana, ShopeePay, dll)</li>
                                <li>2. Pilih menu Scan QR atau QRIS</li>
                                <li>3. Scan kode QR di atas</li>
                                <li>4. Masukkan nominal: <span class="font-bold text-primary">{formatRupiah(donation.amount)}</span></li>
                                <li>5. Konfirmasi pembayaran</li>
                            </ol>
                        </div>
                    </div>
                </div>
            {/if}

            <!-- Next Steps -->
            <div class="bg-white dark:bg-zinc-900 rounded-[40px] border border-zinc-200 dark:border-white/5 p-8 md:p-12 mb-8">
                <h3 class="text-2xl font-black text-zinc-900 dark:text-white mb-6">Langkah Selanjutnya</h3>
                <div class="space-y-4">
                    {#if donation.proof_image}
                        <div class="flex gap-4">
                            <div class="size-10 rounded-full bg-green-500/10 flex items-center justify-center shrink-0">
                                <svg class="size-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-black text-zinc-900 dark:text-white mb-1">Pembayaran Selesai</h4>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Anda sudah melakukan pembayaran</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="size-10 rounded-full bg-green-500/10 flex items-center justify-center shrink-0">
                                <svg class="size-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-black text-zinc-900 dark:text-white mb-1">Bukti Transfer Sudah Diupload</h4>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Bukti pembayaran Anda sudah kami terima</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="size-10 rounded-full bg-amber-500/10 flex items-center justify-center shrink-0">
                                <svg class="size-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-black text-zinc-900 dark:text-white mb-1">Menunggu Verifikasi</h4>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Tim kami sedang memverifikasi pembayaran Anda (1x24 jam)</p>
                            </div>
                        </div>
                    {:else}
                        <div class="flex gap-4">
                            <div class="size-10 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                                <span class="text-sm font-black text-primary">1</span>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-black text-zinc-900 dark:text-white mb-1">Lakukan Pembayaran</h4>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Transfer sesuai nominal ke rekening yang tertera di atas</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="size-10 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                                <span class="text-sm font-black text-primary">2</span>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-black text-zinc-900 dark:text-white mb-1">Upload Bukti Transfer</h4>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Klik tombol di bawah untuk upload bukti pembayaran Anda</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="size-10 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                                <span class="text-sm font-black text-primary">3</span>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-black text-zinc-900 dark:text-white mb-1">Menunggu Verifikasi</h4>
                                <p class="text-sm text-zinc-600 dark:text-zinc-400">Tim kami akan memverifikasi dalam 1x24 jam</p>
                            </div>
                        </div>
                    {/if}
                </div>
            </div>

            <!-- Action Buttons -->
            {#if donation.proof_image}
                <div class="flex flex-col gap-4">
                    <Link href="/"
                        class="w-full h-16 rounded-xl bg-primary text-white font-black uppercase tracking-widest shadow-xl shadow-primary/30 hover:bg-primary/90 transition flex items-center justify-center gap-2"
                    >
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>
                        Kembali ke Beranda
                    </Link>
                </div>
            {:else}
                <div class="flex flex-col md:flex-row gap-4">
                    <Link href={`/donasi/status/${donation.id}`}
                        class="md:flex-1 h-16 rounded-xl bg-primary text-white font-black uppercase tracking-widest shadow-xl shadow-primary/30 hover:bg-primary/90 transition flex items-center justify-center gap-2"
                    >
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" /></svg>
                        Upload Bukti Pembayaran
                    </Link>
                    <Link href="/"
                        class="md:flex-1 h-16 rounded-xl font-bold border-2 border-zinc-200 dark:border-white/10 flex items-center justify-center text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-white/5 transition"
                    >
                        Kembali ke Beranda
                    </Link>
                </div>
            {/if}

            <!-- Contact Support -->
            <div class="mt-8 text-center">
                <p class="text-sm text-zinc-500 mb-2">Butuh bantuan?</p>
                <a href="https://wa.me/{contactWhatsapp}" target="_blank" class="inline-flex items-center gap-2 text-primary font-bold hover:underline">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" /></svg>
                    Hubungi Admin via WhatsApp
                </a>
            </div>

        </div>
    </div>
</Layout>

