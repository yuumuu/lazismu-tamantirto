<script>
    import { ArrowRight } from "lucide-svelte";
    import { Link } from "@inertiajs/svelte";
    export let campaign = {};

    const formatCurrency = (amount) => {
        if (!amount && amount !== 0) return "Rp 0";
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            maximumFractionDigits: 0,
        }).format(amount);
    };

    let bannerImage = "";
    let imageUrl = "";

    $: bannerImage =
        campaign?.featured_image ||
        campaign?.image ||
        campaign?.image_path ||
        "";
    $: imageUrl = bannerImage?.startsWith("http")
        ? bannerImage
        : bannerImage
          ? `/storage/${bannerImage}`
          : "";
</script>

<div
    class="group flex flex-col overflow-hidden rounded-[32px] border border-zinc-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl dark:border-white/5 dark:bg-zinc-950"
>
    <div
        class="relative aspect-[4/3] overflow-hidden bg-zinc-100 text-zinc-500 dark:bg-zinc-900"
    >
        {#if bannerImage}
            <img
                src={imageUrl}
                alt={campaign.title}
                class="h-full w-full object-cover transition duration-700 group-hover:scale-105"
            />
        {:else}
            <div class="flex h-full items-center justify-center text-6xl">
                🌾
            </div>
        {/if}

        <div
            class="absolute inset-0 bg-gradient-to-t from-zinc-950/80 via-transparent to-transparent"
        ></div>
        <div class="absolute left-4 top-4 flex flex-col gap-2">
            {#if campaign.is_urgent}
                <span
                    class="rounded-full bg-red-500 px-3 py-1 text-[10px] font-black uppercase text-white"
                    >Mendesak</span
                >
            {/if}
            <span
                class="rounded-full bg-primary px-3 py-1 text-[10px] font-black uppercase text-white"
                >{campaign.category?.name || "Program"}</span
            >
        </div>
        {#if campaign.daysRemaining > 0}
            <div
                class="absolute bottom-4 left-4 rounded-3xl bg-zinc-950/80 px-4 py-2 text-xs font-bold uppercase text-white"
            >
                {campaign.daysRemaining} Hari Lagi
            </div>
        {/if}
    </div>

    <div class="flex flex-1 flex-col p-8 gap-6">
        <div class="space-y-4">
            <Link
                href={`/program/${campaign.slug}`}
                class="text-xl font-black leading-tight tracking-tight text-zinc-900 dark:text-white hover:text-primary"
            >
                {campaign.title}
            </Link>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 line-clamp-2">
                {campaign.short_description ||
                    campaign.description ||
                    "Program kebaikan yang siap Anda dukung."}
            </p>
        </div>

        <div class="space-y-6">
            <div class="space-y-3">
                <div
                    class="flex items-center justify-between text-sm font-bold uppercase text-zinc-500"
                >
                    <span>Terkumpul</span>
                    <span>Target</span>
                </div>
                <div
                    class="flex items-center justify-between text-base font-black text-primary"
                >
                    <span
                        >{formatCurrency(
                            campaign.verified_donations_sum_amount ??
                                campaign.current_amount ??
                                0,
                        )}</span
                    >
                    <span class="text-zinc-500 dark:text-zinc-400"
                        >{formatCurrency(campaign.target_amount ?? 0)}</span
                    >
                </div>
                <div
                    class="h-3 overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800"
                >
                    <div
                        class="h-full rounded-full bg-primary transition-all duration-700"
                        style="width: {Math.min(
                            campaign.progressPercentage ?? 0,
                            100,
                        )}%"
                    ></div>
                </div>
                <div
                    class="flex items-center justify-between text-[11px] uppercase text-zinc-500 dark:text-zinc-400"
                >
                    <span
                        >{Math.round(campaign.progressPercentage ?? 0)}%
                        tercapai</span
                    >
                    <span>{campaign.verified_donations_count ?? 0} donatur</span
                    >
                </div>
            </div>

            <div class="flex gap-4">
                <Link
                    href={`/donasi/${campaign.slug}`}
                    class="flex-1 rounded-2xl bg-primary px-5 py-3 text-sm font-black uppercase text-white shadow-lg transition hover:bg-primary/90"
                >
                    Donasi Sekarang
                </Link>
                <Link
                    href={`/program/${campaign.slug}`}
                    class="flex h-14 w-14 items-center justify-center rounded-2xl border border-zinc-200 text-zinc-700 transition hover:border-primary hover:text-primary dark:border-white/10 dark:text-zinc-300 dark:hover:text-primary"
                >
                    <ArrowRight class="h-5 w-5" />
                </Link>
            </div>
        </div>
    </div>
</div>
