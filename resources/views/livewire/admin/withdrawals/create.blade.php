<?php

use App\Models\Withdrawal;
use App\Models\Campaign;
use App\Models\Mustahik;
use App\Models\Distributor;
use App\Enums\WithdrawalStatus;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public $campaign_id;
    public $mustahik_id;
    public $distributor_id;
    public $amount;
    public $date;
    public $description;
    public $proof_image;

    public function mount(): void
    {
        $this->date = now()->format('Y-m-d');
    }

    public function save(): void
    {
        $this->validate([
            'amount' => 'required|numeric|min:1000',
            'date' => 'required|date',
            'description' => 'required|min:10',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'mustahik_id' => 'nullable|exists:mustahiks,id',
            'distributor_id' => 'nullable|exists:distributors,id',
        ]);

        $data = [
            'campaign_id' => $this->campaign_id,
            'mustahik_id' => $this->mustahik_id,
            'distributor_id' => $this->distributor_id,
            'amount' => $this->amount,
            'date' => $this->date,
            'description' => $this->description,
            'status' => WithdrawalStatus::Draft->value,
            'created_by' => auth()->id(),
        ];

        if ($this->proof_image) {
            $data['proof_image'] = $this->proof_image->store('withdrawals', 'public');
        }

        Withdrawal::create($data);

        $this->dispatch('notify', message: 'Catatan penyaluran berhasil disimpan sebagai draft.', type: 'success');
        $this->redirect(route('admin.withdrawals.index'), navigate: true);
    }

    public function with(): array
    {
        // Calculate available funds
        $totalDonations = \App\Models\Donation::where('status', 'verified')->sum('amount');
        $totalWithdrawals = \App\Models\Withdrawal::whereIn('status', ['verified', 'sent'])->sum('amount');
        $availableFunds = $totalDonations - $totalWithdrawals;

        return [
            'campaigns' => Campaign::active()->get(),
            'mustahiks' => Mustahik::active()->get(),
            'distributors' => Distributor::active()->get(),
            'availableFunds' => $availableFunds,
            'totalDonations' => $totalDonations,
            'totalWithdrawals' => $totalWithdrawals,
        ];
    }
} ?>

