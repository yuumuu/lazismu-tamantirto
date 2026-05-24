<script>
    import { Link } from "@inertiajs/svelte";
    import Layout from "../../Layouts/GuestLayout.svelte";

    let { post } = $props();

    function formatDate(dateStr) {
        if (!dateStr) return "";
        return new Date(dateStr).toLocaleDateString("id-ID", {
            year: "numeric",
            month: "long",
            day: "numeric",
        });
    }
</script>

<svelte:head><title>{post.title} | Lazismu</title></svelte:head>

<Layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="mb-6">
            <Link href="/berita" class="text-[#FF7300] text-sm hover:underline">← Kembali ke Berita</Link>
        </div>

        {#if post.category}
            <span class="text-xs font-semibold text-[#FF7300] uppercase">{post.category.name}</span>
        {/if}
        <h1 class="text-3xl font-bold text-gray-900 mt-2 mb-4">{post.title}</h1>

        <div class="flex items-center gap-4 text-sm text-gray-500 mb-8 pb-8 border-b">
            {#if post.author}
                <span>Oleh {post.author.name}</span>
            {/if}
            <span>{formatDate(post.published_at)}</span>
            {#if post.reading_time}
                <span>{post.reading_time} menit baca</span>
            {/if}
        </div>

        {#if post.featured_image}
            <img src={post.featured_image} alt={post.title} class="w-full rounded-xl mb-8 object-cover max-h-96" />
        {/if}

        <div class="prose prose-orange max-w-none text-gray-700 leading-relaxed">
            {@html post.content}
        </div>
    </div>
</Layout>
