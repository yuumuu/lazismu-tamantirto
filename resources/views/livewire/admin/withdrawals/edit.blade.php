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

    public Withdrawal $withdrawal;
    
    public $campaign_id;
    public $mustahik_id;
    public $distributor_id;
    public $amount;
    public $date;
    public $description;
    public $proof_image;

    public function mount(Withdrawal $withdrawal): void
    {
        $this->withdrawal = $withdrawal;

        if ($withdrawal->status !== WithdrawalStatus::Draft) {
            $this->dispatch('notify', message: 'Hanya data draft yang dapat diubah.', type: 'error');
            $this->redirect(route('admin.withdrawals.show', $withdrawal), navigate: true);
            return;
        }

        $this->campaign_id = $withdrawal->campaign_id;
        $this->mustahik_id = $withdrawal->mustahik_id;
        $this->distributor_id = $withdrawal->distributor_id;
        $this->amount = (float) $withdrawal->amount;
        $this->date = $withdrawal->date->format('Y-m-d');
        $this->description = $withdrawal->description;
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
        ];

        if ($this->proof_image) {
            $data['proof_image'] = $this->proof_image->store('withdrawals', 'public');
        }

        $this->withdrawal->update($data);

        $this->dispatch('notify', message: 'Data penyaluran berhasil diperbarui.', type: 'success');
        $this->redirect(route('admin.withdrawals.index'), navigate: true);
    }

    public function with(): array
    {
        return [
            'campaigns' => Campaign::active()->get(),
            'mustahiks' => Mustahik::active()->get(),
            'distributors' => Distributor::active()->get(),
        ];
    }
} ?>

<div class="p-3 md:p-6 lg:p-10 space-y-8">
    <div class="flex items-center gap-4">
        <flux:button variant="ghost" icon="arrow-left" :href="route('admin.withdrawals.index')" wire:navigate />
        <div>
            <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">{{ __('Edit Penyaluran Dana') }}</h1>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm mt-1">{{ __('Ubah informasi draft penyaluran.') }}</p>
        </div>
    </div>

    <form wire:submit="save" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white dark:bg-zinc-900 p-8 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                <!-- Financials -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <flux:input wire:model="amount" type="number" label="Jumlah Penyaluran (Rp)" prefix="Rp" />
                    <flux:input wire:model="date" type="date" label="Tanggal Penyaluran" />
                </div>

                <!-- Description -->
                <div class="space-y-4">
                    <flux:textarea wire:model="description" label="Keterangan / Tujuan" rows="4" />
                </div>
            </div>

            <!-- Proof of Distribution -->
            <div class="bg-white dark:bg-zinc-900 p-6 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm">
                <h3 class="font-bold text-zinc-900 dark:text-white mb-4">{{ __('Bukti Penyaluran') }}</h3>
                
                <div class="space-y-4">
                    @if ($proof_image)
                        <div class="relative w-full aspect-video rounded-2xl overflow-hidden border border-zinc-200">
                            <img src="{{ $proof_image->temporaryUrl() }}" class="size-full object-cover">
                            <button type="button" wire:click="$set('proof_image', null)" class="absolute top-2 right-2 p-1.5 bg-red-500 text-white rounded-full">
                                <flux:icon name="x-mark" class="size-4" />
                            </button>
                        </div>
                    @elseif($withdrawal->proof_image)
                         <div class="relative w-full aspect-video rounded-2xl overflow-hidden border border-zinc-200 group">
                            <img src="{{ $withdrawal->proof_image_url }}" class="size-full object-cover">
                            <label class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center cursor-pointer text-white">
                                <flux:icon name="camera" class="size-6 mr-2" />
                                <span>Ganti Gambar</span>
                                <input type="file" wire:model="proof_image" class="hidden">
                            </label>
                        </div>
                    @else
                        <label class="flex flex-col items-center justify-center w-full aspect-video rounded-2xl border-2 border-dashed border-zinc-200 dark:border-zinc-800 hover:border-amber-500 transition-colors cursor-pointer bg-zinc-50 dark:bg-zinc-950">
                            <flux:icon name="cloud-arrow-up" class="size-10 text-zinc-400 mb-2" />
                            <span class="text-sm text-zinc-500">{{ __('Klik untuk upload bukti') }}</span>
                            <input type="file" wire:model="proof_image" class="hidden" accept="image/*,application/pdf">
                        </label>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Selection -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-zinc-900 p-6 rounded-[24px_0_24px_0] border border-zinc-200 dark:border-zinc-800 shadow-sm space-y-6">
                <div class="space-y-4">
                    <flux:select wire:model="mustahik_id" label="Penerima (Mustahik)">
                        <option value="">-- Umum / Tanpa Nama --</option>
                        @foreach($mustahiks as $m)
                            <option value="{{ $m->id }}">{{ $m->name }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model="campaign_id" label="Program / Campaign">
                        <option value="">-- Dana Umum --</option>
                        @foreach($campaigns as $c)
                            <option value="{{ $c->id }}">{{ $c->title }}</option>
                        @endforeach
                    </flux:select>

                    <flux:select wire:model="distributor_id" label="Penyalur (Distributor)">
                        <option value="">-- Petugas Internal --</option>
                        @foreach($distributors as $d)
                            <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </flux:select>
                </div>

                <div class="pt-4">
                    <flux:button type="submit" variant="primary" class="w-full" icon="check">
                        {{ __('Simpan Perubahan') }}
                    </flux:button>
                </div>
            </div>
        </div>
    </form>
</div>
