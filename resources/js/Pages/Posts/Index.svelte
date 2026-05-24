<script>
    import { Link } from "@inertiajs/svelte";
    import Layout from "../../Layouts/GuestLayout.svelte";

    let { posts = { data: [] }, categories = [], filters = {} } = $props();

    function formatDate(dateStr) {
        if (!dateStr) return "";
        return new Date(dateStr).toLocaleDateString("id-ID", {
            year: "numeric",
            month: "long",
            day: "numeric",
        });
    }
</script>

<svelte:head><title>Berita | Lazismu</title></svelte:head>

<Layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Berita & Artikel</h1>

        {#if posts.data && posts.data.length > 0}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                {#each posts.data as post}
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition">
                        {#if post.featured_image}
                            <img src={post.featured_image} alt={post.title} class="w-full h-48 object-cover" />
                        {:else}
                            <div class="h-48 bg-orange-50 flex items-center justify-center text-4xl">📰</div>
                        {/if}
                        <div class="p-6">
                            {#if post.category}
                                <span class="text-xs font-semibold text-[#FF7300] uppercase">{post.category.name}</span>
                            {/if}
                            <h2 class="font-bold text-lg mt-1 mb-2 line-clamp-2">{post.title}</h2>
                            <p class="text-gray-600 text-sm line-clamp-3">{post.excerpt || ""}</p>
                            <div class="mt-4 flex items-center justify-between">
                                <span class="text-xs text-gray-400">{formatDate(post.published_at)}</span>
                                <Link href={`/berita/${post.slug}`} class="text-[#FF7300] font-semibold text-sm hover:underline">
                                    Baca →
                                </Link>
                            </div>
                        </div>
                    </div>
                {/each}
            </div>

            <!-- Pagination -->
            {#if posts.links}
                <div class="mt-10 flex justify-center gap-2">
                    {#each posts.links as link}
                        {#if link.url}
                            <Link
                                href={link.url}
                                class="px-4 py-2 rounded-lg text-sm font-medium border {link.active ? 'bg-[#FF7300] text-white border-[#FF7300]' : 'bg-white text-gray-700 border-gray-300 hover:border-[#FF7300]'}"
                            >
                                {@html link.label}
                            </Link>
                        {:else}
                            <span class="px-4 py-2 rounded-lg text-sm font-medium border text-gray-400 border-gray-200">{@html link.label}</span>
                        {/if}
                    {/each}
                </div>
            {/if}
        {:else}
            <p class="text-center text-gray-500 py-16">Belum ada artikel yang dipublikasikan.</p>
        {/if}
    </div>
</Layout>
