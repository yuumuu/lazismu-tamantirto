<script>
    import Layout from "../Layouts/GuestLayout.svelte";
    import { Link } from "@inertiajs/svelte";
    import Briefcase from "lucide-svelte/icons/briefcase";
    import FileText from "lucide-svelte/icons/file-text";
    import ShieldCheck from "lucide-svelte/icons/shield-check";
    import Sparkles from "lucide-svelte/icons/sparkles";
    import Users from "lucide-svelte/icons/users";

    let { previewMembers = [] } = $props();

    const coreValues = [
        {
            icon: ShieldCheck,
            title: "Amanah",
            description:
                "Menjaga kepercayaan donatur dengan integritas tertinggi.",
        },
        {
            icon: Briefcase,
            title: "Profesional",
            description:
                "Mengelola dana dengan standar keunggulan dan kompetensi.",
        },
        {
            icon: FileText,
            title: "Transparan",
            description:
                "Setiap rupiah yang masuk dan keluar dapat dipertanggungjawabkan.",
        },
        {
            icon: Sparkles,
            title: "Mandiri",
            description:
                "Mendorong kemandirian penerima manfaat melalui pemberdayaan.",
        },
    ];

    const missionPoints = [
        "Meningkatkan kesadaran masyarakat tentang pentingnya zakat, infaq, dan sedekah.",
        "Mengelola dana publik secara transparan, akuntebel, dan sesuai syariat.",
        "Menyalurkan bantuan secara merata dan tepat sasaran di wilayah Tamantirto.",
    ];
</script>

<svelte:head>
    <title>Tentang Kami</title>
</svelte:head>

