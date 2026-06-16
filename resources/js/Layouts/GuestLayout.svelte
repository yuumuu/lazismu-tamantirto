<script>
    import { Link, page } from "@inertiajs/svelte";
    import Sun from "lucide-svelte/icons/sun";
    import Moon from "lucide-svelte/icons/moon";
    import Menu from "lucide-svelte/icons/menu";
    import X from "lucide-svelte/icons/x";
    import Home from "lucide-svelte/icons/home";
    import Heart from "lucide-svelte/icons/heart";
    import Calculator from "lucide-svelte/icons/calculator";
    import BookOpen from "lucide-svelte/icons/book-open";
    import Plus from "lucide-svelte/icons/plus";
    import MapPin from "lucide-svelte/icons/map-pin";
    import Phone from "lucide-svelte/icons/phone";
    import Mail from "lucide-svelte/icons/mail";
    import ArrowRight from "lucide-svelte/icons/arrow-right";
    import { onMount } from "svelte";

    let theme = $state("light");
    let mobileOpen = $state(false);
    let tentangOpen = $state(false);
    let tentangTimeout = $state(null);
    const themeKey = "lazismu-theme";

    const site = $derived(page.props.site || {});
    const url = $derived(page.url);

    const navLinks = [
        { label: "Beranda", href: "/", pattern: /^\/$/ },
        { label: "Donasi", href: "/program", pattern: /^\/program/ },
        { label: "Berita", href: "/berita", pattern: /^\/berita/ },
        { label: "Tentang", href: "/tentang", pattern: /^\/tentang/ },
        { label: "Kontak", href: "/kontak", pattern: /^\/kontak/ },
    ];

    const bottomNavItems = [
        { label: "Beranda", href: "/", icon: Home, pattern: /^\/$/ },
        {
            label: "Donasi",
            href: "/program",
            icon: Heart,
            pattern: /^\/program/,
        },
        { label: null, href: "/donasi", icon: Plus, isFab: true },
        {
            label: "Zakat",
            href: "/zakat-kalkulator",
            icon: Calculator,
            pattern: /^\/zakat-kalkulator/,
        },
        {
            label: "Tentang",
            href: "/tentang",
            icon: BookOpen,
            pattern: /^\/tentang/,
        },
    ];

    const isActive = (pattern) => pattern?.test(url);

    const applyTheme = (value) => {
        theme = value;
        document.documentElement.classList.toggle("dark", value === "dark");
        window.localStorage.setItem(themeKey, value);
    };

    const toggleTheme = () => {
        applyTheme(theme === "dark" ? "light" : "dark");
    };

    onMount(() => {
        const storedTheme = window.localStorage.getItem(themeKey);
        const prefersDark = window.matchMedia(
            "(prefers-color-scheme: dark)",
        ).matches;
        applyTheme(storedTheme || (prefersDark ? "dark" : "light"));

        return () => clearTimeout(tentangTimeout);
    });
</script>

<div
    class="min-h-screen flex flex-col bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 antialiased selection:bg-primary/20 selection:text-primary"
