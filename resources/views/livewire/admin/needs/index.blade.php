<?php

declare(strict_types=1);

use App\Models\Need;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';

    public function approve(Need $need): void
    {
        $need->update(['status' => 'approved']);
        \Flux::toast('Pengajuan disetujui.', variant: 'success');
    }

    public function reject(Need $need): void
    {
        $need->update(['status' => 'rejected']);
        \Flux::toast('Pengajuan ditolak.', variant: 'warning');
    }

    public function with(): array
    {
        $query = Need::withoutGlobalScope('branch')
            ->with('branch')
            ->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('applicant_name', 'like', "%{$this->search}%")
                  ->orWhere('applicant_phone', 'like', "%{$this->search}%")
                  ->orWhere('tracking_token', 'like', "%{$this->search}%");
            });
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        return [
            'needs' => $query->paginate(15),
        ];
    }
}; ?>

@push('head')
    <title>Pengajuan Bantuan - {{ ucwords(config('app.name')) }}</title>
@endpush

<div class="p-3 md:p-6 lg:p-10 space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
        <div class="space-y-2">
            <div class="flex items-center gap-2">
                <div class="h-6 w-1 bg-primary rounded-full"></div>
                <h1 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white">{{ __('Pengajuan Bantuan') }}</h1>
            </div>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm max-w-lg border-l pl-4 border-zinc-200 dark:border-zinc-800">
                {{ __('Kelola pengajuan bantuan dari masyarakat.') }}
            </p>
        </div>
    </div>

    <!-- Filters -->
    <div class="flex flex-col md:flex-row gap-4">
        <div class="flex-1">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari nama, nomor HP, atau token..." icon="magnifying-glass" />
        </div>
        <div class="w-full md:w-48">
            <flux:select wire:model.live="statusFilter" placeholder="Semua Status">
                <option value="">Semua Status</option>
                <option value="pending">Pending</option>
                <option value="approved">Disetujui</option>
                <option value="rejected">Ditolak</option>
            </flux:select>
        </div>
    </div>

    <!-- Table -->
    <div class="premium-card p-0 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-zinc-200 dark:border-zinc-800">
                        <th class="text-left p-4 font-black text-zinc-500 uppercase tracking-widest text-[10px]">Token</th>
                        <th class="text-left p-4 font-black text-zinc-500 uppercase tracking-widest text-[10px]">Pemohon</th>
                        <th class="text-left p-4 font-black text-zinc-500 uppercase tracking-widest text-[10px]">Judul</th>
                        <th class="text-left p-4 font-black text-zinc-500 uppercase tracking-widest text-[10px]">Kategori</th>
                        <th class="text-left p-4 font-black text-zinc-500 uppercase tracking-widest text-[10px]">Nominal</th>
                        <th class="text-left p-4 font-black text-zinc-500 uppercase tracking-widest text-[10px]">Cabang</th>
                        <th class="text-left p-4 font-black text-zinc-500 uppercase tracking-widest text-[10px]">Status</th>
                        <th class="text-left p-4 font-black text-zinc-500 uppercase tracking-widest text-[10px]">Tanggal</th>
                        <th class="text-right p-4 font-black text-zinc-500 uppercase tracking-widest text-[10px]">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($needs as $need)
                        <tr class="border-b border-zinc-100 dark:border-zinc-800 hover:bg-zinc-50 dark:hover:bg-white/5 transition-colors">
                            <td class="p-4">
                                <span class="font-mono font-bold text-xs bg-zinc-100 dark:bg-zinc-800 px-2 py-1 rounded-lg">{{ $need->tracking_token }}</span>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-zinc-900 dark:text-white">{{ $need->applicant_name }}</div>
                                <div class="text-xs text-zinc-500">{{ $need->applicant_phone }}</div>
                            </td>
                            <td class="p-4">
                                <div class="font-medium text-zinc-700 dark:text-zinc-300 max-w-[200px] truncate" title="{{ $need->title }}">{{ $need->title }}</div>
                            </td>
                            <td class="p-4">
                                <span class="text-xs capitalize">{{ $need->category }}</span>
                            </td>
                            <td class="p-4 font-bold">Rp {{ number_format($need->amount_requested, 0, ',', '.') }}</td>
                            <td class="p-4 text-xs text-zinc-500">{{ $need->branch?->name ?? '-' }}</td>
                            <td class="p-4">
                                @if($need->status === 'pending')
                                    <span class="px-3 py-1 rounded-full bg-yellow-100 text-yellow-700 text-[10px] font-black uppercase">Pending</span>
                                @elseif($need->status === 'approved')
                                    <span class="px-3 py-1 rounded-full bg-green-100 text-green-700 text-[10px] font-black uppercase">Disetujui</span>
                                @else
                                    <span class="px-3 py-1 rounded-full bg-red-100 text-red-700 text-[10px] font-black uppercase">Ditolak</span>
                                @endif
                            </td>
                            <td class="p-4 text-xs text-zinc-500">{{ $need->created_at->format('d/m/Y') }}</td>
                            <td class="p-4 text-right">
                                @if($need->status === 'pending')
                                    <div class="flex gap-2 justify-end">
                                        <flux:button wire:click="approve({{ $need->id }})" size="xs" variant="filled" icon="check" class="bg-green-500 hover:bg-green-600">Setuju</flux:button>
                                        <flux:button wire:click="reject({{ $need->id }})" size="xs" variant="filled" icon="x-mark" class="bg-red-500 hover:bg-red-600">Tolak</flux:button>
                                    </div>
                                @else
                                    <span class="text-xs text-zinc-400 italic">{{ $need->status === 'approved' ? 'Sudah disetujui' : 'Ditolak' }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="p-16 text-center text-zinc-500">
                                <div class="flex flex-col items-center gap-4">
                                    <flux:icon name="inbox" class="size-12 text-zinc-300" />
                                    <p class="font-bold">Belum ada pengajuan bantuan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="flex justify-center">
        {{ $needs->links() }}
    </div>
</div>