<div class="p-3 md:p-6 lg:p-10 space-y-6 md:space-y-8">
    <x-admin.page-header 
        title="Catat Penyaluran Dana"
        description="Input data penyaluran dana ke mustahik atau program."
        back-route="admin.withdrawals.index"
    />

    <form wire:submit="save" class="space-y-6 md:space-y-8">
        <!-- Mobile-First: Actions at top on small screens -->
        <div class="lg:hidden bg-white dark:bg-zinc-900 p-3 md:p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 shadow-sm">
            <flux:button 
                type="submit" 
                variant="primary" 
                class="w-full" 
                icon="check" 
                wire:loading.attr="disabled"
                :disabled="$availableFunds <= 0"
                size="sm"
            >
                <span wire:loading.remove wire:target="save">{{ __('Simpan Draft') }}</span>
                <span wire:loading wire:target="save">{{ __('Menyimpan...') }}</span>
            </flux:button>
            
            @if($availableFunds <= 0)
                <p class="text-xs text-red-500 mt-2 text-center">{{ __('Dana tidak mencukupi') }}</p>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">
            <div class="lg:col-span-2 space-y-4 md:space-y-6">
            <div class="bg-white dark:bg-zinc-900 p-4 md:p-6 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-4 md:space-y-6">
                <!-- Financials -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>{{ __('Jumlah Penyaluran (Rp)') }}</flux:label>
                        <flux:input wire:model.live.debounce.500ms="amount" type="number" prefix="Rp" min="1000" />
                        <flux:error name="amount" />
                        @if($amount && $amount >= 1000)
                            <flux:description class="text-green-600">
                                <flux:icon name="check-circle" class="size-4" />
                                {{ format_rupiah($amount) }}
                            </flux:description>
                        @endif
                    </flux:field>
                    
                    <flux:field>
                        <flux:label>{{ __('Tanggal Penyaluran') }}</flux:label>
                        <flux:input wire:model.live="date" type="date" max="{{ now()->format('Y-m-d') }}" />
                        <flux:error name="date" />
                    </flux:field>
                </div>

                <!-- Description -->
                <flux:field>
                    <flux:label>{{ __('Keterangan / Tujuan') }}</flux:label>
                    <flux:textarea 
                        wire:model.live.debounce.500ms="description" 
                        placeholder="Contoh: Penyaluran beasiswa bulan Januari untuk 5 siswa..." 
                        rows="4" 
                    />
                    <flux:error name="description" />
                    @if($description)
                        <flux:description class="text-xs">
                            {{ strlen($description) }}/500 karakter
                            @if(strlen($description) >= 10)
                                <flux:icon name="check-circle" class="size-3 text-green-500 inline ml-1" />
                            @endif
                        </flux:description>
                    @endif
                </flux:field>
            </div>

            <!-- Proof of Distribution -->
            <div class="bg-white dark:bg-zinc-900 p-6 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <h3 class="font-bold text-zinc-900 dark:text-white mb-4">{{ __('Bukti Penyaluran (Opsional)') }}</h3>
                
                <div class="space-y-4">
                    @if ($proof_image)
                        <div class="relative w-full aspect-video rounded-2xl overflow-hidden border border-zinc-200 dark:border-zinc-800">
                            <img src="{{ $proof_image->temporaryUrl() }}" class="size-full object-cover">
                            <div class="absolute inset-0 bg-black/50 opacity-0 hover:opacity-100 transition-opacity flex items-center justify-center">
                                <button type="button" wire:click="$set('proof_image', null)" class="p-2 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors">
                                    <flux:icon name="trash" class="size-4" />
                                </button>
                            </div>
                        </div>
                        <p class="text-xs text-green-600 flex items-center gap-1">
                            <flux:icon name="check-circle" class="size-4" />
                            {{ __('Gambar berhasil diunggah') }}
                        </p>
                    @else
                        <div 
                            x-data="{ 
                                isDragging: false,
                                handleDrop(e) {
                                    this.isDragging = false;
                                    const files = e.dataTransfer.files;
                                    if (files.length > 0) {
                                        @this.upload('proof_image', files[0]);
                                    }
                                }
                            }"
                            @dragover.prevent="isDragging = true"
                            @dragleave.prevent="isDragging = false"
                            @drop.prevent="handleDrop"
                            :class="isDragging ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20' : 'border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-950'"
                            class="flex flex-col items-center justify-center w-full aspect-video rounded-2xl border-2 border-dashed hover:border-amber-500 transition-colors cursor-pointer"
                        >
                            <label class="flex flex-col items-center justify-center w-full h-full cursor-pointer">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <flux:icon name="cloud-arrow-up" class="size-10 text-zinc-400 mb-2" />
                                    <p class="mb-2 text-sm text-zinc-500">
                                        <span class="font-semibold">{{ __('Klik untuk upload') }}</span> {{ __('atau drag & drop') }}
                                    </p>
                                    <p class="text-xs text-zinc-400">{{ __('PNG, JPG, PDF (Max. 5MB)') }}</p>
                                </div>
                                <input type="file" wire:model="proof_image" class="hidden" accept="image/*,application/pdf">
                            </label>
                        </div>
                        
                        <div wire:loading wire:target="proof_image" class="text-center">
                            <div class="inline-flex items-center gap-2 text-sm text-amber-600">
                                <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-amber-600"></div>
                                {{ __('Mengunggah...') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Selection -->
        <div class="space-y-4 md:space-y-6">
            <!-- Available Funds Info -->
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 p-4 md:p-6 rounded-[24px_0_24px_0] border border-green-200 dark:border-green-800 shadow-sm">
                <div class="space-y-4">
                    <div class="flex items-center gap-2">
                        <flux:icon name="banknotes" class="size-5 text-green-600" />
                        <h3 class="font-bold text-green-900 dark:text-green-100">{{ __('Dana Tersedia') }}</h3>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-green-700 dark:text-green-300">{{ __('Total Donasi') }}</span>
                            <span class="font-semibold text-green-900 dark:text-green-100">{{ format_rupiah($totalDonations) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-green-700 dark:text-green-300">{{ __('Total Tersalurkan') }}</span>
                            <span class="font-semibold text-red-600 dark:text-red-400">- {{ format_rupiah($totalWithdrawals) }}</span>
                        </div>
                        <div class="pt-2 border-t border-green-200 dark:border-green-700">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-green-800 dark:text-green-200">{{ __('Saldo Tersedia') }}</span>
                                <span class="text-lg font-bold text-green-600 dark:text-green-400">{{ format_rupiah($availableFunds) }}</span>
                            </div>
                        </div>
                    </div>
                    
                    @if($availableFunds <= 0)
                        <div class="mt-3 p-3 bg-red-100 dark:bg-red-900/30 rounded-xl border border-red-200 dark:border-red-800">
                            <p class="text-xs text-red-700 dark:text-red-300 flex items-center gap-1">
                                <flux:icon name="exclamation-triangle" class="size-4" />
                                {{ __('Dana tidak mencukupi untuk penyaluran baru') }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 p-4 md:p-6 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-4 md:space-y-6">
                <div class="space-y-4">
                    <flux:select wire:model="mustahik_id" label="Penerima (Mustahik)" placeholder="Pilih Mustahik">
                        <option value="">-- Umum / Tanpa Nama --</option>
                        @foreach($mustahiks as $m)
                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model="campaign_id" label="Program / Campaign" placeholder="Pilih Program">
                        <option value="">-- Dana Umum (Tanpa Campaign) --</option>
                        @foreach($campaigns as $c)
                            <option value="{{ $c->id }}">{{ $c->title }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model="distributor_id" label="Penyalur (Distributor)" placeholder="Pilih Penyalur">
                        <option value="">-- Petugas Internal --</option>
                        @foreach($distributors as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div class="pt-4">
                    <flux:button 
                        type="submit" 
                        variant="primary" 
                        class="w-full" 
                        icon="check"
                        wire:loading.attr="disabled"
                        :disabled="$availableFunds <= 0"
                    >
                        <span wire:loading.remove wire:target="save">{{ __('Simpan Draft Penyaluran') }}</span>
                        <span wire:loading wire:target="save">{{ __('Menyimpan...') }}</span>
                    </flux:button>
                    
                    @if($availableFunds <= 0)
                        <p class="text-xs text-red-500 mt-2 text-center">{{ __('Tidak dapat membuat penyaluran karena dana tidak mencukupi') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </form>
</div>
