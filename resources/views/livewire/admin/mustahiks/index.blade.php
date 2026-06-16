<?php

use App\Imports\MustahikImport;
use App\Models\Mustahik;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

new class extends Component {
    use WithFileUploads, WithPagination;

    public string $search = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    public $importFile;
    public int $importCount = 0;
    public int $importErrorCount = 0;
    public array $importErrors = [];
    public bool $importing = false;

    public function with(): array
    {
        return [
            'mustahiks' => Mustahik::query()
                ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('nik', 'like', '%' . $this->search . '%')
                    ->orWhere('family_card_number', 'like', '%' . $this->search . '%'))
                ->orderBy($this->sortField, $this->sortDirection)
                ->paginate(10),
        ];
    }

    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function delete(Mustahik $mustahik): void
    {
        $mustahik->delete();
        $this->dispatch('notify', message: 'Data Mustahik berhasil dihapus.', type: 'success');
    }

    public function toggleStatus(Mustahik $mustahik): void
    {
        $mustahik->update(['is_active' => !$mustahik->is_active]);
        $this->dispatch('notify', message: 'Status Mustahik diperbarui.', type: 'success');
    }

    public function openImportModal(): void
    {
        $this->reset(['importFile', 'importCount', 'importErrorCount', 'importErrors', 'importing']);
        $this->js('$flux.modal("import-mustahik-modal").show()');
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
            $import = new MustahikImport;
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
}; ?>

@push('head')
    <title>Arsip Mustahik - {{ ucwords(config('app.name')) }}</title>
@endpush

<div class="p-3 md:p-6 lg:p-10 space-y-8">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
        <div class="space-y-2">
             <div class="flex items-center gap-2">
                <div class="h-6 w-1 bg-primary rounded-full"></div>
                <h1 class="text-2xl font-black tracking-tight text-zinc-900 dark:text-white">{{ __('Arsip Mustahik') }}</h1>
            </div>
            <p class="text-zinc-500 dark:text-zinc-400 text-sm max-w-lg border-l pl-4 border-zinc-200 dark:border-zinc-800">
                {{ __('Database penerima manfaat terklasifikasi berdasarkan asnaf.') }}
            </p>
        </div>
        <div class="flex items-center gap-2">
            <flux:button icon="document-arrow-up" variant="subtle" wire:click="openImportModal">
                {{ __('Import CSV') }}
            </flux:button>
            <flux:button variant="primary" icon="plus" :href="route('admin.mustahiks.create')" wire:navigate>
                {{ __('Tambah Mustahik') }}
            </flux:button>
        </div>
    </div>

    <!-- Toolbar -->
    <div class="flex flex-col sm:flex-row gap-4 justify-between items-center">
        <div class="w-full sm:w-72">
            <flux:input icon="magnifying-glass" wire:model.live.debounce.300ms="search" placeholder="Cari nama, NIK, atau KK..." />
        </div>
        <div class="flex items-center gap-2">
             <flux:button icon="funnel" variant="subtle" size="sm">{{ __('Filter') }}</flux:button>
        </div>
    </div>

    <!-- Table -->
    <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-xs">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-zinc-50 dark:bg-zinc-900/50 text-zinc-500 font-medium border-b border-zinc-200 dark:border-zinc-800">
                    <tr>
                        <th class="px-6 py-3 cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-300 transition-colors" wire:click="sortBy('name')">
                            <div class="flex items-center gap-1">
                                {{ __('Nama & Identitas') }}
                                <flux:icon name="chevron-up-down" class="size-3 opacity-50" />
                            </div>
                        </th>
                        <th class="px-6 py-3 cursor-pointer hover:text-zinc-700 dark:hover:text-zinc-300 transition-colors" wire:click="sortBy('asnaf_type')">
                            <div class="flex items-center gap-1">
                                {{ __('Asnaf') }}
                                <flux:icon name="chevron-up-down" class="size-3 opacity-50" />
                            </div>
                        </th>
                        <th class="px-6 py-3">{{ __('Wilayah/Alamat') }}</th>
                        <th class="px-6 py-3 text-center">{{ __('Status') }}</th>
                        <th class="px-6 py-3 text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800 bg-white dark:bg-zinc-900">
                    @forelse($mustahiks as $mustahik)
                        <tr class="group hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors" wire:key="{{ $mustahik->id }}">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="font-bold text-zinc-900 dark:text-white">{{ $mustahik->name }}</span>
                                    <div class="flex items-center gap-2 text-xs text-zinc-500 font-mono mt-1">
                                        <span class="bg-zinc-100 dark:bg-zinc-800 px-1 rounded">NIK: {{ $mustahik->nik ?? '-' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <flux:badge size="sm" color="zinc" inset="top bottom">{{ $mustahik->asnaf_type?->label() ?? '-' }}</flux:badge>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-zinc-600 dark:text-zinc-400 text-sm line-clamp-2 max-w-[200px]">{{ $mustahik->address ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <flux:switch wire:click="toggleStatus('{{ $mustahik->id }}')" :checked="$mustahik->is_active" />
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <flux:button icon="pencil-square" size="xs" variant="ghost" :href="route('admin.mustahiks.edit', $mustahik)" wire:navigate />
                                    <flux:button icon="trash" size="xs" variant="ghost" class="text-red-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20" wire:click="delete('{{ $mustahik->id }}')" wire:confirm="Yakin ingin menghapus data ini?" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <flux:icon name="magnifying-glass" class="size-8 text-zinc-300" />
                                    <p class="text-zinc-500 text-sm font-medium">{{ __('Tidak ada data ditemukan.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 p-4">
            {{ $mustahiks->links() }}
        </div>
    </div>

    <!-- Import Modal -->
    <flux:modal name="import-mustahik-modal" class="max-w-lg">
        <form wire:submit="import" class="space-y-6">
            <div>
                <flux:heading>Import Mustahik (CSV/Excel)</flux:heading>
                <flux:subheading>
                    Upload file CSV atau Excel dengan kolom: <strong>nama</strong>, <strong>alamat</strong>, <strong>telepon</strong>/<strong>phone</strong>, <strong>asnaf</strong>, <strong>nik</strong>, <strong>no_kk</strong>, <strong>pekerjaan</strong>, <strong>penghasilan</strong>, <strong>domisili</strong>, <strong>catatan</strong>.
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



