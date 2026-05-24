<script>
    import { Link } from '@inertiajs/svelte';
    import Layout from '../../Layouts/GuestLayout.svelte';

    export let donation;
</script>

<svelte:head><title>Donasi Berhasil | Lazismu</title></svelte:head>

<Layout>
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12 text-center">
        <div class="bg-white rounded-xl shadow-md p-8 md:p-12">
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-green-100 mb-6">
                <svg class="h-10 w-10 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Terima Kasih, Orang Baik!</h1>
            
            <p class="text-lg text-gray-600 mb-8">
                Jazakumullah Khairan Katsiran atas donasi yang telah Anda berikan. 
                Semoga Allah SWT membalas dengan kebaikan yang berlipat ganda.
            </p>
            
            <div class="bg-gray-50 rounded-lg p-6 mb-8 text-left max-w-sm mx-auto">
                <div class="mb-4 pb-4 border-b">
                    <p class="text-sm text-gray-500 mb-1">ID Donasi</p>
                    <p class="font-mono font-bold text-gray-900">{donation.invoice_number || `INV-${donation.id}`}</p>
                </div>
                <div class="mb-4 pb-4 border-b">
                    <p class="text-sm text-gray-500 mb-1">Nominal</p>
                    <p class="font-bold text-gray-900">Rp {new Intl.NumberFormat('id-ID').format(donation.amount)}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Program</p>
                    <p class="font-medium text-gray-900">{donation.campaign ? donation.campaign.title : 'Donasi Umum'}</p>
                </div>
            </div>

            <div class="space-y-4">
                <Link href="/" class="block w-full py-3 px-4 border border-transparent rounded-md shadow-sm font-medium text-white bg-primary-600 hover:bg-primary-700">
                    Kembali ke Beranda
                </Link>
                <Link href="/program" class="block w-full py-3 px-4 border border-gray-300 rounded-md font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Lihat Program Lainnya
                </Link>
            </div>
        </div>
    </div>
</Layout>
