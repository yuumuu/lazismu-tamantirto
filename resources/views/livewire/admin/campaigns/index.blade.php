<?php

use App\Enums\CampaignStatus;
use App\Imports\CampaignImport;
use App\Models\Campaign;
use App\Models\CampaignCategory;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

new class extends Component {
    use WithFileUploads, WithPagination;

    public $search = '';
    public $status = '';
    public $category = '';

    public $importFile;
    public int $importCount = 0;
    public int $importErrorCount = 0;
    public array $importErrors = [];
    public bool $importing = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'category' => ['except' => ''],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function delete(string $id): void
    {
        Campaign::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Campaign berhasil dihapus.', type: 'success');
    }

    public function with(): array
    {
        return [
            'campaigns' => Campaign::query()
                ->with(['category', 'creator'])
                ->withSum('verifiedDonations', 'amount')
                ->when($this->search, fn($q) => $q->where('title', 'like', '%' . $this->search . '%'))
                ->when($this->status, fn($q) => $q->where('status', $this->status))
                ->when($this->category, fn($q) => $q->where('category_id', $this->category))
                ->latest()
                ->paginate(10),
            'categories' => CampaignCategory::ordered()->get(),
            'statuses' => CampaignStatus::cases(),
        ];
    }

    public function openImportModal(): void
    {
        $this->reset(['importFile', 'importCount', 'importErrorCount', 'importErrors', 'importing']);
        $this->js('$flux.modal("import-campaign-modal").show()');
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
            $import = new CampaignImport;
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
} ?>

<div class="p-3 md:p-6 lg:p-10 space-y-6 md:space-y-8">
    <!-- Header -->
    <x-admin.page-header
        title="Program Campaign"
        description="Manajemen penggalangan dana terpadu untuk penyaluran zakat, infaq, dan sedekah."
    >
        <x-slot:action>
            <div class="flex items-center gap-2">
                <flux:button icon="document-arrow-up" variant="subtle" wire:click="openImportModal">
                    {{ __('Import CSV') }}
                </flux:button>
                <flux:button variant="primary" icon="plus" :href="route('admin.campaigns.create')" wire:navigate data-tour="campaign-create">
                    {{ __('Buat Campaign') }}
                </flux:button>
            </div>
        </x-slot:action>
    </x-admin.page-header>

    <!-- Toolbar -->
    <div class="flex flex-col md:flex-row gap-4 items-center" data-tour="campaign-filters">
        <div class="w-full md:w-1/3">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Cari judul campaign..." icon="magnifying-glass" />
        </div>
        <div class="w-full md:w-1/4">
            <flux:select wire:model.live="category" placeholder="Semua Kategori">
                <option value="">{{ __('Semua Kategori') }}</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
            </flux:select>
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
    <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-xs" data-tour="campaign-table">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-zinc-50 dark:bg-zinc-900/50 text-zinc-500 font-medium border-b border-zinc-200 dark:border-zinc-800">
                    <tr>
                        <th class="px-6 py-3">{{ __('Judul Program') }}</th>
                        <th class="px-6 py-3">{{ __('Target') }}</th>
                        <th class="px-6 py-3">{{ __('Terkumpul') }}</th>
                        <th class="px-6 py-3 text-center">{{ __('Verifikasi') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                    @forelse($campaigns as $campaign)
                        <tr class="group hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors" wire:key="{{ $campaign->id }}">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="size-10 rounded-lg bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-zinc-400 font-black text-xs group-hover:bg-primary/10 group-hover:text-primary transition-colors">
                                        {{ substr($campaign->title, 0, 1) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="font-bold text-zinc-900 dark:text-white line-clamp-1 max-w-[250px]">{{ $campaign->title }}</span>
                                        <span class="text-xs text-zinc-500">{{ $campaign->category?->name ?? 'Umum' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-mono text-zinc-600 dark:text-zinc-400 font-medium">{{ format_rupiah($campaign->target_amount) }}</span>
                            </td>
                            <td class="px-6 py-4 w-64">
                                <div class="space-y-1">
                                    <div class="flex justify-between text-xs font-bold text-zinc-900 dark:text-white">
                                        <span>{{ format_rupiah($campaign->current_amount) }}</span>
                                        <span class="text-primary">{{ $campaign->progress_percentage }}%</span>
                                    </div>
                                    <div class="w-full bg-zinc-100 dark:bg-zinc-800 rounded-full h-1.5 overflow-hidden">
                                        <div class="bg-primary h-full rounded-full transition-all duration-500" style="width: {{ $campaign->progress_percentage }}%"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <flux:badge size="sm" :color="$campaign->status->color()" inset="top bottom">{{ $campaign->status->label() }}</flux:badge>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button icon="pencil-square" size="xs" variant="ghost" :href="route('admin.campaigns.edit', $campaign)" wire:navigate />
                                    <flux:button icon="trash" size="xs" variant="ghost" class="text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20" wire:click="delete('{{ $campaign->id }}')" wire:confirm="Yakin ingin menghapus campaign ini?" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <flux:icon name="magnifying-glass" class="size-8 text-zinc-300" />
                                    <p class="text-zinc-500 text-sm font-medium">{{ __('Tidak ada campaign ditemukan.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 p-4">
            {{ $campaigns->links() }}
        </div>
    </div>

    <!-- Import Modal -->
    <flux:modal name="import-campaign-modal" class="max-w-lg">
        <form wire:submit="import" class="space-y-6">
            <div>
                <flux:heading>Import Campaign (CSV/Excel)</flux:heading>
                <flux:subheading>
                    Upload file CSV atau Excel dengan kolom: <strong>judul</strong>/<strong>title</strong>, <strong>deskripsi</strong>, <strong>target</strong>, <strong>tipe</strong>, <strong>status</strong>, <strong>unggulan</strong>, <strong>prioritas</strong>, <strong>tanggal_mulai</strong>, <strong>tanggal_selesai</strong>.
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