>
    <!-- Navbar -->
    <nav
        class="sticky top-0 left-0 w-full bg-white/80 dark:bg-zinc-900/80 backdrop-blur-md z-[99] border-b border-zinc-200 dark:border-white/10"
    >
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="relative flex h-20 items-center justify-between">
                <div
                    class="flex flex-1 items-center justify-start sm:items-stretch sm:justify-between"
                >
                    <!-- Logo -->
                    <div class="flex shrink-0 items-center">
                        <a href="/" class="flex items-center gap-3 group">
                            <div
                                class="flex aspect-square size-10 items-center justify-center rounded-xl group-hover:scale-105 transition-transform"
                            >
                                <img
                                    src="/favicon.png"
                                    class="size-8"
                                    alt="Logo"
                                />
                            </div>
                            <div class="flex flex-col leading-tight">
                                <span
                                    class="text-zinc-900 dark:text-white font-black text-xl tracking-tighter uppercase"
                                    >{site.name || "Lazismu"}</span
                                >
                                <span
                                    class="text-primary font-bold text-[10px] uppercase"
                                    >{site.tagline || "Tamantirto"}</span
                                >
                            </div>
                        </a>
                    </div>

                    <!-- Desktop Nav -->
                    <div class="hidden lg:ml-6 lg:block">
                        <div
                            class="flex space-x-1.5 items-center bg-zinc-100/80 dark:bg-zinc-900/80 p-1.5 rounded-2xl border border-zinc-200/50 dark:border-zinc-700/50 shadow-inner"
                        >
                            {#each navLinks as link}
                                {#if link.label === "Tentang"}
                                    <div class="relative">
                                        <button
                                            class="relative z-10 px-5 py-2.5 text-sm font-bold transition-all duration-300 whitespace-nowrap cursor-pointer rounded-xl flex items-center gap-1
                                            {isActive(link.pattern)
                                                ? 'bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white shadow-lg'
                                                : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-white/60 dark:hover:bg-zinc-800/60'}"
                                            style={isActive(link.pattern)
                                                ? "border-radius: 14px 6px 14px 6px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1), inset 0 1px 0 0 rgba(255,255,255,0.1);"
                                                : ""}
                                            onclick={() => (tentangOpen = !tentangOpen)}
                                            onmouseenter={() => { clearTimeout(tentangTimeout); tentangOpen = true; }}
                                            onmouseleave={() => { tentangTimeout = setTimeout(() => (tentangOpen = false), 150); }}
                                        >
                                            {link.label}
                                            <svg class="size-3 transition-transform {tentangOpen ? 'rotate-180' : ''}" viewBox="0 0 12 12" fill="none">
                                                <path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </button>
                                        {#if tentangOpen}
                                            <div
                                                class="absolute top-full left-0 mt-2 w-56 bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 shadow-xl py-2 z-50"
                                                onmouseenter={() => { clearTimeout(tentangTimeout); tentangOpen = true; }}
                                                onmouseleave={() => { tentangTimeout = setTimeout(() => (tentangOpen = false), 150); }}
                                            >
                                                <Link href="/tentang" class="block px-5 py-3 text-sm font-bold text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-white/5 transition-all rounded-xl" onclick={() => (tentangOpen = false)}>Tentang Kami</Link>
                                                <Link href="/struktur" class="block px-5 py-3 text-sm font-bold text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-white/5 transition-all rounded-xl" onclick={() => (tentangOpen = false)}>Struktur Organisasi</Link>
                                            </div>
                                        {/if}
                                    </div>
                                {:else}
                                    <Link
                                        href={link.href}
                                        class="relative z-10 px-5 py-2.5 text-sm font-bold transition-all duration-300 whitespace-nowrap cursor-pointer rounded-xl
                                        {isActive(link.pattern)
                                            ? 'bg-white dark:bg-zinc-800 text-zinc-900 dark:text-white shadow-lg'
                                            : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-white/60 dark:hover:bg-zinc-800/60'}"
                                        style={isActive(link.pattern)
                                            ? "border-radius: 14px 6px 14px 6px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -2px rgba(0,0,0,0.1), inset 0 1px 0 0 rgba(255,255,255,0.1);"
                                            : ""}
                                    >
                                        {link.label}
                                    </Link>
                                {/if}
                            {/each}
                        </div>
                    </div>

                    <!-- Right Actions -->
                    <div class="hidden lg:flex items-center gap-3 ml-6">
                        <button
                            onclick={toggleTheme}
                            class="p-2 rounded-xl bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-600 dark:text-zinc-400 hover:text-primary transition-all"
                        >
                            {#if theme === "dark"}
                                <Sun class="size-5" />
                            {:else}
                                <Moon class="size-5" />
                            {/if}
                        </button>
                        <Link
                            href="/donasi"
                            class="rounded-2xl bg-primary px-5 py-3 text-sm font-black uppercase text-white shadow-xl shadow-primary/30 hover:bg-primary/90 transition-all"
                        >
                            Donasi Sekarang
                        </Link>
                    </div>

                    <!-- Hamburger -->
                    <div
                        class="absolute inset-y-0 right-0 flex items-center lg:hidden"
                    >
                        <button
                            onclick={() => (mobileOpen = !mobileOpen)}
                            class="relative inline-flex items-center justify-center rounded-xl p-2.5 text-zinc-500 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-white/5 transition-all"
                        >
                            {#if mobileOpen}
                                <X class="size-6" />
                            {:else}
                                <Menu class="size-6" />
                            {/if}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        {#if mobileOpen}
            <div
                class="lg:hidden border-t border-zinc-200 dark:border-white/10 bg-white dark:bg-zinc-900 shadow-2xl"
                transition:slide
            >
                <div class="px-6 py-6 space-y-2">
                    {#each navLinks as link}
                        {#if link.label === "Tentang"}
                            <div>
                                <button
                                    onclick={() => (tentangOpen = !tentangOpen)}
                                    class="flex items-center justify-between w-full px-5 py-3 rounded-xl text-sm font-bold transition-all text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-white/5"
                                >
                                    Tentang
                                    <svg class="size-3 transition-transform {tentangOpen ? 'rotate-180' : ''}" viewBox="0 0 12 12" fill="none">
                                        <path d="M3 4.5L6 7.5L9 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                                {#if tentangOpen}
                                    <div class="ml-4 mt-1 space-y-1">
                                        <Link
                                            href="/tentang"
                                            onclick={() => { mobileOpen = false; tentangOpen = false; }}
                                            class="block px-5 py-3 rounded-xl text-sm font-bold transition-all text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-white/5"
                                        >
                                            Tentang Kami
                                        </Link>
                                        <Link
                                            href="/struktur"
                                            onclick={() => { mobileOpen = false; tentangOpen = false; }}
                                            class="block px-5 py-3 rounded-xl text-sm font-bold transition-all text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-white/5"
                                        >
                                            Struktur Organisasi
                                        </Link>
                                    </div>
                                {/if}
                            </div>
                        {:else}
                            <Link
                                href={link.href}
                                onclick={() => (mobileOpen = false)}
                                class="block px-5 py-3 rounded-xl text-sm font-bold transition-all
                                {isActive(link.pattern)
                                    ? 'bg-primary text-white shadow-lg'
                                    : 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100 dark:hover:bg-white/5'}"
                            >
                                {link.label}
                            </Link>
                        {/if}
                    {/each}
                    <hr class="my-4 border-zinc-200 dark:border-white/10" />
                    <Link
                        href="/donasi"
                        onclick={() => (mobileOpen = false)}
                        class="block w-full text-center rounded-2xl bg-primary px-5 py-3 text-sm font-black uppercase text-white shadow-xl shadow-primary/30"
                    >
                        Donasi Sekarang
                    </Link>
                </div>
            </div>
        {/if}
    </nav>

    <!-- Main Content -->
    <main class="flex-grow pb-24 lg:pb-0">
        <slot />
    </main>

    <!-- Footer -->
    <footer
        class="bg-zinc-50 dark:bg-zinc-900 border-t border-zinc-200 dark:border-white/5 pt-16 pb-32 lg:pb-16"
    >
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-12">
                <!-- Branding -->
                <div class="space-y-6 lg:col-span-2">
                    <a href="/" class="flex items-center gap-3">
                        <div
                            class="flex aspect-square size-10 items-center justify-center rounded-xl"
                        >
                            <img src="/favicon.png" class="size-8" alt="Logo" />
                        </div>
                        <div class="flex flex-col leading-tight">
                            <span
                                class="text-zinc-900 dark:text-white font-black text-xl tracking-tighter uppercase"
                                >{site.name || "Lazismu"}</span
                            >
                            <span
                                class="text-primary font-bold text-[10px] uppercase"
                                >{site.tagline || "Tamantirto"}</span
                            >
                        </div>
                    </a>
                    <p class="text-zinc-500 dark:text-zinc-400 text-sm">
                        {site.description || "Memberi untuk negeri."}
                    </p>
                    <div class="flex gap-4">
                        {#each Object.entries(site.social || {}).filter(([, v]) => v) as [platform, url]}
                            <a
                                href={url}
                                target="_blank"
                                rel="noopener noreferrer"
                                class="p-2 rounded-lg bg-white dark:bg-white/5 border border-zinc-200 dark:border-white/10 text-zinc-600 dark:text-zinc-400 hover:text-primary transition-colors"
                            >
                                <span class="text-xs font-black uppercase"
                                    >{platform[0]}</span
                                >
                            </a>
                        {/each}
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-zinc-900 dark:text-white font-bold mb-6">
                        Tautan Cepat
                    </h3>
                    <ul class="space-y-4">
                        <li>
                            <a
                                href="/program"
                                class="text-zinc-500 dark:text-zinc-400 hover:text-primary transition-colors text-sm"
                                >Program Donasi</a
                            >
                        </li>
                        <li>
                            <a
                                href="/berita"
                                class="text-zinc-500 dark:text-zinc-400 hover:text-primary transition-colors text-sm"
                                >Berita & Artikel</a
                            >
                        </li>
                        <li>
                            <a
                                href="/tentang"
                                class="text-zinc-500 dark:text-zinc-400 hover:text-primary transition-colors text-sm"
                                >Tentang Kami</a
                            >
                        </li>
                        <li>
                            <a
                                href="/kontak"
                                class="text-zinc-500 dark:text-zinc-400 hover:text-primary transition-colors text-sm"
                                >Hubungi Kami</a
                            >
                        </li>
                    </ul>
                </div>

                <!-- Pages -->
                {#if site.pages?.length > 0}
                    <div>
                        <h3
                            class="text-zinc-900 dark:text-white font-bold mb-6"
                        >
                            Halaman Kami
                        </h3>
                        <ul class="space-y-4">
                            {#each site.pages as page}
                                <li>
                                    <a
                                        href="#"
                                        class="text-zinc-500 dark:text-zinc-400 hover:text-primary transition-colors text-sm"
                                        >{page.title}</a
                                    >
                                </li>
                            {/each}
                        </ul>
                    </div>
                {/if}

                <!-- Services -->
                <div>
                    <h3 class="text-zinc-900 dark:text-white font-bold mb-6">
                        Layanan
                    </h3>
                    <ul class="space-y-4">
                        <li>
                            <a
                                href="/donasi"
                                class="text-zinc-500 dark:text-zinc-400 hover:text-primary transition-colors text-sm"
                                >Zakat Online</a
                            >
                        </li>
                        <li>
                            <a
                                href="/donasi"
                                class="text-zinc-500 dark:text-zinc-400 hover:text-primary transition-colors text-sm"
                                >Infaq & Sedekah</a
                            >
                        </li>
                        <li>
                            <a
                                href="/zakat-kalkulator"
                                class="text-zinc-500 dark:text-zinc-400 hover:text-primary transition-colors text-sm"
                                >Kalkulator Zakat</a
                            >
                        </li>
                        <li>
                            <a
                                href="/laporan"
                                class="text-zinc-500 dark:text-zinc-400 hover:text-primary transition-colors text-sm"
                                >Laporan Keuangan</a
                            >
                        </li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h3 class="text-zinc-900 dark:text-white font-bold mb-6">
                        Kontak Kami
                    </h3>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <MapPin
                                class="size-5 text-primary shrink-0 mt-0.5"
                            />
                            <span
                                class="text-zinc-500 dark:text-zinc-400 text-sm italic"
                                >{site.address ||
                                    "Jl. Brawijaya, Tamantirto, Kasihan, Bantul, DIY"}</span
                            >
                        </li>
                        <li class="flex items-center gap-3">
                            <Phone class="size-5 text-primary shrink-0" />
                            <span
                                class="text-zinc-500 dark:text-zinc-400 text-sm"
                                >{site.phone || "0812-3456-7890"}</span
                            >
                        </li>
                        <li class="flex items-center gap-3">
                            <Mail class="size-5 text-primary shrink-0" />
                            <span
                                class="text-zinc-500 dark:text-zinc-400 text-sm"
                                >{site.email || "lazismu@umy.ac.id"}</span
                            >
                        </li>
                    </ul>
                </div>
            </div>

            <div
                class="mt-16 pt-8 border-t border-zinc-200 dark:border-white/5 flex flex-col md:flex-row justify-between items-center gap-4"
            >
                <p class="text-zinc-400 dark:text-zinc-500 text-xs">
                    {site.footer_text ||
                        `© ${new Date().getFullYear()} Lazismu Tamantirto. All rights reserved.`}
                </p>
            </div>
        </div>
    </footer>

    <!-- Bottom Navigation (Mobile) -->
    <div
        class="lg:hidden fixed bottom-0 left-0 right-0 z-[100] px-4 pb-4 pointer-events-none"
    >
        <div
            class="mx-auto max-w-md w-full bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl border border-zinc-200 dark:border-white/10 rounded-2xl shadow-2xl pointer-events-auto flex items-center justify-around p-2"
        >
            {#each bottomNavItems as item}
                {#if item.isFab}
                    <div class="relative -top-6">
                        <a
                            href={item.href}
                            class="flex items-center justify-center size-14 rounded-xl bg-primary text-white shadow-xl shadow-primary/40 hover:scale-105 active:scale-95 transition-all"
                        >
                            <item.icon class="size-8" />
                        </a>
                    </div>
                {:else}
                    <a
                        href={item.href}
                        class="flex flex-col items-center gap-1 p-2 rounded-xl transition-colors {isActive(
                            item.pattern,
                        )
                            ? 'text-primary'
                            : 'text-zinc-500 dark:text-zinc-400'}"
                    >
                        <item.icon class="size-6" />
                        <span
                            class="text-[10px] font-bold uppercase tracking-wider"
                            >{item.label}</span
                        >
                    </a>
                {/if}
            {/each}
        </div>
    </div>
</div>
