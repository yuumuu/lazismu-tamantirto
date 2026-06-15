<?php

use App\Models\Donation;
use App\Models\Withdrawal;
use App\Models\FinancialReport;
use App\Enums\WithdrawalStatus;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public $startDate;
    public $endDate;

    // Report Management State
    public $showReportModal = false;
    public $editingReportId = null;
    public $reportForm = [
        'title' => '',
        'year' => '',
        'type' => 'annual',
        'is_published' => true,
        'description' => '',
    ];
    public $reportFile;
    public $reportCover;

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->endOfMonth()->format('Y-m-d');
        $this->reportForm['year'] = date('Y');
    }

    public function with(): array
    {
        $donations = Donation::query()
            ->verified()
            ->whereBetween('verified_at', [$this->startDate . ' 00:00:00', $this->endDate . ' 23:59:59'])
            ->get();

        $withdrawals = Withdrawal::query()
            ->where('status', WithdrawalStatus::Sent)
            ->whereBetween('date', [$this->startDate, $this->endDate])
            ->get();

        // Income and outcome strictly for the selected period
        $periodIncome = $donations->sum('amount');
        $periodOutcome = $withdrawals->sum('amount');

        // Cumulative totals up to end date to calculate true ending balance 
        $cumulativeIncome = Donation::query()->verified()
            ->where('verified_at', '<=', $this->endDate . ' 23:59:59')->sum('amount');
        $cumulativeOutcome = Withdrawal::query()->where('status', WithdrawalStatus::Sent)
            ->where('date', '<=', $this->endDate)->sum('amount');

        return [
            'totalIncome' => $periodIncome,
            'totalOutcome' => $periodOutcome,
            'balance' => $cumulativeIncome - $cumulativeOutcome,
            'incomeCount' => $donations->count(),
            'outcomeCount' => $withdrawals->count(),
            'incomeBySource' => $donations->groupBy('donation_type')->map->sum('amount'),
            'financialReports' => FinancialReport::latest()->get(),
            'recentActivity' => collect()
                ->concat($donations->map(fn($d) => ['type' => 'income', 'label' => 'Donasi: ' . $d->donor_name, 'amount' => $d->amount, 'date' => $d->verified_at]))
                ->concat($withdrawals->map(fn($w) => ['type' => 'outcome', 'label' => 'Penyaluran: ' . ($w->mustahik?->name ?? 'Umum'), 'amount' => $w->amount, 'date' => $w->date]))
                ->sortByDesc('date')
                ->take(10),
        ];
    }

    public function openCreateModal(): void
    {
        $this->reset(['editingReportId', 'reportFile', 'reportCover']);
        $this->reportForm = [
            'title' => '',
            'year' => date('Y'),
            'type' => 'annual',
            'is_published' => true,
            'description' => '',
        ];
        $this->showReportModal = true;
    }

    public function editReport(FinancialReport $report): void
    {
        $this->editingReportId = $report->id;
        $this->reportForm = [
            'title' => $report->title,
            'year' => $report->year,
            'type' => $report->type,
            'is_published' => $report->is_published,
            'description' => $report->description,
        ];
        $this->reset(['reportFile', 'reportCover']);
        $this->showReportModal = true;
    }

    public function saveReport(): void
    {
        $rules = [
            'reportForm.title' => 'required|string|max:255',
            'reportForm.year' => 'required|integer',
            'reportForm.type' => 'required|string',
            'reportForm.description' => 'nullable|string',
        ];

        if (!$this->editingReportId) {
            $rules['reportFile'] = 'required|file|mimes:pdf|max:10240';
        }

        $this->validate($rules);

        $data = $this->reportForm;

        if ($this->reportFile) {
            $data['file_path'] = $this->reportFile->store('reports', 'public');
        }

        if ($this->reportCover) {
            $data['cover_image'] = $this->reportCover->store('report-covers', 'public');
        }

        if ($this->editingReportId) {
            FinancialReport::find($this->editingReportId)->update($data);
            $message = 'Laporan berhasil diperbarui.';
        } else {
            FinancialReport::create($data);
            $message = 'Laporan berhasil ditambahkan.';
        }

        $this->showReportModal = false;
        $this->dispatch('notify', message: $message, type: 'success');
    }

    public function deleteReport(string $id): void
    {
        FinancialReport::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Laporan berhasil dihapus.', type: 'success');
    }
}; ?>

