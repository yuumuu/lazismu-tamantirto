<?php

use App\Enums\DonationStatus;
use App\Imports\DonationImport;
use App\Models\Donation;
use App\Services\Donation\DonationVerificationService;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

new class extends Component {
    use WithFileUploads, WithPagination;

    public $search = '';
    public $status = '';
    public $type = '';
    public $selectedDonationId = null;
    public $confirmationNotes = '';

    public $importFile;
    public int $importCount = 0;
    public int $importErrorCount = 0;
    public array $importErrors = [];
    public bool $importing = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'type' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function openVerifyModal(int $donationId): void
    {
        $this->selectedDonationId = $donationId;
        $this->confirmationNotes = '';
        $this->js('$flux.modal("verify-donation-modal").show()');
    }

    public function openRejectModal(int $donationId): void
    {
        $this->selectedDonationId = $donationId;
        $this->confirmationNotes = '';
        $this->js('$flux.modal("reject-donation-modal").show()');
    }

    public function verifyDonation(): void
    {
        if (!$this->selectedDonationId) {
            return;
        }

        $donation = Donation::findOrFail($this->selectedDonationId);
        $verificationService = app(DonationVerificationService::class);
        
        $result = $verificationService->verify($donation, auth()->user(), $this->confirmationNotes ?: null);
        
        if ($result->isSuccess()) {
            $this->dispatch('notification', [
                'type' => 'success',
                'message' => $result->getMessage()
            ]);
            $this->js('$flux.modal("verify-donation-modal").close()');
        } else {
            $this->dispatch('notification', [
                'type' => 'error', 
                'message' => $result->getMessage()
            ]);
        }

        $this->reset(['selectedDonationId', 'confirmationNotes']);
    }

    public function rejectDonation(): void
    {
        if (!$this->selectedDonationId) {
            return;
        }

        $this->validate([
            'confirmationNotes' => 'required|string|min:3',
        ], [
            'confirmationNotes.required' => 'Alasan penolakan harus diisi.',
            'confirmationNotes.min' => 'Alasan penolakan minimal 3 karakter.',
        ]);

        $donation = Donation::findOrFail($this->selectedDonationId);
        $verificationService = app(DonationVerificationService::class);
        
        $result = $verificationService->reject($donation, auth()->user(), $this->confirmationNotes);
        
        if ($result->isSuccess()) {
            $this->dispatch('notification', [
                'type' => 'success',
                'message' => $result->getMessage()
            ]);
            $this->js('$flux.modal("reject-donation-modal").close()');
        } else {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => $result->getMessage()
            ]);
        }

        $this->reset(['selectedDonationId', 'confirmationNotes']);
    }

    public function openImportModal(): void
    {
        $this->reset(['importFile', 'importCount', 'importErrorCount', 'importErrors', 'importing']);
        $this->js('$flux.modal("import-donation-modal").show()');
    }

    public function import(): void
    {
        $this->validate([
            'importFile' => 'required|file|mimes:csv,txt,xlsx,xls|max:5120',
        ]);

        $this->importing = true;
        $this->importCount = 0;
        $this->importErrorCount = 0;
        $this->importErrors = [];

        try {
            $import = new DonationImport;
            Excel::import($import, $this->importFile->getRealPath());

            $this->importCount = $import->getProcessedRows();
            $failures = $import->failures();
            $this->importErrorCount = count($failures);
            $this->importErrors = collect($failures)->map(fn($f) => 'Baris ' . $f->row() . ': ' . implode(', ', $f->errors()))->toArray();

            \Flux::toast('Import selesai! ' . $this->importCount . ' data berhasil diimpor.', variant: 'success');
        } catch (\Exception $e) {
            $this->importErrors[] = 'Kesalahan: ' . $e->getMessage();
            $this->importErrorCount++;
            \Flux::toast('Gagal mengimpor file.', variant: 'danger');
        }

        $this->importing = false;
    }

    public function with(DonationVerificationService $verificationService): array
    {
        return [
            'donations' => Donation::query()
                ->with(['campaign', 'muzakki'])
                ->when($this->search, function ($q) {
                    $q->where('donor_name', 'like', '%' . $this->search . '%')
                      ->orWhere('transaction_id', 'like', '%' . $this->search . '%')
                      ->orWhere('donor_email', 'like', '%' . $this->search . '%');
                })
                ->when($this->status, fn($q) => $q->where('status', $this->status))
                ->when($this->type, fn($q) => $q->where('donation_type', $this->type))
                ->latest()
                ->paginate(15),
            'statuses' => DonationStatus::cases(),
            'stats' => [
                'pending' => Donation::pending()->count(),
                'suspicious' => Donation::suspicious()->count(),
            ]
        ];
    }
} ?>

