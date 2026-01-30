<?php

use App\Models\Withdrawal;
use App\Enums\WithdrawalStatus;
use App\Services\Donation\WithdrawalVerificationService;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public Withdrawal $withdrawal;
    public $proof;
    public $notes = '';
    public $showQuickComplete = false;

    public function mount(Withdrawal $withdrawal): void
    {
        $this->withdrawal = $withdrawal;
    }

    public function quickComplete(WithdrawalVerificationService $service): void
    {
        if (!$this->proof) {
            $this->dispatch('notify', message: 'Bukti penyaluran wajib diunggah untuk mode cepat.', type: 'error');
            return;
        }

        $proofPath = $this->proof->store('withdrawals/proofs', 'public');
        
        // Verify first, then complete
        $verifyResult = $service->verify($this->withdrawal, auth()->user());
        if (!$verifyResult->isSuccess()) {
            $this->dispatch('notify', message: $verifyResult->getMessage(), type: 'error');
            return;
        }

        // Refresh the model to get updated status
        $this->withdrawal->refresh();
        
        $completeResult = $service->complete($this->withdrawal, $proofPath);
        
        if ($completeResult->isSuccess()) {
            $this->dispatch('notify', message: 'Dana berhasil diverifikasi dan ditandai tersalurkan.', type: 'success');
            $this->redirect(route('admin.withdrawals.index'), navigate: true);
        } else {
            $this->dispatch('notify', message: $completeResult->getMessage(), type: 'error');
        }
    }

    public function verify(WithdrawalVerificationService $service): void
    {
        $result = $service->verify($this->withdrawal, auth()->user());
        
        if ($result->isSuccess()) {
            $this->dispatch('notify', message: $result->getMessage(), type: 'success');
        } else {
            $this->dispatch('notify', message: $result->getMessage(), type: 'error');
        }
    }

    public function complete(WithdrawalVerificationService $service): void
    {
        $proofPath = null;
        if ($this->proof) {
            $proofPath = $this->proof->store('withdrawals/proofs', 'public');
        }

        $result = $service->complete($this->withdrawal, $proofPath);

        if ($result->isSuccess()) {
            $this->dispatch('notify', message: $result->getMessage(), type: 'success');
            $this->redirect(route('admin.withdrawals.index'), navigate: true);
        } else {
            $this->dispatch('notify', message: $result->getMessage(), type: 'error');
        }
    }
} ?>