@push('head')
    <title>Laporan Keuangan - {{ ucwords(config('app.name')) }}</title>
@endpush

<div class="p-3 md:p-6 lg:p-10 space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
        <div class="space-y-2">
            <div class="flex items-center gap-2">
                <div class="h-6 w-1 bg-primary rounded-full"></div>
                <h1 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white">{{ __('Laporan Keuangan') }}</h1>
            </div>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm max-w-lg border-l pl-4 border-zinc-200 dark:border-zinc-800">
                {{ __('Ringkasan arus kas donasi masuk dan penyaluran.') }}
            </p>
        </div>
    </div>

    <!-- Period Filter -->
    <div class="flex flex-wrap items-end gap-4">
        <flux:input wire:model.live="startDate" type="date" label="Dari Tanggal" />
        <flux:input wire:model.live="endDate" type="date" label="Sampai Tanggal" />
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="premium-card p-6">
            <p class="text-xs font-bold text-zinc-400 uppercase tracking-widest">{{ __('Donasi Masuk (Periode)') }}</p>
            <h2 class="text-2xl font-black text-green-600 mt-2">{{ format_rupiah($totalIncome) }}</h2>
            <p class="text-[10px] text-zinc-500 mt-1">{{ $incomeCount }} {{ __('transaksi terverifikasi') }}</p>
        </div>

        <div class="premium-card p-6">
            <p class="text-xs font-bold text-zinc-400 uppercase tracking-widest">{{ __('Penyaluran (Periode)') }}</p>
            <h2 class="text-2xl font-black text-red-600 mt-2">{{ format_rupiah($totalOutcome) }}</h2>
            <p class="text-[10px] text-zinc-500 mt-1">{{ $outcomeCount }} {{ __('penyaluran selesai') }}</p>
        </div>

        <div class="premium-card p-6">
            <p class="text-xs font-bold text-zinc-400 uppercase tracking-widest">{{ __('Saldo Akhir Periode') }}</p>
            <h2 class="text-2xl font-black text-primary mt-2">{{ format_rupiah($balance) }}</h2>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Income by Type -->
        <div class="premium-card p-6">
            <h3 class="font-bold text-zinc-900 dark:text-white mb-6">{{ __('Donasi Berdasarkan Jenis') }}</h3>
            <div class="space-y-4">
                @foreach($incomeBySource as $type => $amount)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ is_string($type) ? ucfirst($type) : $type->label() }}</span>
                        <div class="flex items-center gap-3">
                            <div class="h-2 w-32 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden">
                                <div class="h-full bg-amber-500" style="width: {{ $totalIncome > 0 ? ($amount / $totalIncome * 100) : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-bold text-zinc-900 dark:text-white">{{ format_rupiah($amount) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="premium-card p-6">
            <h3 class="font-bold text-zinc-900 dark:text-white mb-6">{{ __('Aktivitas Penyaluran & Donasi Terakhir') }}</h3>
            <div class="space-y-6">
                @foreach($recentActivity as $act)
                    <div class="flex gap-4">
                        <div class="size-10 rounded-xl flex items-center justify-center flex-shrink-0 {{ $act['type'] === 'income' ? 'bg-green-100 dark:bg-green-900/30 text-green-600' : 'bg-red-100 dark:bg-red-900/30 text-red-600' }}">
                            <flux:icon :name="$act['type'] === 'income' ? 'arrow-down-tray' : 'arrow-up-tray'" class="size-5" />
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-medium text-zinc-900 dark:text-white">{{ $act['label'] }}</p>
                                <p class="text-sm font-bold {{ $act['type'] === 'income' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $act['type'] === 'income' ? '+' : '-' }} {{ format_rupiah($act['amount']) }}
                                </p>
                            </div>
                            <p class="text-[10px] text-zinc-500 mt-1">{{ Carbon\Carbon::parse($act['date'])->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Financial Reports Section -->
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-black text-zinc-900 dark:text-white tracking-tight">{{ __('Arsip Laporan Keuangan Tahunan/Berkala') }}</h2>
            <flux:button variant="primary" icon="plus" wire:click="openCreateModal">
                {{ __('Tambah Laporan') }}
            </flux:button>
        </div>

        <div class="premium-card overflow-hidden">
            <flux:table>
                <flux:table.columns>
                    <flux:table.column>{{ __('Laporan') }}</flux:table.column>
                    <flux:table.column>{{ __('Tahun') }}</flux:table.column>
                    <flux:table.column>{{ __('Tipe') }}</flux:table.column>
                    <flux:table.column>{{ __('Status') }}</flux:table.column>
                    <flux:table.column align="right"></flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse($financialReports as $report)
                        <flux:table.row :key="$report->id">
                            <flux:table.cell>
                                <div class="flex items-center gap-3">
                                    <div class="size-10 rounded bg-zinc-100 flex items-center justify-center text-zinc-400">
                                        <flux:icon name="document-text" variant="outline" />
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-zinc-900 dark:text-white">{{ $report->title }}</span>
                                        <a href="{{ $report->file_url }}" target="_blank" class="text-[10px] text-primary hover:underline">{{ __('Lihat Berkas PDF') }}</a>
                                    </div>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="font-medium">{{ $report->year }}</flux:table.cell>
                            <flux:table.cell>
                                <flux:badge variant="neutral" size="sm" class="uppercase text-[10px]">{{ $report->type }}</flux:badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($report->is_published)
                                    <flux:badge variant="success" size="sm">{{ __('Publik') }}</flux:badge>
                                @else
                                    <flux:badge variant="neutral" size="sm">{{ __('Draft') }}</flux:badge>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell align="right">
                                <div class="flex justify-end gap-2">
                                    <flux:button variant="ghost" size="sm" icon="pencil-square" wire:click="editReport({{ $report->id }})" />
                                    <flux:button variant="ghost" size="sm" icon="trash" color="danger" wire:click="deleteReport({{ $report->id }})" wire:confirm="Hapus laporan ini?" />
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="text-center py-10 text-zinc-500 italic">
                                {{ __('Belum ada laporan terunggah.') }}
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>
    </div>

    <!-- Report Modal -->
    <flux:modal wire:model="showReportModal" class="md:w-[600px] space-y-6">
        <div>
            <flux:heading >{{ $editingReportId ? __('Edit Laporan') : __('Tambah Laporan Baru') }}</flux:heading>
            <flux:subheading>{{ __('Masukkan detail laporan keuangan untuk dipublikasikan.') }}</flux:subheading>
        </div>

        <form wire:submit="saveReport" class="space-y-6">
            <flux:input wire:model="reportForm.title" label="Judul Laporan" placeholder="Contoh: Laporan Tahunan 2024" />
            
            <div class="grid grid-cols-2 gap-4">
                <flux:input wire:model="reportForm.year" type="number" label="Tahun" />
                <flux:select wire:model="reportForm.type" label="Tipe Laporan">
                    <option value="annual">Tahunan</option>
                    <option value="quarterly">Triwulan</option>
                    <option value="monthly">Bulanan</option>
                </flux:select>
            </div>

            <flux:textarea wire:model="reportForm.description" label="Deskripsi Singkat (Opsional)" />

            <div class="space-y-2">
                <flux:label>{{ __('Berkas PDF Laporan') }}</flux:label>
                <input type="file" accept="image/png, image/jpg, image/jpeg, .pdf" wire:model="reportFile" class="w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition-all" />
                @if($editingReportId && !$reportFile)
                    <p class="text-[10px] text-zinc-400 font-bold italic">{{ __('Biarkan kosong jika tidak ingin mengubah file.') }}</p>
                @endif
                <error for="reportFile" class="text-xs text-red-500" />
            </div>

            <div class="space-y-2">
                <flux:label>{{ __('Gambar Sampul (Opsional)') }}</flux:label>
                <input type="file" accept="image/png, image/jpg, image/jpeg" wire:model="reportCover" class="w-full text-sm text-zinc-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-zinc-100 file:text-zinc-600 hover:file:bg-zinc-200 transition-all" />
                <error for="reportCover" class="text-xs text-red-500" />
            </div>

            <flux:switch wire:model="reportForm.is_published" label="Publikasikan ke Publik" />

            <div class="flex justify-end gap-3">
                <flux:button variant="ghost" wire:click="$set('showReportModal', false)">{{ __('Batal') }}</flux:button>
                <flux:button type="submit" variant="primary">{{ __('Simpan Laporan') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</div>
