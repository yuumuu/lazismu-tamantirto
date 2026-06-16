<script>
    import Layout from "../Layouts/GuestLayout.svelte";
    import { Link } from "@inertiajs/svelte";

    let {
        members = [],
        siteName = "Lazismu",
        siteTagline = "Tamantirto",
    } = $props();
</script>

<svelte:head>
    <title>Struktur Organisasi</title>
</svelte:head>

<Layout>
    <section
        class="bg-zinc-50 dark:bg-zinc-900/50 py-20 border-b border-zinc-200 dark:border-white/5"
    >
        <div class="mx-auto max-w-7xl px-6 lg:px-8 text-center">
            <p class="text-xs font-black uppercase text-primary">
                Struktur Organisasi
            </p>
            <h1
                class="mt-6 text-4xl font-black text-zinc-900 dark:text-white sm:text-5xl"
            >
                Keluarga Besar {siteName}
                {siteTagline}
            </h1>
            <p
                class="mt-6 text-lg text-zinc-500 dark:text-zinc-400 max-w-3xl mx-auto"
            >
                Dipimpin oleh tim yang berdedikasi dan amanah untuk menjamin
                ketepatan penyaluran donasi Anda.
            </p>
        </div>
    </section>

    <section class="py-24 bg-white dark:bg-zinc-950">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid grid-cols-2 gap-8 md:grid-cols-3 md:gap-12 lg:grid-cols-4">
                {#each members as member}
                    <div
                        class="group flex flex-col items-center text-center space-y-6"
                    >
                        <div
                            class="relative w-full aspect-[4/5] rounded-[40px] overflow-hidden shadow-2xl border-4 border-zinc-50 dark:border-zinc-900 group-hover:border-primary/20 transition-all"
                        >
                            <img
                                src={member.photo_url ||
                                    "https://images.unsplash.com/photo-1502685104226-ee32379fefbe?q=80&w=900&auto=format&fit=crop"}
                                alt={member.name}
                                class="h-full w-full object-cover grayscale group-hover:grayscale-0 transition-all duration-700 scale-105 group-hover:scale-100"
                            />
                            <div
                                class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-t from-zinc-900/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"
                            ></div>
                        </div>
                        <div class="space-y-1">
                            <h2
                                class="text-xl font-black text-zinc-900 dark:text-white group-hover:text-primary transition-colors"
                            >
                                {member.name}
                            </h2>
                            <p
                                class="text-xs font-black uppercase text-zinc-400"
                            >
                                {member.position_name || member.position}
                            </p>
                        </div>
                    </div>
                {/each}
            </div>
        </div>
    </section>
</Layout>