<div class="p-3 md:p-6 lg:p-10 space-y-6 md:space-y-8">
    <x-admin.page-header 
        title="Detail Penyaluran"
        description="Verifikasi dan unggah bukti penyaluran dana."
        back-route="admin.withdrawals.index"
    />

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <!-- Progress Indicator -->
            <div class="bg-white dark:bg-zinc-900 p-6 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-zinc-900 dark:text-white">{{ __('Status Penyaluran') }}</h3>
                    <flux:badge :color="$withdrawal->status->color()">{{ $withdrawal->status->label() }}</flux:badge>
                </div>
                
                <!-- Progress Steps -->
                <div class="flex items-center space-x-4">
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $withdrawal->status->value === 'draft' ? 'bg-amber-500 text-white' : 'bg-green-500 text-white' }}">
                            @if($withdrawal->status->value === 'draft')
                                <flux:icon name="pencil" class="size-4" />
                            @else
                                <flux:icon name="check" class="size-4" />
                            @endif
                        </div>
                        <span class="ml-2 text-sm font-medium text-zinc-900 dark:text-white">Draft</span>
                    </div>
                    
                    <div class="flex-1 h-0.5 {{ in_array($withdrawal->status->value, ['verified', 'sent']) ? 'bg-green-500' : 'bg-zinc-200 dark:bg-zinc-700' }}"></div>
                    
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $withdrawal->status->value === 'verified' ? 'bg-blue-500 text-white' : ($withdrawal->status->value === 'sent' ? 'bg-green-500 text-white' : 'bg-zinc-200 dark:bg-zinc-700 text-zinc-500') }}">
                            @if($withdrawal->status->value === 'verified')
                                <flux:icon name="shield-check" class="size-4" />
                            @elseif($withdrawal->status->value === 'sent')
                                <flux:icon name="check" class="size-4" />
                            @else
                                <flux:icon name="shield-check" class="size-4" />
                            @endif
                        </div>
                        <span class="ml-2 text-sm font-medium {{ in_array($withdrawal->status->value, ['verified', 'sent']) ? 'text-zinc-900 dark:text-white' : 'text-zinc-500' }}">Terverifikasi</span>
                    </div>
                    
                    <div class="flex-1 h-0.5 {{ $withdrawal->status->value === 'sent' ? 'bg-green-500' : 'bg-zinc-200 dark:bg-zinc-700' }}"></div>
                    
                    <div class="flex items-center">
                        <div class="flex items-center justify-center w-8 h-8 rounded-full {{ $withdrawal->status->value === 'sent' ? 'bg-green-500 text-white' : 'bg-zinc-200 dark:bg-zinc-700 text-zinc-500' }}">
                            <flux:icon name="check-circle" class="size-4" />
                        </div>
                        <span class="ml-2 text-sm font-medium {{ $withdrawal->status->value === 'sent' ? 'text-zinc-900 dark:text-white' : 'text-zinc-500' }}">Tersalurkan</span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-900 p-8 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-4">
                        <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest">{{ __('Informasi Penyaluran') }}</h3>
                        <div class="space-y-1">
                            <p class="text-lg font-bold text-red-500">- {{ $withdrawal->formatted_amount }}</p>
                            <p class="text-sm text-zinc-500">{{ $withdrawal->date->format('d F Y') }}</p>
                            <flux:badge :color="$withdrawal->status->color()" class="mt-2">{{ $withdrawal->status->label() }}</flux:badge>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest">{{ __('Target & Pelaksana') }}</h3>
                        <div class="space-y-1">
                            <p class="text-sm font-bold text-zinc-900 dark:text-white">Penerima: {{ $withdrawal->mustahik?->name ?? 'Fisabilillah / Umum' }}</p>
                            <p class="text-sm text-zinc-500">Penyalur: {{ $withdrawal->distributor?->name ?? 'Internal Team' }}</p>
                        </div>
                    </div>
                </div>

                <div class="pt-8 border-t border-zinc-100 dark:border-zinc-800 space-y-4">
                    <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest">{{ __('Deskripsi / Tujuan') }}</h3>
                    <p class="text-zinc-600 dark:text-zinc-400 whitespace-pre-line">{{ $withdrawal->description }}</p>
                </div>
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-zinc-900 p-6 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                @if($withdrawal->status === WithdrawalStatus::Draft)
                    <h3 class="font-bold text-zinc-900 dark:text-white">{{ __('Verifikasi Dana') }}</h3>
                    <p class="text-xs text-zinc-500">{{ __('Pastikan saldo dana program mencukupi sebelum memverifikasi pengeluaran.') }}</p>
                    
                    <div class="space-y-3">
                        <flux:button wire:click="verify" variant="primary" class="w-full rounded-[16px_0_16px_0]" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="verify">{{ __('Setujui Pengeluaran') }}</span>
                            <span wire:loading wire:target="verify">{{ __('Memverifikasi...') }}</span>
                        </flux:button>
                        
                        <!-- Quick Complete Option -->
                        <flux:button wire:click="$toggle('showQuickComplete')" variant="ghost" size="sm" class="w-full text-xs">
                            {{ __('Langsung Tandai Tersalurkan') }}
                        </flux:button>
                    </div>

                    @if($showQuickComplete ?? false)
                        <div class="mt-4 p-4 bg-amber-50 dark:bg-amber-900/20 rounded-xl border border-amber-200 dark:border-amber-800">
                            <p class="text-xs text-amber-700 dark:text-amber-300 mb-3">{{ __('Mode cepat: Langsung tandai sebagai tersalurkan tanpa verifikasi terpisah.') }}</p>
                            
                            <div class="space-y-3">
                                <div class="space-y-2">
                                    <flux:label>{{ __('Bukti Penyaluran (Wajib)') }}</flux:label>
                                    @if ($proof)
                                        <div class="relative aspect-video rounded-xl overflow-hidden border border-zinc-200">
                                            <img src="{{ $proof->temporaryUrl() }}" class="size-full object-cover">
                                        </div>
                                    @endif
                                    <input type="file" wire:model="proof" class="text-xs text-zinc-500" required>
                                </div>
                                
                                <flux:button wire:click="quickComplete" variant="primary" color="green" class="w-full" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="quickComplete">{{ __('Konfirmasi Tersalurkan') }}</span>
                                    <span wire:loading wire:target="quickComplete">{{ __('Memproses...') }}</span>
                                </flux:button>
                            </div>
                        </div>
                    @endif
                @elseif($withdrawal->status === WithdrawalStatus::Verified)
                    <h3 class="font-bold text-zinc-900 dark:text-white">{{ __('Konfirmasi Penyaluran') }}</h3>
                    
                    <div class="space-y-4">
                        <div class="space-y-2">
                            <flux:label>{{ __('Bukti Penyaluran (Foto/BAST)') }}</flux:label>
                            @if ($proof)
                                <div class="relative aspect-video rounded-xl overflow-hidden border border-zinc-200">
                                    <img src="{{ $proof->temporaryUrl() }}" class="size-full object-cover">
                                </div>
                            @endif
                            <input type="file" wire:model="proof" class="text-xs text-zinc-500">
                        </div>

                        <flux:button wire:click="complete" variant="primary" color="green" class="w-full rounded-[16px_0_16px_0]">
                            {{ __('Tandai Tersalurkan') }}
                        </flux:button>
                    </div>
                @else
                    <h3 class="font-bold text-zinc-900 dark:text-white">{{ __('Bukti Penyaluran') }}</h3>
                    @if($withdrawal->proof_image)
                        <img src="{{ $withdrawal->proof_image_url }}" class="w-full rounded-xl border border-zinc-200">
                    @else
                        <p class="text-xs text-zinc-500 italic">{{ __('Tidak ada foto bukti.') }}</p>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
