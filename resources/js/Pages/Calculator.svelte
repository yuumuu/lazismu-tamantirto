<script>
    import Layout from "../Layouts/GuestLayout.svelte";
    import { Link } from "@inertiajs/svelte";
    import { ArrowRight, Calculator as CalculatorIcon } from "lucide-svelte";

    let {
        goldPrice = 1200000,
        goldNisab = 85,
        donateFormUrl = "/donasi",
    } = $props();
    let tab = $state("profesi");
    let income = $state(0);
    let otherIncome = $state(0);
    let maalAsset = $state(0);
    let fitrahQty = $state(1);
    let ricePrice = $state(15000);

    const totalIncome = () => Number(income) + Number(otherIncome);
    const nisabProfesi = () => (goldNisab * goldPrice) / 12;
    const nisabMaal = () => goldNisab * goldPrice;

    const zakatProfesi = () => {
        const amount = totalIncome();
        return amount >= nisabProfesi() ? Math.floor(amount * 0.025) : 0;
    };

    const zakatMaal = () => {
        const amount = Number(maalAsset);
        return amount >= nisabMaal() ? Math.floor(amount * 0.025) : 0;
    };

    const zakatFitrah = () => {
        return Number(fitrahQty) * 2.5 * Number(ricePrice);
    };

    const currentZakat = () => {
        if (tab === "profesi") {
            return zakatProfesi();
        }

        if (tab === "maal") {
            return zakatMaal();
        }

        return zakatFitrah();
    };

    const formatCurrency = (value) => {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            maximumFractionDigits: 0,
        }).format(value || 0);
    };

    const donationUrl = () => {
        const amount = currentZakat();
        const subtype =
            tab === "profesi" ? "profesi" : tab === "maal" ? "maal" : "fitrah";
        return `${donateFormUrl}?amount=${amount}&type=zakat&subtype=${subtype}&from=calculator`;
    };
</script>

<svelte:head>
    <title>Kalkulator Zakat</title>
</svelte:head>

