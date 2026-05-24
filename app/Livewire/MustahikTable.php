<?php

namespace App\Livewire;

use App\Models\Mustahik;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class MustahikTable extends DataTableComponent
{
    protected $model = Mustahik::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setThAttributes(function (Column $column) {
                return ['class' => 'font-mono text-[10px] uppercase tracking-widest text-zinc-400'];
            })
            ->setTableAttributes(['class' => 'min-w-full divide-y divide-zinc-100 dark:divide-zinc-800'])
            ->setSearchBlur();
    }

    public function columns(): array
    {
        return [
            Column::make('Nama', 'name')
                ->sortable()
                ->searchable()
                ->format(fn ($value, $row) => view('components.admin.table.entity-cell', ['name' => $value, 'meta' => 'NIK: '.($row->nik ?? '-')])),

            Column::make('Asnaf', 'asnaf_type')
                ->sortable()
                ->format(fn ($value, $row) => view('components.admin.table.badge-cell', ['label' => $row->asnaf_type->label()])),

            Column::make('Alamat', 'address')
                ->sortable()
                ->searchable()
                ->format(fn ($value) => '<span class="text-xs text-zinc-500 line-clamp-1 italic">"'.($value ?? '-').'"</span>')
                ->html(),

            Column::make('Status', 'is_active')
                ->sortable()
                ->format(fn ($value, $row) => view('components.admin.table.toggle-cell', ['active' => $value, 'id' => $row->id, 'action' => 'toggleStatus'])),

            Column::make('Aksi', 'id')
                ->format(fn ($value, $row) => view('components.admin.table.actions-cell', [
                    'edit' => route('admin.mustahiks.edit', $row),
                    'delete' => $value,
                    'confirm' => 'Hapus data mustahik ini?',
                ])),
        ];
    }

    public function toggleStatus(string $id): void
    {
        $mustahik = Mustahik::findOrFail($id);
        $mustahik->update(['is_active' => ! $mustahik->is_active]);
        $this->dispatch('notify', message: 'Status Mustahik diperbarui.', type: 'success');
    }

    public function delete(string $id): void
    {
        Mustahik::findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Data Mustahik berhasil dihapus.', type: 'success');
    }
}
