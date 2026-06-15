<script>
    import {
        Calculator,
        FileText,
        Globe2,
        HeartHandshake,
    } from "lucide-svelte";
    import { Link } from "@inertiajs/svelte";

    const shortcuts = [
        {
            title: "Zakat",
            subtitle: "Hitung & Bayar",
            href: "/donasi?type=zakat",
            icon: Calculator,
        },
        {
            title: "Infaq",
            subtitle: "Sedekah Rutin",
            href: "/donasi?type=infaq",
            icon: HeartHandshake,
        },
        {
            title: "Kemanusiaan",
            subtitle: "Bantu Sesama",
            href: "/program",
            icon: Globe2,
        },
        {
            title: "Laporan",
            subtitle: "Transparansi",
            href: "/laporan",
            icon: FileText,
        },
    ];
</script>

<section class="relative -mt-16 z-30 px-6 lg:px-8">
    <div class="mx-auto max-w-7xl">
        <div class="grid grid-cols-2 gap-4 md:grid-cols-4">
            {#each shortcuts as shortcut}
                <Link
                    href={shortcut.href}
                    class="group flex flex-col gap-6 rounded-[32px] border border-zinc-200 bg-white px-6 py-8 text-center shadow-xl transition hover:-translate-y-1 hover:shadow-2xl dark:border-white/5 dark:bg-zinc-950"
                >
                    <div
                        class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-primary/10 text-primary transition group-hover:bg-primary group-hover:text-white"
                    >
                        <svelte:component
                            this={shortcut.icon}
                            class="h-7 w-7"
                        />
                    </div>
                    <div>
                        <p
                            class="text-sm font-black uppercaseem] text-zinc-900 dark:text-white"
                        >
                            {shortcut.title}
                        </p>
                        <p
                            class="mt-2 text-xs font-bold uppercase text-zinc-500 dark:text-zinc-400"
                        >
                            {shortcut.subtitle}
                        </p>
                    </div>
                </Link>
            {/each}
        </div>
    </div>
</section>
