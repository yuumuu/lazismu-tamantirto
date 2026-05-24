<script>
    import { Link, useForm } from '@inertiajs/svelte';
    import Layout from '../../Layouts/GuestLayout.svelte';

    export let campaign = null;

    let form = useForm({
        amount: '',
        is_anonymous: false,
        name: '',
        email: '',
        phone: '',
        message: ''
    });

    const amountOptions = [
        10000, 20000, 50000, 100000, 250000, 500000, 1000000
    ];

    function selectAmount(amt) {
        $form.amount = amt;
    }

    // Determine if campaign is expired
    $: isExpired = campaign && campaign.end_date && new Date(campaign.end_date) < new Date();

    function submit() {
        if (isExpired) return;
        $form.post('/donasi/submit');
    }
</script>

<svelte:head><title>{campaign ? `Donasi - ${campaign.title}` : 'Donasi Umum'}</title></svelte:head>

<Layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-xl shadow-md overflow-hidden p-6 md:p-8">
            {#if campaign}
                <div class="mb-8 pb-8 border-b">
                    <h1 class="text-2xl font-bold mb-2 text-gray-900">Donasi untuk {campaign.title}</h1>
                    {#if isExpired}
                        <div class="bg-red-50 text-red-700 p-4 rounded-md mb-4 border border-red-200">
                            <strong>Perhatian:</strong> Program donasi ini telah berakhir pada {new Date(campaign.end_date).toLocaleDateString('id-ID')}. Anda tidak dapat berdonasi untuk program ini lagi.
                        </div>
                    {/if}
                    <div class="flex items-center text-sm text-gray-600">
                        <span class="mr-4">Target: Rp {new Intl.NumberFormat('id-ID').format(campaign.target_amount || 0)}</span>
                        <span>Terkumpul: Rp {new Intl.NumberFormat('id-ID').format(campaign.verified_donations_sum_amount || 0)}</span>
                    </div>
                </div>
            {:else}
                <div class="mb-8 pb-8 border-b">
                    <h1 class="text-2xl font-bold mb-2 text-gray-900">Donasi Umum</h1>
                    <p class="text-gray-600">Salurkan donasi Anda untuk berbagai program kebaikan Lazismu.</p>
                </div>
            {/if}

            <form on:submit|preventDefault={submit} class="space-y-6">
                <!-- Nominal Donasi -->
                <div>
                    <div class="block text-sm font-medium text-gray-700 mb-2">Pilih Nominal Donasi</div>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                        {#each amountOptions as amt}
                            <button 
                                type="button" 
                                on:click={() => selectAmount(amt)}
                                disabled={isExpired}
                                class="py-2 px-4 border rounded-md font-medium text-sm transition {$form.amount === amt ? 'bg-primary-600 text-white border-primary-600' : 'bg-white text-gray-700 border-gray-300 hover:border-primary-500'} disabled:opacity-50 disabled:cursor-not-allowed">
                                Rp {new Intl.NumberFormat('id-ID').format(amt)}
                            </button>
                        {/each}
                    </div>
                    <div>
                        <label for="custom_amount" class="block text-sm font-medium text-gray-700">Nominal Lainnya</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">Rp</span>
                            </div>
                            <input 
                                type="number" 
                                id="custom_amount" 
                                bind:value={$form.amount} 
                                disabled={isExpired}
                                class="focus:ring-primary-500 focus:border-primary-500 block w-full pl-10 sm:text-sm border-gray-300 rounded-md py-3 disabled:opacity-50 disabled:bg-gray-100" 
                                placeholder="0">
                        </div>
                        {#if $form.errors.amount}
                            <p class="mt-2 text-sm text-red-600">{$form.errors.amount}</p>
                        {/if}
                    </div>
                </div>

                <!-- Informasi Donatur -->
                <div class="space-y-4 pt-4 border-t">
                    <h3 class="text-lg font-medium text-gray-900">Informasi Donatur</h3>
                    
                    <div class="flex items-center">
                        <input id="is_anonymous" type="checkbox" bind:checked={$form.is_anonymous} disabled={isExpired} class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 rounded disabled:opacity-50">
                        <label for="is_anonymous" class="ml-2 block text-sm text-gray-900">
                            Sembunyikan nama saya (Hamba Allah)
                        </label>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <input type="text" id="name" bind:value={$form.name} disabled={isExpired} class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md py-2 disabled:opacity-50 disabled:bg-gray-100">
                            {#if $form.errors.name}
                                <p class="mt-1 text-sm text-red-600">{$form.errors.name}</p>
                            {/if}
                        </div>
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700">Nomor WhatsApp</label>
                            <input type="tel" id="phone" bind:value={$form.phone} disabled={isExpired} class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md py-2 disabled:opacity-50 disabled:bg-gray-100" placeholder="08...">
                            {#if $form.errors.phone}
                                <p class="mt-1 text-sm text-red-600">{$form.errors.phone}</p>
                            {/if}
                        </div>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email (Opsional)</label>
                        <input type="email" id="email" bind:value={$form.email} disabled={isExpired} class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md py-2 disabled:opacity-50 disabled:bg-gray-100">
                        {#if $form.errors.email}
                            <p class="mt-1 text-sm text-red-600">{$form.errors.email}</p>
                        {/if}
                    </div>

                    <div>
                        <label for="message" class="block text-sm font-medium text-gray-700">Doa / Pesan (Opsional)</label>
                        <textarea id="message" rows="3" bind:value={$form.message} disabled={isExpired} class="mt-1 focus:ring-primary-500 focus:border-primary-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md disabled:opacity-50 disabled:bg-gray-100"></textarea>
                    </div>
                </div>

                <div class="pt-6">
                    <button 
                        type="submit" 
                        disabled={isExpired || $form.processing}
                        class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-lg font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        {#if isExpired}
                            Donasi Ditutup
                        {:else if $form.processing}
                            Memproses...
                        {:else}
                            Lanjutkan Pembayaran
                        {/if}
                    </button>
                </div>
            </form>
        </div>
    </div>
</Layout>
