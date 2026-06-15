<script>
    import Layout from "../Layouts/GuestLayout.svelte";
    import { Link } from "@inertiajs/svelte";
    import {
        ArrowUpRight,
        Facebook,
        Instagram,
        Mail,
        MapPin,
        Phone,
        Twitter,
        Youtube,
    } from "lucide-svelte";

    let {
        address = "",
        phone = "",
        whatsapp = "",
        email = "",
        mapsUrl = "",
        social = {},
    } = $props();

    const contactItems = [
        {
            icon: MapPin,
            label: "Alamat Kantor",
            value: address,
        },
        {
            icon: Phone,
            label: "Telepon & WhatsApp",
            value: `${phone}${whatsapp ? ` / ${whatsapp}` : ""}`,
        },
        {
            icon: Mail,
            label: "Email Resmi",
            value: email,
        },
    ];

    const socials = [
        {
            provider: "facebook",
            label: "Facebook",
            icon: Facebook,
            url: social.facebook,
        },
        {
            provider: "instagram",
            label: "Instagram",
            icon: Instagram,
            url: social.instagram,
        },
        {
            provider: "twitter",
            label: "Twitter",
            icon: Twitter,
            url: social.twitter,
        },
        {
            provider: "youtube",
            label: "YouTube",
            icon: Youtube,
            url: social.youtube,
        },
    ];

    const mapSrc =
        mapsUrl ||
        "https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15810.835497232338!2d110.32049685!3d-7.82062535!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7af80302b11545%3A0xe549646b142921fd!2zTFFSVlVRIChMYXppc01VKSBUYW1hbnRpcnRv!5e0!3m2!1sid!2sid!4v1711234567890";
</script>

<svelte:head>
    <title>Kontak</title>
</svelte:head>

<Layout>
    <section class="py-12 md:py-24 bg-zinc-50 dark:bg-zinc-950 min-h-screen">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid gap-16 lg:grid-cols-2 lg:gap-24 items-start">
                <div class="space-y-12">
                    <div class="space-y-6">
                        <p class="text-xs font-black uppercase text-primary">
                            Hubungi Kami
                        </p>
                        <h1
                            class="text-4xl font-black text-zinc-900 dark:text-white sm:text-5xl"
                        >
                            Selalu Siap Mendengar & Melayani Anda
                        </h1>
                        <p
                            class="text-lg text-zinc-500 dark:text-zinc-400 leading-relaxed"
                        >
                            Punya pertanyaan seputar zakat atau ingin
                            berkolaborasi? Jangan ragu menghubungi kami melalui
                            jalur resmi di bawah ini.
                        </p>
                    </div>

                    <div class="space-y-6">
                        {#each contactItems as item}
                            <div
                                class="flex items-center gap-6 rounded-[32px] border border-zinc-200 bg-white p-6 shadow-sm transition hover:border-primary/20 dark:border-white/5 dark:bg-zinc-900"
                            >
                                <div
                                    class="flex h-16 w-16 items-center justify-center rounded-3xl bg-primary/10 text-primary"
                                >
                                    <svelte:component
                                        this={item.icon}
                                        class="h-7 w-7"
                                    />
                                </div>
                                <div>
                                    <p
                                        class="text-[10px] uppercase text-zinc-400"
                                    >
                                        {item.label}
                                    </p>
                                    <p
                                        class="mt-2 font-black text-zinc-900 dark:text-white"
                                    >
                                        {item.value}
                                    </p>
                                </div>
                            </div>
                        {/each}
                    </div>

                    <div
                        class="pt-12 border-t border-zinc-200 dark:border-white/5 space-y-6"
                    >
                        <h4 class="text-xs font-black uppercase text-zinc-400">
                            Ikuti Media Sosial
                        </h4>
                        <div class="flex flex-wrap gap-4">
                            {#each socials as socialItem}
                                {#if socialItem.url}
                                    <a
                                        href={socialItem.url}
                                        target="_blank"
                                        rel="noreferrer"
                                        class="flex h-12 w-12 items-center justify-center rounded-xl border border-zinc-200 bg-white text-zinc-500 transition hover:border-primary hover:text-primary dark:border-white/5 dark:bg-zinc-900 dark:text-zinc-300"
                                    >
                                        <svelte:component
                                            this={socialItem.icon}
                                            class="h-5 w-5"
                                        />
                                    </a>
                                {/if}
                            {/each}
                        </div>
                    </div>
                </div>

                <div class="space-y-8 lg:sticky lg:top-24">
                    <div
                        class="overflow-hidden rounded-[48px] border border-zinc-200 bg-white shadow-2xl dark:border-white/5 dark:bg-zinc-900"
                    >
                        <iframe
                            title=""
                            src={mapSrc}
                            width="100%"
                            height="100%"
                            class="aspect-[4/5] border-0 grayscale opacity-95"
                            allowfullscreen
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                        ></iframe>
                    </div>

                    <a
                        href={mapsUrl || "https://www.google.com/maps"}
                        target="_blank"
                        rel="noreferrer"
                        class="inline-flex h-16 w-full items-center justify-center rounded-2xl border border-zinc-200 bg-white text-sm font-black uppercase text-zinc-900 transition hover:border-primary hover:text-primary dark:border-white/10 dark:bg-zinc-950 dark:text-white"
                    >
                        Buka di Google Maps
                        <ArrowUpRight class="ml-3 h-4 w-4" />
                    </a>
                </div>
            </div>
        </div>
    </section>
</Layout>
