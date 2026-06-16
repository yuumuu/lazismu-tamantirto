<script>
    import ArrowRight from "lucide-svelte/icons/arrow-right";
    import { Link } from "@inertiajs/svelte";
    export let posts = [];

    const formatDate = (value) => {
        if (!value) return "";
        try {
            const date = new Date(value);
            return new Intl.DateTimeFormat("id-ID", {
                day: "numeric",
                month: "long",
                year: "numeric",
            }).format(date);
        } catch {
            return value;
        }
    };
</script>

<section class="bg-zinc-50 py-24 dark:bg-zinc-900/50">
    <div class="mx-auto max-w-7xl px-6 lg:px-8">
        <div class="text-center space-y-4 mb-16">
            <p class="text-xs font-black uppercase text-primary">
                Berita Terbaru
            </p>
            <h2
                class="text-3xl font-black tracking-tight text-zinc-900 dark:text-white sm:text-4xl"
            >
                Kabar Lazismu Terbaru
            </h2>
        </div>

        <div class="grid gap-8 md:grid-cols-3">
            {#each posts as post}
                <article
                    class="group overflow-hidden rounded-[36px] border border-zinc-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-xl dark:border-white/5 dark:bg-zinc-950"
                >
                    <div
                        class="relative aspect-video overflow-hidden bg-zinc-100 text-zinc-500"
                    >
                        {#if post.featured_image}
                            <img
                                src={post.featured_image.startsWith("http")
                                    ? post.featured_image
                                    : `/storage/${post.featured_image}`}
                                alt={post.title}
                                class="h-full w-full object-cover transition duration-700 group-hover:scale-105"
                            />
                        {:else}
                            <div
                                class="flex h-full items-center justify-center text-6xl"
                            >
                                📰
                            </div>
                        {/if}
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-zinc-950/80 via-transparent to-transparent"
                        ></div>
                    </div>
                    <div class="p-8 space-y-4">
                        <div class="space-y-2">
                            <p
                                class="text-xs font-black uppercase text-zinc-500 dark:text-zinc-400"
                            >
                                {post.category?.name || "Update"}
                            </p>
                            <p
                                class="text-[11px] uppercase text-zinc-400 dark:text-zinc-500"
                            >
                                {formatDate(
                                    post.published_at || post.created_at,
                                )}
                            </p>
                        </div>
                        <h3
                            class="text-xl font-black leading-tight tracking-tight text-zinc-900 dark:text-white line-clamp-2"
                        >
                            <Link href={`/berita/${post.slug}`}
                                >{post.title}</Link
                            >
                        </h3>
                        <p
                            class="text-sm text-zinc-600 dark:text-zinc-400 line-clamp-2"
                        >
                            {post.short_description ||
                                post.excerpt ||
                                "Informasi mendalam tentang kegiatan dan program Lazismu."}
                        </p>
                        <Link
                            href={`/berita/${post.slug}`}
                            class="inline-flex items-center gap-2 text-sm font-semibold text-primary transition hover:underline"
                        >
                            Baca Selengkapnya
                            <ArrowRight class="h-4 w-4" />
                        </Link>
                    </div>
                </article>
            {/each}
        </div>
    </div>
</section>
