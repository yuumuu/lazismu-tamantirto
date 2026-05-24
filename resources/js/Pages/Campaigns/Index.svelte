<script>
    import { Link, useForm, router } from '@inertiajs/svelte';
    import Layout from '../../Layouts/GuestLayout.svelte';

    export let campaigns = { data: [] };
    export let categories = [];
    export let filters = {};

    let form = useForm({
        category: filters.category || '',
        type: filters.type || '',
        search: filters.search || ''
    });

    function submitFilter() {
        form.get('/program', {
            preserveState: true,
            replace: true
        });
    }

    // Determine if campaign is expired
    const isExpired = (endDate) => endDate && new Date(endDate) < new Date();
</script>

<svelte:head><title>Program Donasi | Lazismu</title></svelte:head>

<Layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Program Donasi</h1>
            <Link href="/donasi" class="bg-primary-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-primary-700 transition">Donasi Umum</Link>
        </div>

        <div class="bg-white p-6 rounded-xl shadow-sm border mb-8">
            <form on:submit|preventDefault={submitFilter} class="flex flex-col md:flex-row gap-4">
                <div class="flex-grow">
                    <input type="text" bind:value={$form.search} placeholder="Cari program..." class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500">
                </div>
                <div class="w-full md:w-48">
                    <select bind:value={$form.category} class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="">Semua Kategori</option>
                        {#each categories as category}
                            <option value={category.id}>{category.name}</option>
                        {/each}
                    </select>
                </div>
                <div class="w-full md:w-48">
                    <select bind:value={$form.type} class="w-full border-gray-300 rounded-md shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="">Semua Tipe</option>
                        <option value="zakat">Zakat</option>
                        <option value="infaq">Infaq</option>
                        <option value="sedekah">Sedekah</option>
                        <option value="wakaf">Wakaf</option>
                    </select>
                </div>
                <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded-md font-medium hover:bg-gray-900">Filter</button>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            {#each campaigns.data as campaign}
                <div class="bg-white rounded-xl shadow-md overflow-hidden flex flex-col transition hover:shadow-lg {isExpired(campaign.end_date) ? 'opacity-75' : ''}">
                    <div class="h-48 bg-gray-200 relative">
                        <!-- image placeholder -->
                        {#if isExpired(campaign.end_date)}
                            <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center">
                                <span class="bg-red-600 text-white px-3 py-1 rounded-full text-sm font-bold">Ditutup</span>
                            </div>
                        {/if}
                    </div>
                    <div class="p-6 flex-grow flex flex-col">
                        <div class="text-xs font-semibold text-primary-600 mb-2 uppercase tracking-wide">
                            {campaign.category?.name || 'Umum'} • {campaign.type}
                        </div>
                        <h3 class="font-bold text-xl mb-2 text-gray-900 line-clamp-2">
                            <Link href={`/program/${campaign.slug}`} class="hover:text-primary-600">{campaign.title}</Link>
                        </h3>
                        <div class="mt-auto">
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-4">
                                <div class="bg-primary-500 h-2 rounded-full" style="width: {campaign.target_amount > 0 ? Math.min((campaign.verified_donations_sum_amount / campaign.target_amount) * 100, 100) : 0}%"></div>
                            </div>
                            <div class="flex justify-between mt-2 text-sm text-gray-600">
                                <span class="font-semibold text-gray-900">Rp {new Intl.NumberFormat('id-ID').format(campaign.verified_donations_sum_amount || 0)}</span>
                                <span>Terkumpul</span>
                            </div>
                        </div>
                    </div>
                </div>
            {:else}
                <div class="col-span-full text-center py-12 text-gray-500">
                    Belum ada program donasi yang sesuai dengan kriteria pencarian.
                </div>
            {/each}
        </div>
        
        <!-- Basic Pagination logic can be added here if campaigns.links exist -->
    </div>
</Layout>
