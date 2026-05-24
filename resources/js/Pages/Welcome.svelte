<script>
    import { Link } from "@inertiajs/svelte";
    import Layout from "../Layouts/GuestLayout.svelte";

    let { featuredCampaigns = [], latestPosts = [], banners = [], stats = {} } = $props();

    function formatCurrency(amount) {
        return new Intl.NumberFormat("id-ID").format(amount || 0);
    }

    function progressPercent(campaign) {
        if (!campaign.target_amount || campaign.target_amount <= 0) return 0;
        return Math.min((campaign.verified_donations_sum_amount / campaign.target_amount) * 100, 100);
    }
</script>

<svelte:head><title>Beranda | Lazismu</title></svelte:head>

<Layout>
    <!-- Hero Section -->
    <div class="bg-[#FF7300] text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
            <h1 class="text-4xl font-bold mb-4">Mari Berbagi untuk Sesama</h1>
            <p class="text-xl mb-8 opacity-90">
                Salurkan donasi terbaik Anda melalui Lazismu Kasihan
            </p>
            <Link
                href="/program"
                class="inline-block bg-white text-[#FF7300] px-6 py-3 rounded-lg font-bold hover:bg-orange-50 transition"
            >
                Mulai Donasi
            </Link>
        </div>
    </div>

    <!-- Stats Section -->
    {#if stats && (stats.total_donations || stats.total_campaigns)}
    <div class="bg-orange-50 border-b border-orange-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div>
                    <div class="text-2xl font-bold text-[#FF7300]">Rp {formatCurrency(stats.total_amount)}</div>
                    <div class="text-sm text-gray-600">Total Terkumpul</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-[#FF7300]">{stats.total_donations || 0}</div>
                    <div class="text-sm text-gray-600">Donatur</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-[#FF7300]">{stats.total_campaigns || 0}</div>
                    <div class="text-sm text-gray-600">Program Aktif</div>
                </div>
                <div>
                    <div class="text-2xl font-bold text-[#FF7300]">{stats.total_muzakki || 0}</div>
                    <div class="text-sm text-gray-600">Muzakki</div>
                </div>
            </div>
        </div>
    </div>
    {/if}

    <!-- Campaigns Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">
            Program Pilihan
        </h2>

        {#if featuredCampaigns.length > 0}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            {#each featuredCampaigns as campaign}
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition">
                    <div class="h-48 bg-orange-100 flex items-center justify-center">
                        {#if campaign.image}
                            <img src={campaign.image} alt={campaign.title} class="w-full h-full object-cover" />
                        {:else}
                            <span class="text-orange-300 text-4xl">🕌</span>
                        {/if}
                    </div>
                    <div class="p-6">
                        <span class="text-xs font-semibold text-[#FF7300] uppercase tracking-wide">
                            {campaign.category?.name || 'Umum'}
                        </span>
                        <h3 class="font-bold text-xl mb-2 mt-1">{campaign.title}</h3>
                        <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                            {campaign.description}
                        </p>
                        <div class="mt-4">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div
                                    class="bg-[#FF7300] h-2 rounded-full transition-all"
                                    style="width: {progressPercent(campaign)}%"
                                ></div>
                            </div>
                            <div class="flex justify-between mt-2 text-sm">
                                <span class="font-semibold text-[#FF7300]">
                                    Rp {formatCurrency(campaign.verified_donations_sum_amount)}
                                </span>
                                <span class="text-gray-500">dari Rp {formatCurrency(campaign.target_amount)}</span>
                            </div>
                        </div>
                        <Link
                            href={`/program/${campaign.slug}`}
                            class="mt-4 block w-full text-center bg-orange-50 text-[#FF7300] border border-[#FF7300] py-2 rounded-lg font-semibold hover:bg-[#FF7300] hover:text-white transition"
                        >
                            Donasi Sekarang
                        </Link>
                    </div>
                </div>
            {/each}
        </div>
        {:else}
        <p class="text-center text-gray-500">Belum ada program aktif saat ini.</p>
        {/if}

        <div class="text-center mt-10">
            <Link
                href="/program"
                class="inline-block bg-[#FF7300] text-white px-8 py-3 rounded-lg font-bold hover:bg-[#CC5C00] transition"
            >
                Lihat Semua Program
            </Link>
        </div>
    </div>

    <!-- Latest Posts -->
    {#if latestPosts.length > 0}
    <div class="bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-8 text-center">Berita Terkini</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                {#each latestPosts as post}
                <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition">
                    <div class="p-6">
                        <span class="text-xs font-semibold text-[#FF7300] uppercase">{post.category?.name || ''}</span>
                        <h3 class="font-bold text-lg mt-1 mb-2 line-clamp-2">{post.title}</h3>
                        <p class="text-gray-600 text-sm line-clamp-3">{post.excerpt || ''}</p>
                        <Link href={`/berita/${post.slug}`} class="mt-4 inline-block text-[#FF7300] font-semibold text-sm hover:underline">
                            Baca Selengkapnya →
                        </Link>
                    </div>
                </div>
                {/each}
            </div>
        </div>
    </div>
    {/if}
</Layout>
