<script>
    import {
        BookOpen,
        Briefcase,
        Globe2,
        Handshake,
        Heart,
    } from "lucide-svelte";

    const pillars = [
        {
            label: "Pendidikan",
            description: "Beasiswa & Sarana",
            icon: BookOpen,
        },
        { label: "Kesehatan", description: "Bantuan Medis", icon: Heart },
        { label: "Ekonomi", description: "Pemberdayaan", icon: Briefcase },
        { label: "Sosial", description: "Dakwah & Relawan", icon: Handshake },
        { label: "Kemanusiaan", description: "Tanggap Bencana", icon: Globe2 },
    ];
</script>

<section class="py-24 bg-zinc-50 dark:bg-zinc-900/50">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="text-center space-y-4 mb-20">
            <p class="text-xs font-black uppercase text-primary">
                Pilar Program
            </p>
            <h2
                class="text-3xl font-black tracking-tight text-zinc-900 dark:text-white sm:text-4xl"
            >
                Fokus Kebaikan Kami
            </h2>
            <p
                class="mx-auto max-w-2xl text-sm font-medium text-zinc-500 dark:text-zinc-400 italic"
            >
                Pilar utama Lazismu dalam menggerakkan kemanfaatan bagi sesama.
            </p>
        </div>

        <div class="grid grid-cols-2 gap-6 md:grid-cols-3 lg:grid-cols-5">
            {#each pillars as pillar}
                <div
                    class="group rounded-[48px_0_48px_0] border border-zinc-200 bg-white p-8 text-center shadow-sm transition hover:-translate-y-2 hover:shadow-2xl dark:border-white/5 dark:bg-zinc-950"
                >
                    <div
                        class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-3xl bg-primary/10 text-primary transition group-hover:bg-primary group-hover:text-white"
                    >
                        <svelte:component this={pillar.icon} class="h-7 w-7" />
                    </div>
                    <h3
                        class="text-sm font-black uppercaseem] text-zinc-900 dark:text-white"
                    >
                        {pillar.label}
                    </h3>
                    <p
                        class="mt-2 text-xs font-bold uppercase text-zinc-500 dark:text-zinc-400"
                    >
                        {pillar.description}
                    </p>
                </div>
            {/each}
        </div>
    </div>
</section>
