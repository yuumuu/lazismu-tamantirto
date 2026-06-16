<script>
    import { Link } from '@inertiajs/svelte';
    import Layout from '../../Layouts/GuestLayout.svelte';

    let {
        trackingToken = '',
    } = $props();

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
    <title>Pengajuan Berhasil | Lazismu</title>
</svelte:head>

<Layout>
    <div class="py-12 md:py-24 bg-gradient-to-b from-primary/5 via-white to-zinc-50 dark:from-primary/10 dark:via-zinc-950 dark:to-zinc-950 min-h-screen">
        <div class="mx-auto max-w-4xl px-6 lg:px-8">
            <div class="space-y-8">
                <!-- Success Animation & Message -->
                <div class="text-center space-y-8 mb-8">
                    <div class="inline-flex items-center justify-center size-24 rounded-full bg-green-500/10 border-4 border-green-500/20">
                        <svg class="size-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>

                    <div class="space-y-4">
                        <h1 class="text-4xl md:text-5xl font-black text-zinc-900 dark:text-white tracking-tight">
                            Pengajuan Berhasil Dikirim!
                        </h1>
                        <p class="text-lg text-zinc-600 dark:text-zinc-400 font-medium max-w-2xl mx-auto">
                            Simpan token berikut untuk melacak status pengajuan Anda:
                        </p>
                    </div>
                </div>

                <!-- Token Card -->
                <div class="bg-white dark:bg-zinc-900 rounded-[40px] border border-zinc-200 dark:border-white/5 shadow-2xl p-8 md:p-12">
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-black text-zinc-400 uppercase tracking-widest">Token Pengajuan</span>
                            <button onclick={() => copyToClipboard(trackingToken)}
                                class="px-4 py-2 rounded-xl bg-primary/10 hover:bg-primary/20 text-primary text-xs font-bold transition-colors cursor-pointer flex items-center gap-2"
                            >
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                {copied ? 'Tersalin' : 'Salin Token'}
                            </button>
                        </div>

                        <div class="p-6 rounded-2xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-100 dark:border-white/5 text-center">
                            <span class="text-3xl md:text-4xl font-mono font-black text-primary tracking-widest select-all">{trackingToken}</span>
                        </div>

                        <div class="p-4 rounded-2xl bg-amber-500/5 border border-amber-500/10 flex items-start gap-3">
                            <svg class="size-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <p class="text-sm text-zinc-600 dark:text-zinc-400 font-medium">
                                Token akan digunakan untuk mengecek status pengajuan. Kami akan menghubungi Anda melalui nomor WhatsApp yang didaftarkan.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col md:flex-row gap-4">
                    <Link href="/cek-status"
                        class="flex-1 h-16 rounded-xl bg-primary text-white font-black uppercase tracking-widest shadow-xl shadow-primary/30 hover:bg-primary/90 transition flex items-center justify-center gap-2"
                    >
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 3-3"/></svg>
                        Cek Status Pengajuan
                    </Link>
                    <Link href="/"
                        class="flex-1 h-16 rounded-xl font-bold border-2 border-zinc-200 dark:border-white/10 flex items-center justify-center text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-white/5 transition"
                    >
                        <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        Kembali ke Beranda
                    </Link>
                </div>
            </div>
        </div>
    </div>
</Layout>
