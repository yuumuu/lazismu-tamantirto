<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Distributor;

class DistributorTable extends DataTableComponent
{
    protected $model = Distributor::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setThAttributes(function(Column $column) {
                return ['class' => 'font-mono text-[10px] uppercase tracking-widest text-zinc-400'];
            })
            ->setTableAttributes(['class' => 'min-w-full divide-y divide-zinc-100 dark:divide-zinc-800'])
            ->setSearchBlur();
    }

    public function columns(): array
    {
        return [
            Column::make("Nama", "name")
                ->sortable()
                ->searchable()
                ->format(fn($value, $row) => view('components.admin.table.entity-cell', ['name' => $value, 'meta' => $row->phone ?? 'TANPA_KONTAK'])),
            
            Column::make("Tipe", "type")
                ->sortable()
                ->format(fn($value) => view('components.admin.table.badge-cell', ['label' => ucfirst($value)])),

            Column::make("Status", "is_active")
                ->sortable()
                ->format(fn($value, $row) => view('components.admin.table.toggle-cell', ['active' => $value, 'id' => $row->id, 'action' => 'toggleStatus'])),

            Column::make("Aksi", "id")
                ->format(fn($value, $row) => view('components.admin.table.actions-cell', [
                    'edit' => route('admin.distributors.edit', $row),
                    'delete' => $value,
                    'confirm' => 'Hapus data penyalur ini?'
                ])),
        ];
    }

    public function toggleStatus(string $id): void
    {
        $distributor = Distributor::findOrFail($id);
        $distributor->update(['is_active' => !$distributor->is_active]);
        $this->dispatch('notify', message: 'Status Penyalur diperbarui.', type: 'success');
    }

    public function delete(string $id): void
    {
        Distributor::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Data Penyalur berhasil dihapus.', type: 'success');
    }
}