<Layout>
    <section class="py-12 md:py-24 bg-zinc-50 dark:bg-zinc-950 min-h-screen">
        <div class="mx-auto max-w-4xl px-6 lg:px-8">
            <div class="text-center space-y-4 mb-16">
                <p class="text-xs font-black uppercase text-primary">
                    Simulasi Zakat
                </p>
                <h1
                    class="text-4xl font-black text-zinc-900 dark:text-white sm:text-5xl"
                >
                    Kalkulator Zakat
                </h1>
                <p
                    class="text-zinc-500 dark:text-zinc-400 font-medium max-w-2xl mx-auto italic"
                >
                    "Ambillah zakat dari sebagian harta mereka, dengan zakat itu
                    kamu membersihkan dan mencucikan mereka." (QS. At-Taubah:
                    103)
                </p>
            </div>

            <div
                class="overflow-hidden rounded-[48px] border border-zinc-200 bg-white shadow-2xl dark:border-white/5 dark:bg-zinc-900"
            >
                <div
                    class="flex flex-col border-b border-zinc-100 dark:border-white/5 md:flex-row"
                >
                    <button
                        type="button"
                        onclick={() => (tab = "profesi")}
                        class={`flex-1 border-b-4 px-6 py-6 text-sm font-black uppercase transition hover:text-primary ${tab === "profesi" ? "border-primary text-primary" : "border-transparent text-zinc-400 dark:text-zinc-500"}`}
                        >Zakat Profesi</button
                    >
                    <button
                        type="button"
                        onclick={() => (tab = "maal")}
                        class={`flex-1 border-b-4 px-6 py-6 text-sm font-black uppercase transition hover:text-primary ${tab === "maal" ? "border-primary text-primary" : "border-transparent text-zinc-400 dark:text-zinc-500"}`}
                        >Zakat Maal</button
                    >
                    <button
                        type="button"
                        onclick={() => (tab = "fitrah")}
                        class={`flex-1 border-b-4 px-6 py-6 text-sm font-black uppercase transition hover:text-primary ${tab === "fitrah" ? "border-primary text-primary" : "border-transparent text-zinc-400 dark:text-zinc-500"}`}
                        >Zakat Fitrah</button
                    >
                </div>

                <div class="p-8 md:p-12">
                    <div class="grid gap-12 lg:grid-cols-2">
                        <div class="space-y-8">
                            {#if tab === "profesi"}
                                <div class="space-y-6">
                                    <label
                                        class="block text-sm font-black uppercase text-zinc-500"
                                        >Pendapatan per Bulan</label
                                    >
                                    <input
                                        type="number"
                                        bind:value={income}
                                        min="0"
                                        class="w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-4 text-lg font-bold outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/10 dark:border-white/10 dark:bg-zinc-950 dark:text-white"
                                        placeholder="0"
                                    />

                                    <label
                                        class="block text-sm font-black uppercase text-zinc-500"
                                        >Pendapatan Lainnya</label
                                    >
                                    <input
                                        type="number"
                                        bind:value={otherIncome}
                                        min="0"
                                        class="w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-4 text-lg font-bold outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/10 dark:border-white/10 dark:bg-zinc-950 dark:text-white"
                                        placeholder="0"
                                    />

                                    <p
                                        class="text-sm italic text-zinc-500 dark:text-zinc-400"
                                    >
                                        *Nisab Zakat Profesi setara {goldNisab}gr
                                        emas per tahun ({formatCurrency(
                                            nisabProfesi(),
                                        )}/bulan).
                                    </p>
                                </div>
                            {:else if tab === "maal"}
                                <div class="space-y-6">
                                    <label
                                        class="block text-sm font-black uppercase text-zinc-500"
                                        >Total Harta Simpanan</label
                                    >
                                    <input
                                        type="number"
                                        bind:value={maalAsset}
                                        min="0"
                                        class="w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-4 text-lg font-bold outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/10 dark:border-white/10 dark:bg-zinc-950 dark:text-white"
                                        placeholder="0"
                                    />

                                    <p
                                        class="text-sm italic text-zinc-500 dark:text-zinc-400"
                                    >
                                        *Harta yang telah mencapai haul dan
                                        nisab (setara {goldNisab}gr emas ≈ {formatCurrency(
                                            nisabMaal(),
                                        )}).
                                    </p>
                                </div>
                            {:else}
                                <div class="space-y-6">
                                    <label
                                        class="block text-sm font-black uppercase text-zinc-500"
                                        >Jumlah Jiwa</label
                                    >
                                    <input
                                        type="number"
                                        bind:value={fitrahQty}
                                        min="1"
                                        class="w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-4 text-lg font-bold outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/10 dark:border-white/10 dark:bg-zinc-950 dark:text-white"
                                        placeholder="1"
                                    />

                                    <label
                                        class="block text-sm font-black uppercase text-zinc-500"
                                        >Harga Beras per Kg</label
                                    >
                                    <input
                                        type="number"
                                        bind:value={ricePrice}
                                        min="0"
                                        class="w-full rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-4 text-lg font-bold outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/10 dark:border-white/10 dark:bg-zinc-950 dark:text-white"
                                        placeholder="15000"
                                    />

                                    <p
                                        class="text-sm italic text-zinc-500 dark:text-zinc-400"
                                    >
                                        *Zakat Fitrah adalah 2.5kg beras per
                                        jiwa atau setara nilai uang.
                                    </p>
                                </div>
                            {/if}
                        </div>

                        <div
                            class="flex flex-col justify-center gap-6 rounded-[32px] bg-primary/5 p-6 md:p-8 border border-primary/10 dark:bg-primary/10"
                        >
                            <div class="flex items-center gap-4">
                                <div
                                    class="flex h-14 w-14 items-center justify-center rounded-3xl bg-primary text-white shadow-lg"
                                >
                                    <CalculatorIcon class="h-6 w-6" />
                                </div>
                                <div>
                                    <p class="text-xs uppercase text-white/70">
                                        Total Kewajiban Zakat
                                    </p>
                                    <p
                                        class="text-3xl font-black text-white sm:text-4xl"
                                    >
                                        {formatCurrency(currentZakat())}
                                    </p>
                                </div>
                            </div>

                            <div
                                class="space-y-4 border-t border-primary/15 pt-6"
                            >
                                {#if currentZakat() > 0}
                                    <Link
                                        href={donationUrl()}
                                        class="inline-flex h-16 w-full items-center justify-center rounded-2xl bg-white text-sm font-black uppercase text-primary shadow-xl shadow-primary/20 transition hover:bg-zinc-100"
                                        >Tunaikan Sekarang</Link
                                    >
                                {:else}
                                    <div
                                        class="rounded-3xl bg-white/10 p-6 text-center text-sm font-black uppercase text-white/80"
                                    >
                                        Harta Anda belum mencapai nisab wajib
                                        zakat.
                                    </div>
                                {/if}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</Layout>
