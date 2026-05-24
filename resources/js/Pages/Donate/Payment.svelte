<script>
    import { Link, useForm } from '@inertiajs/svelte';
    import Layout from '../../Layouts/GuestLayout.svelte';

    export let donation;

    // Temporary list of banks
    const banks = [
        { code: 'BSI', name: 'Bank Syariah Indonesia' },
        { code: 'BRI', name: 'Bank Rakyat Indonesia' },
        { code: 'BCA', name: 'Bank Central Asia' },
        { code: 'MANDIRI', name: 'Bank Mandiri' }
    ];

    let form = useForm({
        payment_method: ''
    });

    function selectBank(code) {
        $form.payment_method = code;
    }

    function submit() {
        $form.post(`/donasi/pilih-pembayaran/${donation.id}`);
    }
</script>

<svelte:head><title>Pilih Pembayaran | Lazismu</title></svelte:head>

<Layout>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-xl shadow-md overflow-hidden p-6 md:p-8">
            <h1 class="text-2xl font-bold mb-6 text-gray-900 border-b pb-4">Pilih Metode Pembayaran</h1>
            
            <div class="mb-8 bg-gray-50 p-4 rounded-md">
                <p class="text-sm text-gray-600">Nominal Donasi:</p>
                <p class="text-2xl font-bold text-primary-600">Rp {new Intl.NumberFormat('id-ID').format(donation.amount)}</p>
                {#if donation.campaign}
                    <p class="text-sm text-gray-600 mt-2">Program: <span class="font-semibold text-gray-900">{donation.campaign.title}</span></p>
                {:else}
                    <p class="text-sm text-gray-600 mt-2">Program: <span class="font-semibold text-gray-900">Donasi Umum</span></p>
                {/if}
            </div>

            <form on:submit|preventDefault={submit} class="space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Transfer Bank</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {#each banks as bank}
                            <button 
                                type="button"
                                on:click={() => selectBank(bank.code)}
                                class="border p-4 rounded-lg flex items-center justify-between transition {$form.payment_method === bank.code ? 'border-primary-500 bg-primary-50 ring-2 ring-primary-200' : 'border-gray-200 hover:border-primary-300'}">
                                <span class="font-semibold text-gray-800">{bank.code}</span>
                                <span class="text-sm text-gray-500">{bank.name}</span>
                            </button>
                        {/each}
                    </div>
                    {#if $form.errors.payment_method}
                        <p class="mt-2 text-sm text-red-600">{$form.errors.payment_method}</p>
                    {/if}
                </div>

                <div class="pt-6 border-t flex justify-between">
                    <Link href={`/donasi/${donation.campaign ? donation.campaign.slug : ''}`} class="py-3 px-6 text-gray-600 font-medium hover:text-gray-900">
                        Kembali
                    </Link>
                    <button 
                        type="submit" 
                        disabled={$form.processing || !$form.payment_method}
                        class="py-3 px-6 border border-transparent rounded-md shadow-sm font-medium text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 disabled:opacity-50 disabled:cursor-not-allowed">
                        {#if $form.processing}
                            Memproses...
                        {:else}
                            Konfirmasi Pembayaran
                        {/if}
                    </button>
                </div>
            </form>
        </div>
    </div>
</Layout>
