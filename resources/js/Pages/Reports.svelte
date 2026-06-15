<script>
    import Layout from "../Layouts/GuestLayout.svelte";
    import { Link } from "@inertiajs/svelte";
    import {
        Download,
        ArrowUpRight,
        Upload,
        FileText,
        Search,
        ShieldCheck,
    } from "lucide-svelte";

    let {
        totalIncome = 0,
        totalOutcome = 0,
        withdrawalCount = 0,
        balance = 0,
        recentActivity = [],
        publicReports = [],
    } = $props();

    const formatCurrency = (amount) => {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            maximumFractionDigits: 0,
        }).format(amount || 0);
    };

    const formatDate = (value) => {
        if (!value) return "";
        try {
            return new Intl.DateTimeFormat("id-ID", {
                day: "numeric",
                month: "long",
                year: "numeric",
                hour: "2-digit",
                minute: "2-digit",
            }).format(new Date(value));
        } catch {
            return value;
        }
    };
</script>

<svelte:head>
    <title>Laporan Keuangan</title>
</svelte:head>

<Layout>
    <section
        class="bg-zinc-50 dark:bg-zinc-900/50 py-20 border-b border-zinc-200 dark:border-white/5"
    >
        <div class="mx-auto max-w-7xl px-6 lg:px-8 text-center">
            <p class="text-xs font-black uppercase text-primary">
                Transparansi Dana
            </p>
            <h1
                class="mt-6 text-4xl font-black text-zinc-900 dark:text-white sm:text-5xl"
            >
                Laporan Penyaluran & Keuangan
            </h1>
            <p
                class="mt-6 text-lg text-zinc-500 dark:text-zinc-400 max-w-3xl mx-auto italic"
            >
                Setiap rupiah yang Anda amanahkan sangat berarti. Kami
                berkomitmen menyalurkannya secara transparan dan tepat sasaran.
            </p>
        </div>
    </section>

    <section class="py-24 bg-white dark:bg-zinc-950">
        <div class="mx-auto max-w-7xl px-6 lg:px-8 space-y-20">
            <div class="grid gap-8 md:grid-cols-3">
                <div
                    class="rounded-[48px] border border-zinc-100 bg-zinc-50 p-10 dark:border-white/5 dark:bg-white/5"
                >
                    <span class="text-[10px] uppercase text-zinc-400"
                        >Total Donasi Terkumpul</span
                    >
                    <h2
                        class="mt-4 text-4xl font-black text-zinc-900 dark:text-white"
                    >
                        {formatCurrency(totalIncome)}
                    </h2>
                    <p
                        class="mt-2 text-xs text-zinc-500 dark:text-zinc-400 font-bold italic"
                    >
                        Akumulasi seluruh dana donasi
                    </p>
                </div>
                <div
                    class="rounded-[48px] border border-primary/10 bg-primary/5 p-10 shadow-xl shadow-primary/5 dark:border-primary/20 dark:bg-primary/10"
                >
                    <span class="text-[10px] uppercase text-zinc-400"
                        >Total Telah Disalurkan</span
                    >
                    <h2 class="mt-4 text-4xl font-black text-primary">
                        {formatCurrency(totalOutcome)}
                    </h2>
                    <p
                        class="mt-2 text-xs text-zinc-500 dark:text-zinc-400 font-bold italic"
                    >
                        Melalui {withdrawalCount} program bantuan
                    </p>
                </div>
                <div
                    class="rounded-[48px] border border-zinc-100 bg-zinc-50 p-10 dark:border-white/5 dark:bg-white/5"
                >
                    <span class="text-[10px] uppercase text-zinc-400"
                        >Saldo Aktif Saat Ini</span
                    >
                    <h2
                        class="mt-4 text-4xl font-black text-zinc-900 dark:text-white"
                    >
                        {formatCurrency(balance)}
                    </h2>
                    <p
                        class="mt-2 text-xs text-zinc-500 dark:text-zinc-400 font-bold italic"
                    >
                        Sisa dana siap disalurkan
                    </p>
                </div>
            </div>

            <div class="grid gap-16 lg:grid-cols-3">
                <div class="lg:col-span-2 space-y-12">
                    <h3
                        class="text-2xl font-black text-zinc-900 dark:text-white tracking-tight border-l-4 border-primary pl-4"
                    >
                        Aktivitas Terbaru
                    </h3>
                    <div class="space-y-4">
                        {#each recentActivity as act}
                            <div
                                class="group flex items-center justify-between gap-6 rounded-[32px] border border-zinc-100 bg-white p-6 shadow-sm transition hover:shadow-md dark:border-white/5 dark:bg-zinc-950"
                            >
                                <div class="flex items-center gap-6">
                                    <div
                                        class="flex h-14 w-14 items-center justify-center rounded-3xl {act.type ===
                                        'income'
                                            ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/20'
                                            : 'bg-primary/10 text-primary'}"
                                    >
                                        {#if act.type === "income"}
                                            <Download class="h-6 w-6" />
                                        {:else}
                                            <Upload class="h-6 w-6" />
                                        {/if}
                                    </div>
                                    <div>
                                        <p
                                            class="font-black text-zinc-900 dark:text-white"
                                        >
                                            {act.label}
                                        </p>
                                        <p
                                            class="mt-1 text-xs uppercase text-zinc-400"
                                        >
                                            {formatDate(act.date)}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p
                                        class="text-lg font-black {act.type ===
                                        'income'
                                            ? 'text-emerald-600'
                                            : 'text-primary'}"
                                    >
                                        {act.type === "income" ? "+" : "-"}
                                        {formatCurrency(act.amount)}
                                    </p>
                                </div>
                            </div>
                        {/each}
                    </div>
                </div>

                <aside class="space-y-12">
                    <div
                        class="rounded-[48px] bg-zinc-900 p-8 text-white shadow-2xl"
                    >
                        <h4
                            class="text-xl font-black tracking-tight border-b border-white/10 pb-4"
                        >
                            Komitmen Kami
                        </h4>
                        <div class="space-y-6 mt-6">
                            <div class="flex items-start gap-4">
                                <ShieldCheck
                                    class="h-6 w-6 text-primary flex-shrink-0"
                                />
                                <div>
                                    <p class="font-black">Akuntabel</p>
                                    <p class="text-sm text-zinc-400">
                                        Audit berkala oleh lembaga terkait.
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <Search
                                    class="h-6 w-6 text-primary flex-shrink-0"
                                />
                                <div>
                                    <p class="font-black">Transparan</p>
                                    <p class="text-sm text-zinc-400">
                                        Laporan dapat diakses kapan saja oleh
                                        publik.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <Link
                            href="/donasi?type=zakat"
                            class="mt-8 inline-flex h-16 w-full items-center justify-center rounded-2xl bg-primary text-sm font-black uppercase text-white shadow-xl shadow-primary/30 transition hover:bg-primary/90"
                            >Ikut Berkontribusi</Link
                        >
                    </div>

                    <div
                        class="rounded-[40px] border border-zinc-100 bg-zinc-50 p-8 dark:border-white/5 dark:bg-white/5"
                    >
                        <h4
                            class="text-xl font-black text-zinc-900 dark:text-white"
                        >
                            Arsip Laporan Tahunan
                        </h4>
                        <div class="mt-6 space-y-3">
                            {#if publicReports.length > 0}
                                {#each publicReports as report}
                                    <a
                                        href={report.file_url}
                                        target="_blank"
                                        rel="noreferrer"
                                        class="group flex items-center justify-between rounded-2xl border border-zinc-100 bg-white p-4 transition hover:shadow-md dark:border-white/5 dark:bg-zinc-950"
                                    >
                                        <div class="flex items-center gap-3">
                                            <FileText
                                                class="h-5 w-5 text-zinc-400 transition group-hover:text-primary"
                                            />
                                            <div>
                                                <p
                                                    class="font-bold text-zinc-900 dark:text-white"
                                                >
                                                    {report.title}
                                                </p>
                                                <p
                                                    class="text-[10px] uppercase text-zinc-500 dark:text-zinc-400"
                                                >
                                                    {report.year}
                                                </p>
                                            </div>
                                        </div>
                                        <ArrowUpRight
                                            class="h-4 w-4 text-zinc-400"
                                        />
                                    </a>
                                {/each}
                            {:else}
                                <p
                                    class="text-xs italic text-zinc-500 dark:text-zinc-400"
                                >
                                    Belum ada laporan tersedia.
                                </p>
                            {/if}
                        </div>
                    </div>

                    <div
                        class="rounded-[40px] border border-zinc-100 bg-zinc-50 p-8 dark:border-white/5 dark:bg-white/5"
                    >
                        <h4
                            class="text-lg font-black text-zinc-900 dark:text-white"
                        >
                            Butuh Laporan Lengkap?
                        </h4>
                        <p
                            class="mt-3 text-sm text-zinc-500 dark:text-zinc-400"
                        >
                            Anda dapat meminta rincian laporan keuangan tahunan
                            melalui kantor pelayanan kami.
                        </p>
                        <Link
                            href="/kontak"
                            class="mt-6 inline-flex h-14 w-full items-center justify-center rounded-2xl border border-zinc-200 bg-transparent text-sm font-bold uppercase text-zinc-900 transition hover:border-primary hover:text-primary dark:border-white/10 dark:text-white"
                            >Hubungi Kami</Link
                        >
                    </div>
                </aside>
            </div>
        </div>
    </section>
</Layout>
