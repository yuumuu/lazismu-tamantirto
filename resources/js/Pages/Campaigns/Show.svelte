<script>
    import { Link } from '@inertiajs/svelte';
    import Layout from '../../Layouts/GuestLayout.svelte';

    export let campaign;
    export let donors = [];
    export let donorsCount = 0;
    export let relatedPosts = [];

    // Determine if campaign is expired
    $: isExpired = campaign.end_date && new Date(campaign.end_date) < new Date();
</script>

<svelte:head><title>{`${campaign.title} | Lazismu`}</title></svelte:head>

<Layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="lg:col-span-2 space-y-8">
                <!-- Main Info -->
                <div class="bg-white rounded-xl shadow-md overflow-hidden">
                    <div class="h-64 sm:h-96 bg-gray-200 relative">
                        <!-- image placeholder -->
                        {#if isExpired}
                            <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                <span class="bg-red-600 text-white px-4 py-2 rounded-full text-lg font-bold">Program Telah Berakhir</span>
                            </div>
                        {/if}
                    </div>
                    <div class="p-6 md:p-8">
                        <div class="flex items-center text-sm text-gray-500 mb-4">
                            <span class="bg-primary-50 text-primary-700 px-3 py-1 rounded-full font-medium">{campaign.category?.name || 'Umum'}</span>
                            <span class="mx-3">•</span>
                            <span>{campaign.masjid?.name || campaign.branch?.name || 'Lazismu'}</span>
                        </div>
                        
                        <h1 class="text-3xl font-bold text-gray-900 mb-6">{campaign.title}</h1>
                        
                        <div class="prose max-w-none text-gray-700">
                            {@html campaign.description}
                        </div>
                    </div>
                </div>

                <!-- Related News (Penyaluran Donasi) -->
                {#if relatedPosts.length > 0}
                    <div class="bg-white rounded-xl shadow-md p-6 md:p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6 border-b pb-4">Kabar Penyaluran Donasi</h2>
                        <div class="space-y-6">
                            {#each relatedPosts as post}
                                <div class="flex items-start gap-4">
                                    <div class="w-24 h-24 bg-gray-200 rounded-lg flex-shrink-0"></div>
                                    <div>
                                        <h3 class="font-bold text-lg hover:text-primary-600">
                                            <Link href={`/berita/${post.slug}`}>{post.title}</Link>
                                        </h3>
                                        <p class="text-sm text-gray-500 mb-2">{new Date(post.published_at).toLocaleDateString('id-ID')}</p>
                                        <p class="text-gray-700 text-sm line-clamp-2">{post.excerpt || post.content.replace(/(<([^>]+)>)/gi, "").substring(0, 100)}...</p>
                                    </div>
                                </div>
                            {/each}
                        </div>
                    </div>
                {/if}
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-1 space-y-8">
                <!-- Donation Widget -->
                <div class="bg-white rounded-xl shadow-md p-6 sticky top-6">
                    <h3 class="font-semibold text-gray-900 mb-4">Progres Donasi</h3>
                    
                    <div class="text-3xl font-bold text-primary-600 mb-2">
                        Rp {new Intl.NumberFormat('id-ID').format(campaign.verified_donations_sum_amount || 0)}
                    </div>
                    
                    {#if campaign.target_amount > 0}
                        <p class="text-sm text-gray-500 mb-4">
                            terkumpul dari target Rp {new Intl.NumberFormat('id-ID').format(campaign.target_amount)}
                        </p>
                        
                        <div class="w-full bg-gray-200 rounded-full h-3 mb-6">
                            <div class="bg-primary-500 h-3 rounded-full" style="width: {Math.min((campaign.verified_donations_sum_amount / campaign.target_amount) * 100, 100)}%"></div>
                        </div>
                    {/if}

                    <div class="flex justify-between text-center border-y py-4 mb-6">
                        <div>
                            <div class="text-2xl font-bold text-gray-900">{donorsCount}</div>
                            <div class="text-xs text-gray-500 uppercase">Donatur</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-gray-900">
                                {campaign.end_date ? Math.max(0, Math.ceil((new Date(campaign.end_date) - new Date()) / (1000 * 60 * 60 * 24))) : '∞'}
                            </div>
                            <div class="text-xs text-gray-500 uppercase">Hari Lagi</div>
                        </div>
                    </div>

                    <Link 
                        href={`/donasi/${campaign.slug}`} 
                        disabled={isExpired}
                        class="w-full block text-center py-3 px-4 rounded-lg font-bold transition {isExpired ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-primary-600 text-white hover:bg-primary-700'}">
                        {isExpired ? 'Donasi Ditutup' : 'Donasi Sekarang'}
                    </Link>
                </div>

                <!-- Latest Donors -->
                {#if donors.length > 0}
                    <div class="bg-white rounded-xl shadow-md p-6">
                        <h3 class="font-semibold text-gray-900 mb-4 border-b pb-2">Donatur Terbaru ({donorsCount})</h3>
                        <div class="space-y-4">
                            {#each donors as donor}
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 font-bold">
                                        {donor.is_anonymous ? 'HA' : (donor.donor_name || 'NN').substring(0, 2).toUpperCase()}
                                    </div>
                                    <div>
                                        <p class="font-medium text-sm text-gray-900">{donor.is_anonymous ? 'Hamba Allah' : donor.donor_name}</p>
                                        <p class="text-xs text-gray-500">Rp {new Intl.NumberFormat('id-ID').format(donor.amount)} • {new Date(donor.created_at).toLocaleDateString('id-ID')}</p>
                                    </div>
                                </div>
                            {/each}
                        </div>
                    </div>
                {/if}
            </div>
        </div>
    </div>
</Layout>
