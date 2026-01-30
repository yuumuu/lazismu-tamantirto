<x-layouts.app :title="__('Dashboard')">
    <div class="p-6 lg:p-10 space-y-8">
        <!-- Header Section -->
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ __('Dashboard Overview') }}</h1>
                <p class="text-zinc-500 dark:text-zinc-400 text-sm mt-1">{{ __('Selamat datang kembali, :name!', ['name' => auth()->user()->name]) }}</p>
            </div>
            <div class="flex items-center gap-3">
                <flux:button icon="arrow-down-tray" variant="outline" size="sm" class="rounded-[16px_0_16px_0]">{{ __('Export Laporan') }}</flux:button>
                <flux:button variant="primary" icon="plus" size="sm" class="bg-amber-500 hover:bg-amber-600 border-none rounded-[16px_0_16px_0]">{{ __('Tambah Transaksi') }}</flux:button>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white dark:bg-zinc-900 p-6 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-amber-50 dark:bg-amber-900/20 rounded-2xl">
                        <flux:icon name="banknotes" class="size-6 text-amber-500" />
                    </div>
                    <span class="text-[10px] font-bold px-2 py-1 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 rounded-full">+12.5%</span>
                </div>
                <h3 class="text-zinc-500 dark:text-zinc-400 text-xs font-medium uppercase tracking-wider">{{ __('Total Ziswaf') }}</h3>
                <p class="text-2xl font-bold text-zinc-900 dark:text-white mt-1">Rp 128.450k</p>
            </div>

            <div class="bg-white dark:bg-zinc-900 p-6 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-blue-50 dark:bg-blue-900/20 rounded-2xl">
                        <flux:icon name="user-group" class="size-6 text-blue-500" />
                    </div>
                    <span class="text-[10px] font-bold px-2 py-1 bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 rounded-full">842 New</span>
                </div>
                <h3 class="text-zinc-500 dark:text-zinc-400 text-xs font-medium uppercase tracking-wider">{{ __('Penerima Manfaat') }}</h3>
                <p class="text-2xl font-bold text-zinc-900 dark:text-white mt-1">12,482</p>
            </div>

            <div class="bg-white dark:bg-zinc-900 p-6 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-purple-50 dark:bg-purple-900/20 rounded-2xl">
                        <flux:icon name="folder" class="size-6 text-purple-500" />
                    </div>
                    <span class="text-[10px] font-bold px-2 py-1 bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400 rounded-full">12 Active</span>
                </div>
                <h3 class="text-zinc-500 dark:text-zinc-400 text-xs font-medium uppercase tracking-wider">{{ __('Program Berjalan') }}</h3>
                <p class="text-2xl font-bold text-zinc-900 dark:text-white mt-1">48</p>
            </div>

            <div class="bg-white dark:bg-zinc-900 p-6 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-3 bg-orange-50 dark:bg-orange-900/20 rounded-2xl">
                        <flux:icon name="arrows-right-left" class="size-6 text-orange-500" />
                    </div>
                    <span class="text-[10px] font-bold px-2 py-1 bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400 rounded-full">Needs Audit</span>
                </div>
                <h3 class="text-zinc-500 dark:text-zinc-400 text-xs font-medium uppercase tracking-wider">{{ __('Transaksi Pending') }}</h3>
                <p class="text-2xl font-bold text-zinc-900 dark:text-white mt-1">24</p>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Recent Transactions -->
            <div class="lg:col-span-2 bg-white dark:bg-zinc-900 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-zinc-100 dark:border-zinc-800 flex items-center justify-between">
                    <h2 class="font-bold text-zinc-900 dark:text-white">{{ __('Transaksi Terbaru') }}</h2>
                    <flux:button variant="ghost" size="sm" class="text-xs text-amber-500">{{ __('Lihat Semua') }}</flux:button>
                </div>
                <div class="p-0">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-zinc-50 dark:bg-zinc-800/50 text-zinc-500 dark:text-zinc-400">
                            <tr>
                                <th class="px-6 py-3 font-medium">{{ __('Muzakki') }}</th>
                                <th class="px-6 py-3 font-medium">{{ __('Tipe') }}</th>
                                <th class="px-6 py-3 font-medium">{{ __('Jumlah') }}</th>
                                <th class="px-6 py-3 font-medium">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @foreach([
                                ['name' => 'Ahmad Fauzi', 'type' => 'Zakat Fitrah', 'amount' => '45.000', 'status' => 'Selesai'],
                                ['name' => 'Siti Aminah', 'type' => 'Infaq Terikat', 'amount' => '250.000', 'status' => 'Pending'],
                                ['name' => 'Budi Santoso', 'type' => 'Zakat Maal', 'amount' => '1.200.000', 'status' => 'Selesai'],
                                ['name' => 'Rina Wijaya', 'type' => 'Sedekah Subuh', 'amount' => '10.000', 'status' => 'Selesai'],
                            ] as $tx)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/30 transition-colors">
                                <td class="px-6 py-4 font-medium text-zinc-900 dark:text-white">{{ $tx['name'] }}</td>
                                <td class="px-6 py-4 text-zinc-500 dark:text-zinc-400">{{ $tx['type'] }}</td>
                                <td class="px-6 py-4 font-semibold text-zinc-900 dark:text-white">Rp {{ $tx['amount'] }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $tx['status'] === 'Selesai' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' }}">
                                        {{ $tx['status'] }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Program Status -->
            <div class="bg-white dark:bg-zinc-900 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm p-6 space-y-6">
                <h2 class="font-bold text-zinc-900 dark:text-white">{{ __('Program Teratas') }}</h2>
                
                <div class="space-y-6">
                    @foreach([
                        ['label' => 'Beasiswa Mentari', 'progress' => 85, 'color' => 'bg-amber-500'],
                        ['label' => 'Klinik Apung', 'progress' => 42, 'color' => 'bg-blue-500'],
                        ['label' => 'Sumur Bor NTT', 'progress' => 91, 'color' => 'bg-green-500'],
                    ] as $prog)
                    <div class="space-y-2">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-zinc-700 dark:text-zinc-300 font-medium">{{ $prog['label'] }}</span>
                            <span class="text-zinc-500">{{ $prog['progress'] }}%</span>
                        </div>
                        <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-1.5 overflow-hidden">
                            <div class="{{ $prog['color'] }} h-full transition-all duration-500" style="width: {{ $prog['progress'] }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="pt-4 mt-6 border-t border-zinc-100 dark:border-zinc-800">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-zinc-500 dark:text-zinc-400">{{ __('Target Tahunan') }}</span>
                        <span class="font-bold text-zinc-900 dark:text-white">72% Achieved</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>