<Layout>
    <header class="relative overflow-hidden bg-zinc-900 border-b border-white/5 py-24">
        <img
            src="https://images.unsplash.com/photo-1488521787991-ed7bbaae773c?q=80&w=2070&auto=format&fit=crop"
            alt=""
            class="absolute inset-0 size-full object-cover opacity-20 blur-sm"
        />
        <div class="relative mx-auto max-w-7xl px-6 lg:px-8 text-center space-y-6">
            <p class="text-xs font-black uppercase text-primary">
                Tentang Kami
            </p>
            <h1
                class="text-4xl font-black leading-tight text-white md:text-6xl"
            >
                Menebar Kebaikan,<br />Membangun Kemandirian Umat
            </h1>
        </div>
    </header>

    <section class="py-24 bg-white dark:bg-zinc-950">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid gap-24 lg:grid-cols-2 lg:items-center">
                <div class="space-y-8">
                    <div class="space-y-4">
                        <h2
                            class="text-3xl font-black text-zinc-900 dark:text-white"
                        >
                            Visi Kami
                        </h2>
                        <p class="text-lg text-zinc-500 dark:text-zinc-400">
                            Menjadi Lembaga Amil Zakat yang Amanah, Profesional,
                            dan Mandiri dalam memberdayakan masyarakat melalui
                            pengelolaan zakat, infaq, dan sedekah yang
                            berkualitas.
                        </p>
                    </div>
                    <div class="space-y-4">
                        <h2
                            class="text-3xl font-black text-zinc-900 dark:text-white"
                        >
                            Misi Kami
                        </h2>
                        <ul class="space-y-4 text-zinc-500 dark:text-zinc-400">
                            {#each missionPoints as point}
                                <li class="flex items-start gap-4">
                                    <span
                                        class="mt-1 h-9 w-9 flex-shrink-0 rounded-xl bg-primary/10 text-primary grid place-items-center font-black"
                                        >✓</span
                                    >
                                    <span>{point}</span>
                                </li>
                            {/each}
                        </ul>
                    </div>
                </div>

                <div class="relative">
                    <img
                        src="https://images.unsplash.com/photo-1542601906990-b4d3fb773b09?q=80&w=1914&auto=format&fit=crop"
                        alt="Tentang Kami"
                        class="rounded-[64px] shadow-2xl skew-y-3 hover:skew-y-0 transition-transform duration-700"
                    />
                    <div
                        class="absolute -bottom-8 -left-8 size-48 rounded-[40px] bg-primary -z-10 blur-2xl opacity-20"
                    ></div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 bg-zinc-50 dark:bg-zinc-900/50">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="text-center space-y-4 mb-20">
                <p class="text-xs font-black uppercase text-primary">
                    Nilai Utama
                </p>
                    <h2
                        class="text-3xl md:text-5xl font-black text-zinc-900 dark:text-white tracking-tight"
                    >
                        Prinsip Kami Dalam Melayani
                    </h2>
            </div>

            <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-4">
                {#each coreValues as value}
                    <div
                        class="rounded-[32px] border border-zinc-200 bg-white p-8 text-center shadow-sm transition hover:-translate-y-2 hover:shadow-xl dark:border-white/5 dark:bg-zinc-950"
                    >
                        <div
                            class="mx-auto mb-6 flex h-16 w-16 items-center justify-center rounded-xl bg-primary/10 text-primary shadow-inner transition group-hover:bg-primary group-hover:text-white"
                        >
                            <value.icon class="h-8 w-8" />
                        </div>
                        <h3
                            class="text-xl font-black text-zinc-900 dark:text-white mb-2"
                        >
                            {value.title}
                        </h3>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">
                            {value.description}
                        </p>
                    </div>
                {/each}
            </div>
        </div>
    </section>

    <section class="py-24 bg-white dark:bg-zinc-950 overflow-hidden">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid gap-24 lg:grid-cols-2 lg:items-center">
                <div class="space-y-8">
                    <div class="space-y-4">
                        <p class="text-xs font-black uppercase text-primary">
                            Struktur Organisasi
                        </p>
                        <h2
                            class="text-3xl font-black text-zinc-900 dark:text-white sm:text-4xl"
                        >
                            Dikelola oleh tim profesional dan amanah
                        </h2>
                        <p class="text-lg text-zinc-500 dark:text-zinc-400">
                            Kami yakin setiap proses pengelolaan dana harus
                            dijalankan dengan standar profesionalisme,
                            transparansi, dan kejelasan yang kuat.
                        </p>
                    </div>
                    <div class="flex flex-wrap gap-4">
                        <Link
                            href="/struktur"
                            class="inline-flex h-14 items-center justify-center rounded-xl bg-primary px-8 text-sm font-black uppercase text-white shadow-xl shadow-primary/30 transition hover:bg-primary/90"
                            >Lihat Struktur Lengkap</Link
                        >
                        <Link
                            href="/laporan"
                            class="inline-flex h-14 items-center justify-center rounded-xl border border-zinc-200 bg-transparent px-8 text-sm font-black uppercase text-zinc-900 transition hover:border-primary hover:text-primary dark:border-white/10 dark:text-white"
                            >Laporan Transparansi</Link
                        >
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    {#each previewMembers as member, index}
                        <div
                            class="relative aspect-square overflow-hidden rounded-[32px] group {index %
                                2 ===
                            1
                                ? 'mt-8'
                                : ''}"
                        >
                            <img
                                src={member.photo_url ||
                                    "https://images.unsplash.com/photo-1494790108377-be9c29b29330?q=80&w=900&auto=format&fit=crop"}
                                alt={member.name}
                                class="h-full w-full object-cover transition duration-700 group-hover:scale-105"
                            />
                            <div
                                class="absolute inset-0 bg-gradient-to-t from-zinc-950/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-end p-6"
                            >
                                <span class="text-white font-black text-lg"
                                    >{member.name}</span
                                >
                                <span class="text-primary font-bold text-xs"
                                    >{member.position}</span
                                >
                            </div>
                        </div>
                    {/each}
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 bg-zinc-900 relative overflow-hidden">
        <div class="absolute inset-0 bg-primary/5 opacity-20"></div>
        <div
            class="mx-auto max-w-4xl px-6 lg:px-8 relative text-center space-y-12"
        >
            <h2 class="text-3xl font-black text-white md:text-5xl">
                Siap Untuk Menebar Kebaikan?
            </h2>
            <p class="text-zinc-400 text-lg">
                Bantu kami mewujudkan kemandirian umat melalui dukungan dan
                donasi Anda.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <Link
                    href="/program"
                    class="inline-flex h-16 items-center justify-center rounded-xl bg-primary px-12 text-sm font-black uppercase text-white shadow-2xl shadow-primary/30 transition hover:bg-primary/90"
                    >Mulai Berdonasi</Link
                >
                <Link
                    href="/kontak"
                    class="inline-flex h-16 items-center justify-center rounded-xl border border-white/20 bg-white/5 px-12 text-sm font-black uppercase text-white transition hover:bg-white/10"
                    >Hubungi Kami</Link
                >
            </div>
        </div>
    </section>
</Layout>
