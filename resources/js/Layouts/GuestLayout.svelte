<script>
    import { Link } from "@inertiajs/svelte";
    import { Moon, Sun } from "lucide-svelte";
    import { onMount } from "svelte";

    const navigation = [
        { label: "Program", href: "/program" },
        { label: "Berita", href: "/berita" },
        { label: "Tentang", href: "/tentang" },
        { label: "Kontak", href: "/kontak" },
        { label: "Laporan", href: "/laporan" },
    ];

    let theme = "light";
    const themeKey = "lazismu-theme";

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
    });
</script>

<div
    class="min-h-screen flex flex-col bg-zinc-50 text-zinc-900 dark:bg-zinc-950 dark:text-white"
>
    <header
        class="sticky top-0 z-50 border-b border-zinc-200/70 bg-white/90 backdrop-blur dark:border-white/10 dark:bg-zinc-950/95"
    >
        <div
            class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8"
        >
            <Link href="/" class="flex items-center gap-3">
                <span
                    class="inline-flex h-11 w-11 items-center justify-center rounded-3xl bg-primary/10 text-2xl font-black text-primary"
                    >L</span
                >
                <div>
                    <p class="text-base font-blackem] uppercase text-primary">
                        Lazismu
                    </p>
                    <p
                        class="text-xs uppercase5em] text-zinc-500 dark:text-zinc-400"
                    >
                        Kebaikan & Amanah
                    </p>
                </div>
            </Link>

            <nav class="hidden items-center gap-8 lg:flex">
                {#each navigation as item}
                    <Link
                        href={item.href}
                        class="text-sm font-semibold uppercase2em] text-zinc-700 transition hover:text-primary dark:text-zinc-300 dark:hover:text-white"
                    >
                        {item.label}
                    </Link>
                {/each}
            </nav>

            <div class="flex items-center gap-3">
                <button
                    type="button"
                    on:click={toggleTheme}
                    class="inline-flex h-11 w-11 items-center justify-center rounded-2xl border border-zinc-200 bg-white text-zinc-700 transition hover:border-primary hover:text-primary dark:border-white/10 dark:bg-zinc-950 dark:text-white"
                >
                    {#if theme === "dark"}
                        <Sun class="h-5 w-5" />
                    {:else}
                        <Moon class="h-5 w-5" />
                    {/if}
                </button>
                <Link
                    href="/donasi?type=zakat"
                    class="hidden rounded-2xl bg-primary px-5 py-3 text-sm font-black uppercase text-white shadow-xl shadow-primary/30 transition hover:bg-primary/90 lg:inline-flex"
                >
                    Donasi
                </Link>
            </div>
        </div>
    </header>

    <main class="flex-grow">
        <slot />
    </main>

    <footer
        class="border-t border-zinc-200/80 bg-white/90 py-10 dark:border-white/10 dark:bg-zinc-950/95"
    >
        <div
            class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8 lg:flex-row lg:items-center lg:justify-between"
        >
            <div class="space-y-2">
                <p class="text-sm font-black uppercase text-primary">Lazismu</p>
                <p class="max-w-md text-sm text-zinc-600 dark:text-zinc-400">
                    Mewujudkan donasi amanah yang transparan, cepat, dan
                    berdampak untuk masyarakat.
                </p>
            </div>

            <div
                class="flex flex-wrap gap-4 text-sm font-semibold uppercase text-zinc-700 dark:text-zinc-300"
            >
                {#each navigation as item}
                    <Link
                        href={item.href}
                        class="transition hover:text-primary dark:hover:text-white"
                    >
                        {item.label}
                    </Link>
                {/each}
            </div>
        </div>
    </footer>
</div>
