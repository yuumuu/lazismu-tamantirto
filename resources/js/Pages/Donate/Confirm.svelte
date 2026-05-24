<script>
    import { Link } from '@inertiajs/svelte';
    import Layout from '../../Layouts/GuestLayout.svelte';

    export let donation;
</script>

<svelte:head><title>Konfirmasi Pembayaran | Lazismu</title></svelte:head>

<Layout>
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="bg-primary-600 px-6 py-8 text-center text-white">
                <h1 class="text-2xl font-bold mb-2">Instruksi Pembayaran</h1>
                <p>Segera lakukan pembayaran sesuai detail di bawah ini</p>
            </div>
            
            <div class="p-6 md:p-8 space-y-6">
                <div class="text-center">
                    <p class="text-sm text-gray-500 mb-1">Total Pembayaran</p>
                    <p class="text-4xl font-bold text-gray-900">Rp {new Intl.NumberFormat('id-ID').format(donation.amount)}</p>
                </div>

                <div class="bg-gray-50 p-6 rounded-lg border border-gray-100 text-center">
                    <p class="text-sm text-gray-500 mb-2">Transfer ke Rekening {donation.payment_method || 'Bank'}</p>
                    <!-- Dummy account numbers for demonstration -->
                    <p class="text-2xl font-mono font-bold text-gray-900 tracking-wider mb-2">
                        {#if donation.payment_method === 'BSI'} 7123456789
                        {:else if donation.payment_method === 'BRI'} 012301001234567
                        {:else if donation.payment_method === 'BCA'} 1234567890
                        {:else if donation.payment_method === 'MANDIRI'} 1370012345678
                        {:else} 1234567890 {/if}
                    </p>
                    <p class="text-sm font-semibold text-gray-700 uppercase">A.N LAZISMU</p>
                </div>

                <div class="border-t pt-6">
                    <h3 class="font-medium text-gray-900 mb-4">Setelah transfer, harap konfirmasi pembayaran Anda</h3>
                    <Link href={`/donasi/berhasil/${donation.id}`} class="w-full block text-center py-3 px-4 border border-transparent rounded-md shadow-sm text-lg font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        Saya Sudah Transfer
                    </Link>
                </div>
            </div>
        </div>
    </div>
</Layout>
