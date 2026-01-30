<?php

use App\Models\Withdrawal;
use App\Enums\WithdrawalStatus;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public $search = '';
    public $status = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(string $id): void
    {
        $withdrawal = Withdrawal::findOrFail($id);
        
        if ($withdrawal->status === WithdrawalStatus::Sent) {
            $this->dispatch('notify', message: 'Tidak dapat menghapus dana yang sudah tersalurkan.', type: 'error');
            return;
        }

        $withdrawal->delete();
        $this->dispatch('notify', message: 'Catatan penyaluran berhasil dihapus.', type: 'success');
    }

    public function with(): array
    {
        return [
            'withdrawals' => Withdrawal::query()
                ->with(['campaign', 'mustahik', 'distributor'])
                ->when($this->search, function ($q) {
                    $q->where('description', 'like', '%' . $this->search . '%')
                      ->orWhereHas('mustahik', fn($mq) => $mq->where('name', 'like', '%' . $this->search . '%'));
                })
                ->when($this->status, fn($q) => $q->where('status', $this->status))
                ->latest()
                ->paginate(15),
            'statuses' => WithdrawalStatus::cases(),
        ];
    }
} ?>

<div class="p-3 md:p-6 lg:p-10 space-y-6 md:space-y-8">
    <!-- Header -->
    <x-admin.page-header 
        title="Penyaluran Dana"
        description="Pantau penyaluran dana zakat, infaq, dan sedekah."
    >
        <x-slot:action>
            <flux:button variant="primary" icon="plus" :href="route('admin.withdrawals.create')" wire:navigate>
                {{ __('Catat Penyaluran') }}
            </flux:button>
        </x-slot:action>
    </x-admin.page-header>

    <!-- Toolbar -->
    <div class="flex flex-col md:flex-row gap-4 items-center">
        <div class="w-full md:w-1/3">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari penerima atau deskripsi..." icon="magnifying-glass" />
        </div>
        <div class="w-full md:w-1/4">
            <flux:select wire:model.live="status" placeholder="Semua Status">
                <option value="">{{ __('Semua Status') }}</option>
                @foreach($statuses as $s)
                    <option value="{{ $s->value }}">{{ $s->label() }}</option>
                @endforeach
            </flux:select>
        </div>
    </div>

    <!-- Table -->
    <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-xs">
        <table class="w-full text-sm text-left">
            <thead class="bg-zinc-50 dark:bg-zinc-900/50 text-zinc-500 font-medium border-b border-zinc-200 dark:border-zinc-800">
                <tr>
                    <th class="px-6 py-3">{{ __('ID / Tanggal') }}</th>
                    <th class="px-6 py-3">{{ __('Penerima (Mustahik)') }}</th>
                    <th class="px-6 py-3">{{ __('Campaign / Sumber') }}</th>
                    <th class="px-6 py-3">{{ __('Jumlah') }}</th>
                    <th class="px-6 py-3">{{ __('Status') }}</th>
                    <th class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                @forelse($withdrawals as $w)
                <tr class="group hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-mono text-xs font-bold text-zinc-900 dark:text-white">{{ $w->id }}</span>
                            <span class="text-[10px] text-zinc-500">{{ $w->date->format('d M Y') }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-medium text-zinc-900 dark:text-white">{{ $w->mustahik?->name ?? 'Fisabilillah / Umum' }}</span>
                            <span class="text-[10px] text-zinc-500">Dist: {{ $w->distributor?->name ?? '-' }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-zinc-600 dark:text-zinc-400 line-clamp-1">{{ $w->campaign?->title ?? 'Dana Umum' }}</span>
                    </td>
                    <td class="px-6 py-4 font-semibold text-red-600 dark:text-red-400">
                        - {{ $w->formatted_amount }}
                    </td>
                    <td class="px-6 py-4">
                        <flux:badge :color="$w->status->color()" inset="top">
                            {{ $w->status->label() }}
                        </flux:badge>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2">
                            <flux:button variant="ghost" size="sm" icon="eye" :href="route('admin.withdrawals.show', $w)" wire:navigate />
                            
                            @if($w->status === \App\Enums\WithdrawalStatus::Draft)
                                <flux:tooltip content="Verifikasi Cepat">
                                    <flux:button variant="ghost" size="sm" icon="shield-check" wire:click="quickVerify('{{ $w->id }}')" class="text-blue-500" />
                                </flux:tooltip>
                            @endif
                            
                            @if(!$w->status->isFinal())
                                <flux:button variant="ghost" size="sm" icon="trash" wire:click="delete('{{ $w->id }}')" wire:confirm="Hapus catatan penyaluran ini?" class="text-red-500" />
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-zinc-500">{{ __('Belum ada catatan penyaluran.') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="p-6 border-t border-zinc-100 dark:border-zinc-800">
            {{ $withdrawals->links() }}
        </div>
    </div>
</div>