<div class="p-3 md:p-6 lg:p-10 space-y-8">
    <!-- Header -->
    <x-admin.page-header 
        title="Donasi Masuk"
        description="Monitor arus kas donasi masuk secara real-time."
    >
        <x-slot:action>
            <div class="flex items-center gap-2">
                <flux:button icon="document-arrow-up" variant="subtle" wire:click="openImportModal">
                    {{ __('Import CSV') }}
                </flux:button>
                <flux:button variant="primary" icon="plus" :href="route('admin.donations.create')" wire:navigate>
                    {{ __('Input Manual') }}
                </flux:button>
            </div>
        </x-slot:action>
    </x-admin.page-header>

    <!-- Stats & Filters -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Stats Widgets -->
        <div class="md:col-span-4 lg:col-span-4 grid grid-cols-2 md:grid-cols-4 gap-4 mb-4">
             <div class="premium-card p-4 flex flex-col justify-between hover:border-primary/30 transition-colors">
                <span class="text-xs font-medium text-zinc-500 uppercase tracking-wider">{{ __('Total Donasi') }}</span>
                <span class="text-xl font-black text-zinc-900 dark:text-white mt-1">{{ format_rupiah(App\Models\Donation::verified()->sum('amount')) }}</span>
             </div>
             <div class="premium-card p-4 flex flex-col justify-between hover:border-amber-200 transition-colors">
                <span class="text-xs font-medium text-zinc-500 uppercase tracking-wider">{{ __('Menunggu Verifikasi') }}</span>
                <span class="text-xl font-black text-amber-600 mt-1">{{ App\Models\Donation::pending()->count() }}</span>
             </div>
        </div>

        <div class="md:col-span-2">
            <flux:input icon="magnifying-glass" wire:model.live.debounce.300ms="search" placeholder="Cari donatur, ID transaksi..." />
        </div>
        <div>
            <flux:select wire:model.live="status" placeholder="Semua Status" data-tour="donation-status-filter">
                <option value="">{{ __('Semua Status') }}</option>
                @foreach($statuses as $s)
                    <option value="{{ $s->value }}">{{ $s->label() }}</option>
                @endforeach
            </flux:select>
        </div>
         <div>
            <flux:button icon="funnel" variant="subtle" class="w-full justify-start">{{ __('Filter Lanjutan') }}</flux:button>
        </div>
    </div>

    <!-- Table -->
    <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-zinc-50 dark:bg-zinc-900/50 text-zinc-500 font-medium border-b border-zinc-200 dark:border-zinc-800">
                    <tr>
                        <th class="px-6 py-3">{{ __('Donatur') }}</th>
                        <th class="px-6 py-3">{{ __('Program') }}</th>
                        <th class="px-6 py-3">{{ __('Nominal') }}</th>
                        <th class="px-6 py-3">{{ __('Metode') }}</th>
                        <th class="px-6 py-3 text-center">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                    @forelse($donations as $donation)
                        <tr class="group hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors" wire:key="{{ $donation->id }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="size-8 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center font-bold text-zinc-500 text-xs">
                                        {{ substr($donation->donor_name, 0, 1) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-zinc-900 dark:text-white">{{ $donation->donor_name }}</span>
                                        <span class="text-xs text-zinc-500 font-mono">{{ $donation->created_at->translatedFormat('d M Y, H:i') }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-zinc-600 dark:text-zinc-400 text-sm truncate block max-w-[200px]">{{ $donation->campaign?->title ?? 'Dana Umum' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-black text-zinc-900 dark:text-white font-mono tracking-tight">{{ $donation->formatted_amount }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <flux:icon name="credit-card" class="size-3 text-zinc-400" />
                                    <span class="text-zinc-600 dark:text-zinc-400 text-sm">{{ $donation->payment_method->label() }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <flux:badge :color="$donation->status->color()" size="sm" inset="top bottom">{{ $donation->status->label() }}</flux:badge>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($donation->canBeVerified())
                                        <flux:button 
                                            icon="check" 
                                            size="xs" 
                                            variant="ghost" 
                                            class="text-lime-600 hover:text-lime-700 hover:bg-lime-50"
                                            wire:click="openVerifyModal({{ $donation->id }})"
                                            title="Verifikasi Donasi"
                                        />
                                    @endif
                                    
                                    @if($donation->canBeRejected())
                                        <flux:button 
                                            icon="x-mark" 
                                            size="xs" 
                                            variant="ghost" 
                                            class="text-red-600 hover:text-red-700 hover:bg-red-50"
                                            wire:click="openRejectModal({{ $donation->id }})"
                                            title="Tolak Donasi"
                                        />
                                    @endif
                                    
                                    <flux:button 
                                        icon="eye" 
                                        size="xs" 
                                        variant="ghost" 
                                        :href="route('admin.donations.show', $donation)" 
                                        wire:navigate 
                                        title="Lihat Detail"
                                    />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <flux:icon name="magnifying-glass" class="size-8 text-zinc-300" />
                                    <p class="text-zinc-500 text-sm font-medium">{{ __('Tidak ada donasi ditemukan.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 p-4">
            {{ $donations->links() }}
        </div>
    </div>

    <!-- Verification Modal -->
    <flux:modal name="verify-donation-modal" class="max-w-lg">
        <form wire:submit="verifyDonation" class="space-y-6">
            <div>
                <flux:heading>Verifikasi Donasi</flux:heading>
                <flux:subheading>
                    Apakah Anda yakin ingin memverifikasi donasi ini? Donasi yang sudah diverifikasi akan dihitung dalam total kampanye.
                </flux:subheading>
            </div>

            <flux:field>
                <flux:label>Catatan Verifikasi (Opsional)</flux:label>
                <flux:textarea 
                    wire:model="confirmationNotes" 
                    placeholder="Tambahkan catatan verifikasi jika diperlukan..."
                    rows="3"
                />
            </flux:field>

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary" class="bg-lime-600 hover:bg-lime-700">
                    Verifikasi Donasi
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Rejection Modal -->
    <flux:modal name="reject-donation-modal" class="max-w-lg">
        <form wire:submit="rejectDonation" class="space-y-6">
            <div>
                <flux:heading>Tolak Donasi</flux:heading>
                <flux:subheading>
                    Apakah Anda yakin ingin menolak donasi ini? Donasi yang ditolak tidak akan dihitung dalam total kampanye.
                </flux:subheading>
            </div>

            <flux:field>
                <flux:label>Alasan Penolakan <span class="text-red-500">*</span></flux:label>
                <flux:textarea 
                    wire:model="confirmationNotes" 
                    placeholder="Jelaskan alasan penolakan donasi ini..."
                    rows="3"
                    required
                />
                <flux:error name="confirmationNotes" />
            </flux:field>

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="danger">
                    Tolak Donasi
                </flux:button>
            </div>
        </form>
    </flux:modal>

    <!-- Import Modal -->
    <flux:modal name="import-donation-modal" class="max-w-lg">
        <form wire:submit="import" class="space-y-6">
            <div>
                <flux:heading>Import Donasi (CSV/Excel)</flux:heading>
                <flux:subheading>
                    Upload file CSV atau Excel dengan kolom: <strong>nama_donatur</strong>/<strong>donor_name</strong>, <strong>email</strong>, <strong>telepon</strong>, <strong>jumlah</strong>/<strong>amount</strong>, <strong>tipe_donasi</strong>, <strong>metode</strong>, <strong>status</strong>, <strong>bank</strong>, <strong>no_rekening</strong>, <strong>pesan</strong>, <strong>anonim</strong>.
                </flux:subheading>
            </div>

            <flux:field>
                <flux:label>Pilih File</flux:label>
                <flux:input type="file" wire:model="importFile" accept=".csv,.xlsx,.xls" />
                <flux:error name="importFile" />
                <p class="text-xs text-zinc-400 mt-1">Maksimal 5MB. Format CSV, XLSX, atau XLS.</p>
            </flux:field>

            @if($importErrors)
                <div class="space-y-2">
                    @if($importCount > 0)
                        <div class="p-3 bg-lime-50 dark:bg-lime-900/20 text-lime-700 dark:text-lime-400 rounded-lg text-sm font-medium">
                            {{ $importCount }} data berhasil diimpor.
                        </div>
                    @endif
                    @if($importErrorCount > 0)
                        <div class="p-3 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-lg text-sm">
                            <p class="font-medium mb-1">{{ $importErrorCount }} kesalahan:</p>
                            <ul class="list-disc pl-4 space-y-0.5">
                                @foreach($importErrors as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endif

            <div class="flex justify-end gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost">Tutup</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="primary" :disabled="$importing">
                    <span wire:loading.remove wire:target="import">Import Data</span>
                    <span wire:loading wire:target="import">Mengimpor...</span>
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
